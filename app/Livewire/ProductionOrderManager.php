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
    public $warehouse_id;
    public $quantity = 1;
    public $start_date;
    public $end_date;
    public $status = 'planned';

    // Materials details
    public $materials = [];

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'warehouse_id' => 'nullable|exists:warehouses,id',
        'quantity' => 'required|numeric|min:0.01',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ];

    protected $messages = [
        'product_id.required' => 'Produk yang akan diproduksi wajib dipilih.',
        'quantity.required' => 'Target Qty produksi wajib diisi.',
        'quantity.min' => 'Target Qty minimal 0.01.',
        'start_date.required' => 'Tanggal mulai produksi wajib diisi.',
        'end_date.required' => 'Tanggal selesai produksi wajib diisi.',
        'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
    ];

    public function mount()
    {
        $this->getOrCreateWarehouse();
        $this->resetInputFields();
    }

    public function getOrCreateWarehouse()
    {
        if ($this->warehouse_id) {
            $wh = Warehouse::find($this->warehouse_id);
            if ($wh) {
                return $wh;
            }
        }

        $wh = Warehouse::first();
        if (!$wh) {
            $wh = Warehouse::create([
                'warehouse_code' => 'WH-MAIN',
                'warehouse_name' => 'Gudang Utama (Main Warehouse)',
                'address' => 'Gudang Pusat',
            ]);
        }

        $this->warehouse_id = $wh->id;
        return $wh;
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

        $wh = $this->getOrCreateWarehouse();
        $this->warehouse_id = $wh->id;
        $this->resetValidation();
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
                $qty = is_numeric($this->quantity) && floatval($this->quantity) > 0 ? floatval($this->quantity) : 1;
                foreach ($bom->items as $item) {
                    if ($item->component) {
                        $this->materials[] = [
                            'product_id' => $item->component_id,
                            'name' => $item->component->name,
                            'qty_required' => floatval($item->qty) * $qty,
                            'qty_used' => floatval($item->qty) * $qty,
                            'unit_cost' => floatval($item->component->price ?? 0),
                        ];
                    }
                }
            } else {
                $this->materials = [];
            }
        }
    }

    public function updatedQuantity($value)
    {
        $qty = is_numeric($value) && floatval($value) > 0 ? floatval($value) : 1;
        if ($this->product_id) {
            $bom = Bom::with('items.component')->where('product_id', $this->product_id)->first();
            if ($bom) {
                foreach ($this->materials as $index => $mat) {
                    $bomItem = $bom->items->where('component_id', $mat['product_id'])->first();
                    if ($bomItem) {
                        $this->materials[$index]['qty_required'] = floatval($bomItem->qty) * $qty;
                        $this->materials[$index]['qty_used'] = floatval($bomItem->qty) * $qty;
                    }
                }
            }
        }
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'PRD-' . date('Ymd') . '-';
        $lastOrder = ProductionOrder::where('order_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastOrder && preg_match('/-(\d{4})$/', $lastOrder->order_number, $matches)) {
            $nextNum = ((int) $matches[1]) + 1;
        } else {
            $nextNum = 1;
        }

        $candidate = $prefix . sprintf('%04d', $nextNum);
        while (ProductionOrder::where('order_number', $candidate)->exists()) {
            $nextNum++;
            $candidate = $prefix . sprintf('%04d', $nextNum);
        }

        return $candidate;
    }

    public function store()
    {
        $this->validate();

        $warehouse = $this->getOrCreateWarehouse();

        // Validate stock if status is going to be completed
        if ($this->status === 'completed') {
            foreach ($this->materials as $mat) {
                $stock = StockItem::where('product_id', $mat['product_id'])
                    ->where('warehouse_id', $warehouse->id)
                    ->first();
                
                $available = $stock ? $stock->qty_on_hand : 0;
                $used = floatval($mat['qty_used'] ?? 0);
                if ($available < $used) {
                    session()->flash('error', 'Stok tidak mencukupi untuk komponen: ' . ($mat['name'] ?? 'Bahan') . '. Dibutuhkan: ' . $used . ', Tersedia: ' . $available . ' di ' . $warehouse->warehouse_name);
                    return;
                }
            }
        }

        DB::transaction(function () use ($warehouse) {
            $totalCost = 0;
            foreach ($this->materials as $mat) {
                $totalCost += (floatval($mat['qty_used'] ?? 0) * floatval($mat['unit_cost'] ?? 0));
            }

            $orderNumber = $this->isEditMode && $this->order_id
                ? ProductionOrder::find($this->order_id)?->order_number ?? $this->generateOrderNumber()
                : $this->generateOrderNumber();

            $order = ProductionOrder::updateOrCreate(
                ['id' => $this->order_id],
                [
                    'order_number' => $orderNumber,
                    'product_id' => $this->product_id,
                    'quantity' => floatval($this->quantity),
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
                $qtyReq = floatval($mat['qty_required'] ?? 0);
                $qtyUsed = floatval($mat['qty_used'] ?? 0);
                $unitCost = floatval($mat['unit_cost'] ?? 0);

                ProductionMaterial::create([
                    'production_order_id' => $order->id,
                    'product_id' => $mat['product_id'],
                    'qty_required' => $qtyReq,
                    'qty_used' => $qtyUsed,
                    'unit_cost' => $unitCost,
                    'total_cost' => ($qtyUsed * $unitCost),
                ]);

                // Deduct stock for completed order
                if ($this->status === 'completed') {
                    $stock = StockItem::where('product_id', $mat['product_id'])
                        ->where('warehouse_id', $warehouse->id)
                        ->first();
                    if ($stock) {
                        $stock->qty_on_hand -= $qtyUsed;
                        $stock->save();
                    }

                    Inventory::create([
                        'product_id' => $mat['product_id'],
                        'quantity' => $qtyUsed,
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
                ], [
                    'qty_on_hand' => 0
                ]);
                $finishedStock->qty_on_hand += floatval($this->quantity);
                $finishedStock->save();

                Inventory::create([
                    'product_id' => $this->product_id,
                    'quantity' => floatval($this->quantity),
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

        session()->flash('success', 'Production Order berhasil disimpan.');
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
                'name' => $mat->product->name ?? 'Komponen',
                'qty_required' => $mat->qty_required,
                'qty_used' => $mat->qty_used,
                'unit_cost' => $mat->unit_cost,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->materials()->delete();
        $order->delete();
        session()->flash('success', 'Production Order berhasil dihapus.');
    }

    public function render()
    {
        $query = ProductionOrder::with(['product', 'materials.product']);

        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('product', function($sub) {
                    $sub->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('order_number', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.production-order-manager', [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10),
            'products' => Product::orderBy('name')->get(),
            'warehouses' => Warehouse::orderBy('warehouse_name')->get(),
        ]);
    }
}
