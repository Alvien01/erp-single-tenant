<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\ActivityLog;

class SalesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    
    // Form fields
    public $sale_id;
    public $invoice_number;
    public $customer_id;
    public $sale_date;
    public $due_date;
    public $total_amount = 0;
    public $tax_amount = 0;
    public $grand_total = 0;
    public $sale_status = 'draft';
    public $notes;
    
    // Selected items for creation
    public $items = []; // array of ['product_id', 'quantity', 'unit_price', 'total']

    public $isOpen = false;
    public $isEditMode = false;

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->sale_id = null;
        $this->invoice_number = 'INV-' . now()->format('Ymd') . '-' . sprintf('%04d', Sale::count() + 1);
        $this->customer_id = '';
        $this->sale_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(14)->format('Y-m-d');
        $this->total_amount = 0;
        $this->tax_amount = 0;
        $this->grand_total = 0;
        $this->sale_status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->addItem(); // start with one empty item row
        $this->openModal();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) === 3) {
            $index = $parts[1];
            $field = $parts[2];

            if ($field === 'product_id' && $value) {
                $product = Product::find($value);
                if ($product) {
                    $this->items[$index]['unit_price'] = $product->price;
                }
            }

            $qty = floatval($this->items[$index]['quantity'] ?? 0);
            $price = floatval($this->items[$index]['unit_price'] ?? 0);
            $this->items[$index]['total'] = $qty * $price;
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += floatval($item['total']);
        }
        $this->total_amount = $total;
        $this->tax_amount = $total * 0.11; // 11% VAT
        $this->grand_total = $this->total_amount + $this->tax_amount;
    }

    public function store()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date',
            'sale_status' => 'required',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $sale = Sale::updateOrCreate(['id' => $this->sale_id], [
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'sale_date' => $this->sale_date,
            'due_date' => $this->due_date,
            'total_amount' => $this->total_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'status' => $this->sale_status,
            'notes' => $this->notes,
        ]);

        if ($this->isEditMode) {
            SaleItem::where('sale_id', $sale->id)->delete();
        }

        foreach ($this->items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total'],
            ]);

            if ($this->sale_status === 'delivered') {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Sales',
            'action' => $this->isEditMode ? 'Update Invoice' : 'Create Invoice',
            'description' => 'Invoice ' . $this->invoice_number . ' has been saved with total Rp ' . number_format($this->grand_total, 0, ',', '.')
        ]);

        session()->flash('success', 'Sale transaction saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $this->sale_id = $sale->id;
        $this->invoice_number = $sale->invoice_number;
        $this->customer_id = $sale->customer_id;
        $this->sale_date = $sale->sale_date;
        $this->due_date = $sale->due_date;
        $this->total_amount = $sale->total_amount;
        $this->tax_amount = $sale->tax_amount;
        $this->grand_total = $sale->grand_total;
        $this->sale_status = $sale->status;
        $this->notes = $sale->notes;

        $this->items = [];
        foreach ($sale->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total_price
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $sale = Sale::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Sales',
            'action' => 'Delete Invoice',
            'description' => 'Invoice ' . $sale->invoice_number . ' has been deleted.'
        ]);

        $sale->delete();
        session()->flash('success', 'Invoice deleted successfully.');
    }

    public function render()
    {
        $query = Sale::with(['customer']);

        if ($this->search) {
            $query->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.sales-manager', [
            'sales' => $query->orderBy('invoice_number', 'desc')->paginate(10),
            'customers' => Customer::all(),
            'products' => Product::all(),
        ]);
    }
}
