<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\Account;
use App\Models\Journal;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosManager extends Component
{
    public $searchProduct = '';
    public $cart = [];
    public $customerId = '';
    public $taxRate = 11; // 11% standard VAT in ID

    // Payment fields
    public $cashPaid = 0;
    public $changeDue = 0;
    public $paymentMethod = 'cash'; // cash, bank

    public function addToCart($productId)
    {
        $product = Product::query()->findOrFail($productId);

        // Check if product stock is available in stock_items
        $totalStock = StockItem::query()->where('product_id', $product->id)->sum('qty_on_hand');
        
        if ($totalStock <= 0) {
            session()->flash('error', "Product '{$product->name}' is out of stock!");
            return;
        }

        // Check if item is already in cart
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] + 1 > $totalStock) {
                session()->flash('error', "Cannot add more. Only {$totalStock} items available in stock.");
                return;
            }
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
            ];
        }

        $this->calculateTotals();
    }

    public function updateQty($productId, $qty)
    {
        $qty = (int) $qty;
        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::query()->findOrFail($productId);
        $totalStock = StockItem::query()->where('product_id', $product->id)->sum('qty_on_hand');

        if ($qty > $totalStock) {
            session()->flash('error', "Only {$totalStock} units available for '{$product->name}'.");
            $this->cart[$productId]['qty'] = $totalStock;
        } else {
            $this->cart[$productId]['qty'] = $qty;
        }

        $this->calculateTotals();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $this->cashPaid = max($this->cashPaid, $subtotal);
        $this->changeDue = max(0, $this->cashPaid - $subtotal);
    }

    public function updatedCashPaid()
    {
        $this->calculateTotals();
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Cart is empty. Select products first.');
            return;
        }

        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        if ($this->cashPaid < $subtotal) {
            session()->flash('error', 'Payment amount must be equal to or higher than total cost.');
            return;
        }

        DB::transaction(function () use ($subtotal) {
            // Find or create default walk-in customer if none selected
            $cust = $this->customerId ? Customer::query()->find($this->customerId) : null;
            if (!$cust) {
                $cust = Customer::query()->firstOrCreate(
                    ['email' => 'walkin@erp.com'],
                    [
                        'name' => 'Walk-in Customer',
                        'company_name' => 'Individual RETAIL',
                        'phone' => '-',
                        'address' => 'Walk-in POS client',
                    ]
                );
            }

            // Create Invoice Sale
            $invNum = 'INV-' . now()->format('Ymd') . '-' . sprintf('%04d', Sale::query()->count() + 1);
            $sale = Sale::query()->create([
                'invoice_number' => $invNum,
                'customer_id' => $cust->id,
                'sale_date' => now()->format('Y-m-d'),
                'due_date' => now()->format('Y-m-d'),
                'total_amount' => $subtotal,
                'tax_amount' => 0,
                'grand_total' => $subtotal,
                'status' => 'delivered',
                'notes' => 'Point of Sale transaction. Paid via ' . strtoupper($this->paymentMethod),
            ]);

            // Track HPP / Cost of Goods Sold total
            $totalCOGS = 0;

            foreach ($this->cart as $item) {
                // Create item
                SaleItem::query()->create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['price'] * $item['qty'],
                ]);

                // Reduce inventory stock from the first available warehouse location that has stock
                $qtyToReduce = $item['qty'];
                $stockRecords = StockItem::query()
                    ->where('product_id', $item['id'])
                    ->where('qty_on_hand', '>', 0)
                    ->get();

                foreach ($stockRecords as $stock) {
                    if ($qtyToReduce <= 0) break;

                    $reduction = min($qtyToReduce, $stock->qty_on_hand);
                    $stock->decrement('qty_on_hand', $reduction);
                    $qtyToReduce -= $reduction;

                    // Log inventory change
                    Inventory::query()->create([
                        'product_id' => $item['id'],
                        'quantity' => -$reduction,
                        'type' => 'out',
                        'reference_type' => 'Sale',
                        'reference_id' => $sale->id,
                        'notes' => "POS transaction {$invNum}",
                    ]);
                }

                // Increment Product Master level stock representation
                $p = Product::query()->find($item['id']);
                $p->decrement('stock', $item['qty']);

                // Calculate COGS (use purchase price or 60% of price as proxy HPP)
                $totalCOGS += ($p->price * 0.6) * $item['qty'];
            }

            // Accounting journal entries (double entry)
            $ref = 'JRN-POS-' . now()->format('Ymd') . '-' . sprintf('%04d', $sale->id);

            // 1. Debit Cash/Bank Account
            $paymentAccCode = $this->paymentMethod === 'bank' ? '1112' : '1111'; // 1111: Cash, 1112: Bank
            $paymentAcc = Account::query()->firstOrCreate(
                ['code' => $paymentAccCode],
                ['name' => $this->paymentMethod === 'bank' ? 'Bank Account' : 'Cash Account', 'type' => 'asset', 'balance' => 0]
            );
            $paymentAcc->increment('balance', $subtotal);

            Journal::query()->create([
                'account_id' => $paymentAcc->id,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => "POS Sale {$invNum}",
                'reference_number' => $ref . '-1',
                'amount' => $subtotal,
                'type' => 'debit',
            ]);

            // 2. Credit Sales Revenue Account
            $revenueAcc = Account::query()->firstOrCreate(
                ['code' => '4100'],
                ['name' => 'Sales Revenue', 'type' => 'income', 'balance' => 0]
            );
            $revenueAcc->increment('balance', $subtotal);

            Journal::query()->create([
                'account_id' => $revenueAcc->id,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => "POS Sale {$invNum}",
                'reference_number' => $ref . '-2',
                'amount' => $subtotal,
                'type' => 'credit',
            ]);

            // 3. Debit COGS (Cost of Goods Sold) Account
            $cogsAcc = Account::query()->firstOrCreate(
                ['code' => '5100'],
                ['name' => 'Cost of Goods Sold', 'type' => 'expense', 'balance' => 0]
            );
            $cogsAcc->increment('balance', $totalCOGS);

            Journal::query()->create([
                'account_id' => $cogsAcc->id,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => "POS COGS for {$invNum}",
                'reference_number' => $ref . '-3',
                'amount' => $totalCOGS,
                'type' => 'debit',
            ]);

            // 4. Credit Inventory Asset Account
            $invAcc = Account::query()->firstOrCreate(
                ['code' => '1130'],
                ['name' => 'Inventory Asset', 'type' => 'asset', 'balance' => 0]
            );
            $invAcc->decrement('balance', $totalCOGS);

            Journal::query()->create([
                'account_id' => $invAcc->id,
                'transaction_date' => now()->format('Y-m-d'),
                'description' => "POS Inventory reduction for {$invNum}",
                'reference_number' => $ref . '-4',
                'amount' => $totalCOGS,
                'type' => 'credit',
            ]);

            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'POS',
                'action' => 'POS Transaction',
                'description' => "POS transaction completed: {$invNum} for Rp " . number_format($subtotal, 0, ',', '.')
            ]);
        });

        session()->flash('success', 'Transaction completed successfully!');
        $this->cart = [];
        $this->cashPaid = 0;
        $this->changeDue = 0;
    }

    public function render()
    {
        $productsQuery = Product::query();

        if ($this->searchProduct) {
            $productsQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchProduct . '%')
                  ->orWhere('code', 'like', '%' . $this->searchProduct . '%');
            });
        }

        return view('livewire.pos-manager', [
            'products' => $productsQuery->paginate(12),
            'customers' => Customer::all(),
        ]);
    }
}
