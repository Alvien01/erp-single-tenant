<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\ActivityLog;

class DeliveryOrderManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form fields
    public $do_id;
    public $do_number;
    public $sales_order_id;
    public $warehouse_id;
    public $delivered_by;
    public $delivery_date;
    public $status = 'draft';
    public $notes;
    public $items = []; // [{product_id, name, qty_ordered, qty_delivered}]

    public $isOpen = false;
    public $isEditMode = false;

    public function mount()
    {
        $this->delivery_date = now()->format('Y-m-d');
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
        $this->do_id = null;
        $this->do_number = '';
        $this->sales_order_id = '';
        $this->warehouse_id = '';
        $this->delivered_by = '';
        $this->delivery_date = now()->format('Y-m-d');
        $this->status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->isEditMode = false;
    }

    public function updatedSalesOrderId($soId)
    {
        if ($soId) {
            $so = SalesOrder::with('items.product')->find($soId);
            if ($so) {
                $this->items = [];
                foreach ($so->items as $item) {
                    $this->items[] = [
                        'product_id' => $item->product_id,
                        'name' => $item->product->name,
                        'qty_ordered' => $item->qty,
                        'qty_delivered' => $item->qty,
                    ];
                }
            }
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->do_number = 'DO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'do_number' => 'required|string|unique:delivery_orders,do_number,' . $this->do_id,
            'sales_order_id' => 'required|exists:sales_orders,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'delivery_date' => 'required|date',
            'status' => 'required|in:draft,ready,shipped,delivered,cancelled',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty_ordered' => 'required|numeric|min:0.01',
            'items.*.qty_delivered' => 'required|numeric|min:0',
        ]);

        $do = DeliveryOrder::updateOrCreate(['id' => $this->do_id], [
            'do_number' => $this->do_number,
            'sales_order_id' => $this->sales_order_id,
            'warehouse_id' => $this->warehouse_id,
            'delivered_by' => auth()->id() ?? 1,
            'delivery_date' => $this->delivery_date,
            'status' => $this->status,
            'notes' => $this->notes,
        ]);

        // Clear and recreate items
        DeliveryOrderItem::where('delivery_order_id', $do->id)->delete();
        foreach ($this->items as $item) {
            DeliveryOrderItem::create([
                'delivery_order_id' => $do->id,
                'product_id' => $item['product_id'],
                'qty_ordered' => $item['qty_ordered'],
                'qty_delivered' => $item['qty_delivered'],
            ]);
        }

        // If status changed to SHIPPED or DELIVERED, process stock decrement
        if (in_array($this->status, ['shipped', 'delivered'])) {
            $this->processStockAdjustment($do);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => $this->isEditMode ? 'Update Delivery Order' : 'Create Delivery Order',
            'description' => "Delivery Order {$this->do_number} has been saved."
        ]);

        session()->flash('success', 'Delivery Order saved successfully.');
        $this->closeModal();
    }

    private function processStockAdjustment($do)
    {
        foreach ($do->items as $item) {
            // Find or create StockItem in that warehouse
            $stock = StockItem::firstOrCreate([
                'product_id' => $item->product_id,
                'warehouse_id' => $do->warehouse_id,
            ]);

            // Decrement physical stock
            $stock->qty_on_hand -= $item->qty_delivered;
            $stock->save();

            // Record to Inventories table (log)
            Inventory::create([
                'product_id' => $item->product_id,
                'quantity' => $item->qty_delivered,
                'type' => 'out',
                'reference_type' => DeliveryOrder::class,
                'reference_id' => $do->id,
                'notes' => "Delivered via DO: {$do->do_number}",
            ]);

            // Update main product stock counter
            $product = $item->product;
            $product->stock = StockItem::where('product_id', $product->id)->sum('qty_on_hand');
            $product->save();
        }
    }

    public function edit($id)
    {
        $do = DeliveryOrder::with('items.product')->findOrFail($id);
        $this->do_id = $do->id;
        $this->do_number = $do->do_number;
        $this->sales_order_id = $do->sales_order_id;
        $this->warehouse_id = $do->warehouse_id;
        $this->delivered_by = $do->delivered_by;
        $this->delivery_date = $do->delivery_date;
        $this->status = $do->status;
        $this->notes = $do->notes;

        $this->items = [];
        foreach ($do->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'qty_ordered' => $item->qty_ordered,
                'qty_delivered' => $item->qty_delivered,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function updateStatus($id, $newStatus)
    {
        $do = DeliveryOrder::findOrFail($id);
        $do->status = $newStatus;
        $do->save();

        if (in_array($newStatus, ['shipped', 'delivered'])) {
            $this->processStockAdjustment($do);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Update DO Status',
            'description' => "Delivery Order {$do->do_number} marked as {$newStatus}."
        ]);

        session()->flash('success', "Delivery Order status updated to {$newStatus}.");
    }

    public function delete($id)
    {
        $do = DeliveryOrder::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Delete Delivery Order',
            'description' => "Delivery Order {$do->do_number} deleted."
        ]);

        $do->delete();
        session()->flash('success', 'Delivery Order deleted successfully.');
    }

    public function render()
    {
        $query = DeliveryOrder::with(['salesOrder.customer', 'warehouse']);

        if ($this->search) {
            $query->where('do_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('salesOrder.customer', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }

        return view('livewire.delivery-order-manager', [
            'deliveryOrders' => $query->orderBy('delivery_date', 'desc')->paginate(10),
            'salesOrders' => SalesOrder::where('status', 'confirmed')->orderBy('so_number', 'desc')->get(),
            'warehouses' => Warehouse::orderBy('warehouse_name')->get(),
        ]);
    }
}
