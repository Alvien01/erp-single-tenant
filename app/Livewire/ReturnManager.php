<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Warehouse;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\ActivityLog;

class ReturnManager extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $return_id;
    public $return_number;
    public $type = 'sale'; // sale, purchase
    public $reference_id; // Sale ID or Purchase ID
    public $warehouse_id;
    public $return_date;
    public $status = 'draft';
    public $notes;
    public $items = []; // [{product_id, name, qty_original, qty_returned, reason}]

    public $isOpen = false;
    public $isEditMode = false;

    public function mount()
    {
        $this->return_date = now()->format('Y-m-d');
    }

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
        $this->return_id = null;
        $this->return_number = '';
        $this->type = 'sale';
        $this->reference_id = '';
        $this->warehouse_id = '';
        $this->return_date = now()->format('Y-m-d');
        $this->status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->isEditMode = false;
    }

    public function updatedType()
    {
        $this->reference_id = '';
        $this->items = [];
    }

    public function updatedReferenceId($refId)
    {
        if ($refId) {
            if ($this->type === 'sale') {
                $sale = Sale::with('items.product')->find($refId);
                if ($sale) {
                    $this->items = [];
                    foreach ($sale->items as $item) {
                        $this->items[] = [
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'qty_original' => $item->quantity,
                            'qty_returned' => $item->quantity,
                            'reason' => 'Damaged/Defective',
                        ];
                    }
                }
            } else {
                $purchase = Purchase::with('items.product')->find($refId);
                if ($purchase) {
                    $this->items = [];
                    foreach ($purchase->items as $item) {
                        $this->items[] = [
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'qty_original' => $item->quantity,
                            'qty_returned' => $item->quantity,
                            'reason' => 'Incorrect Item',
                        ];
                    }
                }
            }
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->return_number = 'RET-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'return_number' => 'required|string|unique:returns,return_number,' . $this->return_id,
            'type' => 'required|in:sale,purchase',
            'reference_id' => 'required',
            'warehouse_id' => 'required|exists:warehouses,id',
            'return_date' => 'required|date',
            'status' => 'required|in:draft,completed,cancelled',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_original' => 'required|numeric|min:0.01',
            'items.*.qty_returned' => 'required|numeric|min:0',
            'items.*.reason' => 'required|string',
        ]);

        $refType = $this->type === 'sale' ? Sale::class : Purchase::class;

        $ret = ReturnModel::updateOrCreate(['id' => $this->return_id], [
            'return_number' => $this->return_number,
            'reference_type' => $refType,
            'reference_id' => $this->reference_id,
            'warehouse_id' => $this->warehouse_id,
            'return_date' => $this->return_date,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_by' => auth()->id() ?? 1,
        ]);

        // Recreate items
        ReturnItem::where('return_id', $ret->id)->delete();
        foreach ($this->items as $item) {
            ReturnItem::create([
                'return_id' => $ret->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['qty_returned'],
                'reason' => $item['reason'],
            ]);
        }

        // Process stock adjustments if COMPLETED
        if ($this->status === 'completed') {
            $this->processReturnStock($ret);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => $this->isEditMode ? 'Update Return' : 'Create Return',
            'description' => "Return Order {$this->return_number} has been saved."
        ]);

        session()->flash('success', 'Return Order saved successfully.');
        $this->closeModal();
    }

    private function processReturnStock($ret)
    {
        foreach ($ret->items as $item) {
            $stock = StockItem::firstOrCreate([
                'product_id' => $item->product_id,
                'warehouse_id' => $ret->warehouse_id,
            ]);

            if ($ret->reference_type === Sale::class) {
                // Return from customer -> Stock goes UP
                $stock->qty_on_hand += $item->quantity;
                $direction = 'in';
                $notes = "Returned from customer via RET: {$ret->return_number}";
            } else {
                // Return to supplier -> Stock goes DOWN
                $stock->qty_on_hand -= $item->quantity;
                $direction = 'out';
                $notes = "Returned to supplier via RET: {$ret->return_number}";
            }
            $stock->save();

            Inventory::create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'type' => $direction,
                'reference_type' => ReturnModel::class,
                'reference_id' => $ret->id,
                'notes' => $notes,
            ]);

            $product = $item->product;
            $product->stock = StockItem::where('product_id', $product->id)->sum('qty_on_hand');
            $product->save();
        }
    }

    public function completeReturn($id)
    {
        $ret = ReturnModel::findOrFail($id);
        $ret->status = 'completed';
        $ret->save();

        $this->processReturnStock($ret);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Complete Return',
            'description' => "Return Order {$ret->return_number} marked as completed."
        ]);

        session()->flash('success', 'Return completed and stock adjusted.');
    }

    public function delete($id)
    {
        $ret = ReturnModel::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Delete Return',
            'description' => "Return Order {$ret->return_number} deleted."
        ]);

        $ret->delete();
        session()->flash('success', 'Return Order deleted successfully.');
    }

    public function render()
    {
        $query = ReturnModel::with(['warehouse']);

        if ($this->search) {
            $query->where('return_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.return-manager', [
            'returns' => $query->orderBy('return_date', 'desc')->paginate(10),
            'sales' => Sale::orderBy('invoice_number', 'desc')->get(),
            'purchases' => Purchase::orderBy('purchase_number', 'desc')->get(),
            'warehouses' => Warehouse::orderBy('warehouse_name')->get(),
        ]);
    }
}
