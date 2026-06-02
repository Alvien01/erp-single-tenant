<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\ActivityLog;

class PurchaseManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    
    // Form fields
    public $purchase_id;
    public $purchase_number;
    public $supplier_id;
    public $purchase_date;
    public $due_date;
    public $total_amount = 0;
    public $tax_amount = 0;
    public $grand_total = 0;
    public $purchase_status = 'draft';
    
    public $items = [];

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
        $this->purchase_id = null;
        $this->purchase_number = 'PO-' . now()->format('Ymd') . '-' . sprintf('%04d', Purchase::count() + 1);
        $this->supplier_id = '';
        $this->purchase_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->total_amount = 0;
        $this->tax_amount = 0;
        $this->grand_total = 0;
        $this->purchase_status = 'draft';
        $this->items = [];
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->addItem();
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
                    $this->items[$index]['unit_price'] = $product->price * 0.8; // purchase price estimate (80% of selling price)
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
        $this->tax_amount = $total * 0.11;
        $this->grand_total = $this->total_amount + $this->tax_amount;
    }

    public function store()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'due_date' => 'nullable|date',
            'purchase_status' => 'required',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $purchase = Purchase::updateOrCreate(['id' => $this->purchase_id], [
            'purchase_number' => $this->purchase_number,
            'supplier_id' => $this->supplier_id,
            'purchase_date' => $this->purchase_date,
            'due_date' => $this->due_date,
            'total_amount' => $this->total_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'status' => $this->purchase_status,
        ]);

        if ($this->isEditMode) {
            PurchaseItem::where('purchase_id', $purchase->id)->delete();
        }

        foreach ($this->items as $item) {
            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total'],
            ]);

            if ($this->purchase_status === 'received') {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->increment('stock', $item['quantity']);
                }
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Purchasing',
            'action' => $this->isEditMode ? 'Update PO' : 'Create PO',
            'description' => 'PO ' . $this->purchase_number . ' saved with total Rp ' . number_format($this->grand_total, 0, ',', '.')
        ]);

        session()->flash('success', 'Purchase order saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);
        $this->purchase_id = $purchase->id;
        $this->purchase_number = $purchase->purchase_number;
        $this->supplier_id = $purchase->supplier_id;
        $this->purchase_date = $purchase->purchase_date;
        $this->due_date = $purchase->due_date;
        $this->total_amount = $purchase->total_amount;
        $this->tax_amount = $purchase->tax_amount;
        $this->grand_total = $purchase->grand_total;
        $this->purchase_status = $purchase->status;

        $this->items = [];
        foreach ($purchase->items as $item) {
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
        $purchase = Purchase::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Purchasing',
            'action' => 'Delete PO',
            'description' => 'PO ' . $purchase->purchase_number . ' has been deleted.'
        ]);

        $purchase->delete();
        session()->flash('success', 'Purchase order deleted successfully.');
    }

    public function render()
    {
        $query = Purchase::with(['supplier']);

        if ($this->search) {
            $query->where('purchase_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('supplier', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.purchase-manager', [
            'purchases' => $query->orderBy('purchase_number', 'desc')->paginate(10),
            'suppliers' => Supplier::all(),
            'products' => Product::all(),
        ]);
    }
}
