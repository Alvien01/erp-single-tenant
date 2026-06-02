<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReorderingRule;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockItem;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\ActivityLog;

class ReorderingRuleManager extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $rule_id;
    public $product_id;
    public $warehouse_id;
    public $min_qty = 5;
    public $max_qty = 50;
    public $order_qty = 20;

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
        $this->rule_id = null;
        $this->product_id = '';
        $this->warehouse_id = '';
        $this->min_qty = 5;
        $this->max_qty = 50;
        $this->order_qty = 20;
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'min_qty' => 'required|numeric|min:0',
            'max_qty' => 'required|numeric|gt:min_qty',
            'order_qty' => 'required|numeric|min:0.01',
        ]);

        ReorderingRule::updateOrCreate(['id' => $this->rule_id], [
            'product_id' => $this->product_id,
            'warehouse_id' => $this->warehouse_id,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'qty_multiple' => $this->order_qty,
        ]);

        $prod = Product::find($this->product_id);
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Inventory',
            'action' => $this->isEditMode ? 'Update Reordering Rule' : 'Create Reordering Rule',
            'description' => "Reordering trigger for product {$prod->name} saved."
        ]);

        session()->flash('success', 'Reordering trigger rule saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $rule = ReorderingRule::findOrFail($id);
        $this->rule_id = $rule->id;
        $this->product_id = $rule->product_id;
        $this->warehouse_id = $rule->warehouse_id;
        $this->min_qty = $rule->min_qty;
        $this->max_qty = $rule->max_qty;
        $this->order_qty = $rule->qty_multiple;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function triggerReplenishmentCheck($id)
    {
        $rule = ReorderingRule::with(['product', 'warehouse'])->findOrFail($id);
        
        // Check current stock levels in the rule's warehouse
        $stock = StockItem::where('product_id', $rule->product_id)
            ->where('warehouse_id', $rule->warehouse_id)
            ->first();
        
        $currentQty = $stock ? floatval($stock->qty_on_hand) : 0;

        if ($currentQty < floatval($rule->min_qty)) {
            // Find a supplier
            $supplier = Supplier::first();
            if (!$supplier) {
                session()->flash('error', 'No supplier defined in database. Cannot auto-generate PO.');
                return;
            }

            // Create Draft Purchase Order
            $poNum = 'PO-AUTO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $po = Purchase::create([
                'purchase_number' => $poNum,
                'supplier_id' => $supplier->id,
                'purchase_date' => now()->format('Y-m-d'),
                'grand_total' => $rule->qty_multiple * ($rule->product->purchase_cost ?? 10000),
                'status' => 'draft',
                'created_by' => auth()->id() ?? 1,
            ]);

            PurchaseItem::create([
                'purchase_id' => $po->id,
                'product_id' => $rule->product_id,
                'quantity' => $rule->qty_multiple,
                'unit_price' => $rule->product->purchase_cost ?? 10000,
                'total_price' => $rule->qty_multiple * ($rule->product->purchase_cost ?? 10000),
            ]);

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Purchasing',
                'action' => 'Auto-generate PO',
                'description' => "Reordering trigger executed for {$rule->product->name}. Draft PO {$poNum} created."
            ]);

            session()->flash('success', "Stock under threshold! Draft Purchase Order {$poNum} has been auto-generated.");
        } else {
            session()->flash('info', "Stock level is sufficient (Current: {$currentQty} >= Min: {$rule->min_qty}). No action needed.");
        }
    }

    public function delete($id)
    {
        $rule = ReorderingRule::findOrFail($id);
        $prod = $rule->product;
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Inventory',
            'action' => 'Delete Reordering Rule',
            'description' => "Reordering trigger for product {$prod->name} deleted."
        ]);

        $rule->delete();
        session()->flash('success', 'Reordering rule deleted.');
    }

    public function render()
    {
        $query = ReorderingRule::with(['product', 'warehouse']);

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.reordering-rule-manager', [
            'rules' => $query->paginate(10),
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('warehouse_name')->get(),
        ]);
    }
}
