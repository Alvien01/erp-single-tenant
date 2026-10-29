<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\GoodReceipt;
use App\Models\GoodReceiptItem;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class GoodReceiptManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $gr_id;
    public $supplier_id;
    public $reference; // reference PO number or invoice
    public $purchase_id; // reference PO
    public $status = 'draft';

    // Item rows
    public $items = [];

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'reference' => 'nullable|string|max:255',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.warehouse_id' => 'required|exists:warehouses,id',
        'items.*.qty_received' => 'required|numeric|min:0.01',
        'items.*.unit_price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->gr_id = null;
        $this->supplier_id = '';
        $this->reference = '';
        $this->purchase_id = '';
        $this->status = 'draft';
        $defaultWh = Warehouse::first()->id ?? '';
        $this->items = [
            ['product_id' => '', 'warehouse_id' => $defaultWh, 'qty_received' => 1, 'unit_price' => 0, 'subtotal' => 0]
        ];
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function addItem()
    {
        $defaultWh = Warehouse::first()->id ?? '';
        $this->items[] = [
            'product_id' => '',
            'warehouse_id' => $defaultWh,
            'qty_received' => 1,
            'unit_price' => 0,
            'subtotal' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 3) {
            $index = $parts[1];
            $field = $parts[2];
        } elseif (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];
        } else {
            return;
        }

        if (!isset($this->items[$index])) {
            return;
        }

        // Auto-fill unit price when product is selected
        if ($field === 'product_id') {
            if ($value) {
                $product = Product::find($value);
                if ($product) {
                    $this->items[$index]['unit_price'] = (float) $product->price;
                }
            } else {
                $this->items[$index]['unit_price'] = 0;
            }
        }

        // Recalculate subtotal for this item row
        $qty = floatval($this->items[$index]['qty_received'] ?? 0);
        $price = floatval($this->items[$index]['unit_price'] ?? 0);
        $this->items[$index]['subtotal'] = $qty * $price;
    }

    public function updatedPurchaseId($value)
    {
        if ($value) {
            $po = Purchase::with('items.product')->find($value);
            if ($po) {
                $this->supplier_id = $po->supplier_id;
                $this->reference = $po->purchase_number;
                $this->items = [];
                $defaultWh = Warehouse::first()->id ?? '';
                foreach ($po->items as $item) {
                    $qty = floatval($item->quantity);
                    $price = floatval($item->unit_price);
                    $this->items[] = [
                        'product_id' => $item->product_id,
                        'warehouse_id' => $defaultWh,
                        'qty_received' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $qty * $price,
                    ];
                }
            }
        }
    }

    public function store()
    {
        $this->validate();

        if (count($this->items) === 0) {
            session()->flash('error', 'Add at least one item.');
            return;
        }

        DB::transaction(function () {
            $gr = GoodReceipt::updateOrCreate(
                ['id' => $this->gr_id],
                [
                    'gr_number' => $this->isEditMode ? GoodReceipt::find($this->gr_id)->gr_number : 'GR-' . date('Ymd') . '-' . sprintf('%04d', GoodReceipt::count() + 1),
                    'supplier_id' => $this->supplier_id,
                    'received_by' => auth()->id() ?? 1,
                    'reference' => $this->reference ?: '',
                    'status' => $this->status,
                ]
            );

            if ($this->isEditMode) {
                $gr->items()->delete();
            }

            foreach ($this->items as $item) {
                $qty = floatval($item['qty_received']);
                $unitPrice = floatval($item['unit_price']);
                $subtotal = $qty * $unitPrice;

                GoodReceiptItem::create([
                    'good_receipt_id' => $gr->id,
                    'product_id' => $item['product_id'],
                    'warehouse_id' => $item['warehouse_id'],
                    'qty_received' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // If status is 'received', add stock to warehouse
                if ($this->status === 'received') {
                    $stock = StockItem::firstOrCreate([
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $item['warehouse_id'],
                    ]);
                    $stock->qty_on_hand += $qty;
                    $stock->save();

                    // Log inventory
                    Inventory::create([
                        'product_id' => $item['product_id'],
                        'quantity' => $qty,
                        'type' => 'in',
                        'reference_type' => 'good_receipt',
                        'reference_id' => $gr->id,
                        'notes' => 'Received via Good Receipt ' . $gr->gr_number
                    ]);
                }
            }

            // If referencing a PO, mark it as received
            if ($this->purchase_id && $this->status === 'received') {
                $po = Purchase::find($this->purchase_id);
                if ($po) {
                    $po->status = 'received';
                    $po->save();
                }
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Warehouse',
                'action' => $this->isEditMode ? 'Update Good Receipt' : 'Create Good Receipt',
                'description' => 'Good Receipt ' . $gr->gr_number . ' has been saved with status ' . $this->status
            ]);
        });

        session()->flash('success', 'Good Receipt saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $gr = GoodReceipt::with('items')->findOrFail($id);
        $this->gr_id = $gr->id;
        $this->supplier_id = $gr->supplier_id;
        $this->reference = $gr->reference;
        $this->status = $gr->status;
        
        $this->items = [];
        foreach ($gr->items as $item) {
            $qty = floatval($item->qty_received);
            $unitPrice = floatval($item->unit_price);
            $this->items[] = [
                'product_id' => $item->product_id,
                'warehouse_id' => $item->warehouse_id,
                'qty_received' => $qty,
                'unit_price' => $unitPrice,
                'subtotal' => floatval($item->subtotal ?: ($qty * $unitPrice)),
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function getTotalReceiptQtyProperty(): float
    {
        return collect($this->items)->sum(fn($i) => floatval($i['qty_received'] ?? 0));
    }

    public function getTotalReceiptSubtotalProperty(): float
    {
        return collect($this->items)->sum(fn($i) => floatval($i['subtotal'] ?? (floatval($i['qty_received'] ?? 0) * floatval($i['unit_price'] ?? 0))));
    }

    public function render()
    {
        $query = GoodReceipt::with(['supplier', 'items.product', 'items.warehouse']);

        if ($this->search) {
            $query->whereHas('supplier', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('gr_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.good-receipt-manager', [
            'receipts' => $query->orderBy('created_at', 'desc')->paginate(10),
            'suppliers' => Supplier::all(),
            'warehouses' => Warehouse::all(),
            'products' => Product::all(),
            'purchaseOrders' => Purchase::where('status', '!=', 'received')->get(),
            'totalReceiptQty' => $this->totalReceiptQty,
            'totalReceiptSubtotal' => $this->totalReceiptSubtotal,
        ]);
    }
}
