<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductionOrder;
use App\Models\ProductionMaterial;
use App\Models\Bom;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class ProductionOrderManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $order_id;
    public $product_id;
    public $quantity = 1;
    public $start_date;
    public $end_date;
    public $status = 'planned';

    // Materials details
    public $materials = [];

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0.01',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->order_id = null;
        $this->product_id = '';
        $this->quantity = 1;
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->addDays(3)->format('Y-m-d');
        $this->status = 'planned';
        $this->materials = [];
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

    public function updatedProductId($value)
    {
        if ($value) {
            $bom = Bom::with('items.component')->where('product_id', $value)->first();
            if ($bom) {
                $this->materials = [];
                foreach ($bom->items as $item) {
                    $this->materials[] = [
                        'product_id' => $item->component_id,
                        'name' => $item->component->name,
                        'qty_required' => $item->qty * $this->quantity,
                        'qty_used' => $item->qty * $this->quantity,
                        'unit_cost' => $item->component->price ?? 0,
                    ];
                }
            } else {
                $this->materials = [];
            }
        }
    }

    public function updatedQuantity($value)
    {
        if ($this->product_id && $value) {
            $bom = Bom::with('items.component')->where('product_id', $this->product_id)->first();
            if ($bom) {
                foreach ($this->materials as $index => $mat) {
                    $bomItem = $bom->items->where('component_id', $mat['product_id'])->first();
                    if ($bomItem) {
                        $this->materials[$index]['qty_required'] = $bomItem->qty * $value;
                        $this->materials[$index]['qty_used'] = $bomItem->qty * $value;
                    }
                }
            }
        }
    }

    public function store()
    {
        $this->validate();

        $warehouse = Warehouse::first();
        if (!$warehouse) {
            session()->flash('error', 'Create at least one warehouse first.');
            return;
        }

        // Validate stock if status is going to be completed
        if ($this->status === 'completed') {
            foreach ($this->materials as $mat) {
                $stock = StockItem::where('product_id', $mat['product_id'])
                    ->where('warehouse_id', $warehouse->id)
                    ->first();
                
                $available = $stock ? $stock->qty_on_hand : 0;
                if ($available < $mat['qty_used']) {
                    session()->flash('error', 'Insufficient stock for component: ' . $mat['name'] . '. Required: ' . $mat['qty_used'] . ', Available: ' . $available);
                    return;
                }
            }
        }

        DB::transaction(function () use ($warehouse) {
            $totalCost = 0;
            foreach ($this->materials as $mat) {
                $totalCost += ($mat['qty_used'] * $mat['unit_cost']);
            }

            $order = ProductionOrder::updateOrCreate(
                ['id' => $this->order_id],
                [
                    'order_number' => $this->isEditMode ? ProductionOrder::find($this->order_id)->order_number : 'PRD-' . date('Ymd') . '-' . sprintf('%04d', ProductionOrder::count() + 1),
                    'product_id' => $this->product_id,
                    'quantity' => $this->quantity,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'total_cost' => $totalCost,
                    'status' => $this->status,
                ]
            );

            if ($this->isEditMode) {
                $order->materials()->delete();
            }

            foreach ($this->materials as $mat) {
                ProductionMaterial::create([
                    'production_order_id' => $order->id,
                    'product_id' => $mat['product_id'],
                    'qty_required' => $mat['qty_required'],
                    'qty_used' => $mat['qty_used'],
                    'unit_cost' => $mat['unit_cost'],
                    'total_cost' => ($mat['qty_used'] * $mat['unit_cost']),
                ]);

                // Deduct stock for completed order
                if ($this->status === 'completed') {
                    $stock = StockItem::where('product_id', $mat['product_id'])
                        ->where('warehouse_id', $warehouse->id)
                        ->first();
                    if ($stock) {
                        $stock->qty_on_hand -= $mat['qty_used'];
                        $stock->save();
                    }

                    Inventory::create([
                        'product_id' => $mat['product_id'],
                        'quantity' => $mat['qty_used'],
                        'type' => 'out',
                        'reference_type' => 'production_material',
                        'reference_id' => $order->id,
                        'notes' => 'Consumed for Production Order ' . $order->order_number
                    ]);
                }
            }

            // If completed, add finished product stock
            if ($this->status === 'completed') {
                $finishedStock = StockItem::firstOrCreate([
                    'product_id' => $this->product_id,
                    'warehouse_id' => $warehouse->id,
                ]);
                $finishedStock->qty_on_hand += $this->quantity;
                $finishedStock->save();

                Inventory::create([
                    'product_id' => $this->product_id,
                    'quantity' => $this->quantity,
                    'type' => 'in',
                    'reference_type' => 'production_output',
                    'reference_id' => $order->id,
                    'notes' => 'Finished Goods from Production Order ' . $order->order_number
                ]);
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Manufacturing',
                'action' => $this->isEditMode ? 'Update Production Order' : 'Create Production Order',
                'description' => 'Production Order ' . $order->order_number . ' has been updated to status ' . $this->status
            ]);
        });

        session()->flash('success', 'Production Order processed successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $order = ProductionOrder::with('materials.product')->findOrFail($id);
        $this->order_id = $order->id;
        $this->product_id = $order->product_id;
        $this->quantity = $order->quantity;
        $this->start_date = $order->start_date;
        $this->end_date = $order->end_date;
        $this->status = $order->status;
        
        $this->materials = [];
        foreach ($order->materials as $mat) {
            $this->materials[] = [
                'product_id' => $mat->product_id,
                'name' => $mat->product->name,
                'qty_required' => $mat->qty_required,
                'qty_used' => $mat->qty_used,
                'unit_cost' => $mat->unit_cost,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function render()
    {
        $query = ProductionOrder::with(['product', 'materials.product']);

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('order_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.production-order-manager', [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10),
            'products' => Product::all(),
        ]);
    }
}
