<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WarehouseTransfer;
use App\Models\WarehouseTransferItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\ActivityLog;

class WarehouseTransferManager extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $transfer_id;
    public $transfer_number;
    public $source_warehouse_id;
    public $destination_warehouse_id;
    public $transfer_date;
    public $status = 'draft';
    public $notes;
    public $items = []; // [{product_id, qty, available_qty}]

    public $isOpen = false;
    public $isEditMode = false;

    public function mount()
    {
        $this->transfer_date = now()->format('Y-m-d');
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
        $this->transfer_id = null;
        $this->transfer_number = '';
        $this->source_warehouse_id = '';
        $this->destination_warehouse_id = '';
        $this->transfer_date = now()->format('Y-m-d');
        $this->status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->isEditMode = false;
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'qty' => 1,
            'available_qty' => 0,
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
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'product_id' && $this->source_warehouse_id) {
                $stock = StockItem::where('product_id', $value)
                    ->where('warehouse_id', $this->source_warehouse_id)
                    ->first();
                $this->items[$index]['available_qty'] = $stock ? floatval($stock->qty_on_hand) : 0;
            }
        }
    }

    public function updatedSourceWarehouseId($sourceWhId)
    {
        // Update available quantities for already listed items
        if ($sourceWhId) {
            foreach ($this->items as $index => $item) {
                if ($item['product_id']) {
                    $stock = StockItem::where('product_id', $item['product_id'])
                        ->where('warehouse_id', $sourceWhId)
                        ->first();
                    $this->items[$index]['available_qty'] = $stock ? floatval($stock->qty_on_hand) : 0;
                }
            }
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->transfer_number = 'TRF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'transfer_number' => 'required|string|unique:warehouse_transfers,transfer_number,' . $this->transfer_id,
            'source_warehouse_id' => 'required|exists:warehouses,id|different:destination_warehouse_id',
            'destination_warehouse_id' => 'required|exists:warehouses,id',
            'transfer_date' => 'required|date',
            'status' => 'required|in:draft,completed,cancelled',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
        ]);

        // Check if there is enough stock at source for all items when status is completed
        if ($this->status === 'completed') {
            foreach ($this->items as $item) {
                $stock = StockItem::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $this->source_warehouse_id)
                    ->first();
                $qtyOnHand = $stock ? floatval($stock->qty_on_hand) : 0;

                if ($qtyOnHand < floatval($item['qty'])) {
                    $prod = Product::find($item['product_id']);
                    session()->flash('error', "Insufficient stock for product {$prod->name} in source warehouse.");
                    return;
                }
            }
        }

        $trf = WarehouseTransfer::updateOrCreate(['id' => $this->transfer_id], [
            'transfer_number' => $this->transfer_number,
            'source_warehouse_id' => $this->source_warehouse_id,
            'destination_warehouse_id' => $this->destination_warehouse_id,
            'transfer_date' => $this->transfer_date,
            'status' => $this->status,
            'notes' => $this->notes,
            'requested_by' => auth()->id() ?? 1,
        ]);

        WarehouseTransferItem::where('warehouse_transfer_id', $trf->id)->delete();
        foreach ($this->items as $item) {
            WarehouseTransferItem::create([
                'warehouse_transfer_id' => $trf->id,
                'product_id' => $item['product_id'],
                'qty' => $item['qty'],
            ]);
        }

        // Process actual stock movement if completed
        if ($this->status === 'completed') {
            $this->processTransferMovement($trf);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Warehouse',
            'action' => $this->isEditMode ? 'Update Warehouse Transfer' : 'Create Warehouse Transfer',
            'description' => "Warehouse Transfer {$this->transfer_number} saved."
        ]);

        session()->flash('success', 'Warehouse Transfer saved successfully.');
        $this->closeModal();
    }

    private function processTransferMovement($trf)
    {
        foreach ($trf->items as $item) {
            // Source decrement
            $sourceStock = StockItem::firstOrCreate([
                'product_id' => $item->product_id,
                'warehouse_id' => $trf->source_warehouse_id,
            ]);
            $sourceStock->qty_on_hand -= $item->qty;
            $sourceStock->save();

            // Destination increment
            $destStock = StockItem::firstOrCreate([
                'product_id' => $item->product_id,
                'warehouse_id' => $trf->destination_warehouse_id,
            ]);
            $destStock->qty_on_hand += $item->qty;
            $destStock->save();

            // Log both movements in inventory logs
            Inventory::create([
                'product_id' => $item->product_id,
                'quantity' => $item->qty,
                'type' => 'out',
                'reference_type' => WarehouseTransfer::class,
                'reference_id' => $trf->id,
                'notes' => "Transferred out to WH: {$trf->destinationWarehouse->warehouse_name} via TRF: {$trf->transfer_number}",
            ]);

            Inventory::create([
                'product_id' => $item->product_id,
                'quantity' => $item->qty,
                'type' => 'in',
                'reference_type' => WarehouseTransfer::class,
                'reference_id' => $trf->id,
                'notes' => "Transferred in from WH: {$trf->sourceWarehouse->warehouse_name} via TRF: {$trf->transfer_number}",
            ]);

            // Sync main product counter
            $product = $item->product;
            $product->stock = StockItem::where('product_id', $product->id)->sum('qty_on_hand');
            $product->save();
        }
    }

    public function completeTransfer($id)
    {
        $trf = WarehouseTransfer::with('items')->findOrFail($id);

        // Pre-validate stock levels
        foreach ($trf->items as $item) {
            $stock = StockItem::where('product_id', $item->product_id)
                ->where('warehouse_id', $trf->source_warehouse_id)
                ->first();
            $qtyOnHand = $stock ? floatval($stock->qty_on_hand) : 0;

            if ($qtyOnHand < floatval($item->qty)) {
                $prod = Product::find($item->product_id);
                session()->flash('error', "Cannot complete. Insufficient stock for product {$prod->name} in source warehouse.");
                return;
            }
        }

        $trf->status = 'completed';
        $trf->save();

        $this->processTransferMovement($trf);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Warehouse',
            'action' => 'Complete Warehouse Transfer',
            'description' => "Warehouse Transfer {$trf->transfer_number} marked as completed."
        ]);

        session()->flash('success', 'Transfer completed and stocks successfully updated.');
    }

    public function edit($id)
    {
        $trf = WarehouseTransfer::with('items.product')->findOrFail($id);
        $this->transfer_id = $trf->id;
        $this->transfer_number = $trf->transfer_number;
        $this->source_warehouse_id = $trf->source_warehouse_id;
        $this->destination_warehouse_id = $trf->destination_warehouse_id;
        $this->transfer_date = $trf->transfer_date;
        $this->status = $trf->status;
        $this->notes = $trf->notes;

        $this->items = [];
        foreach ($trf->items as $item) {
            $stock = StockItem::where('product_id', $item->product_id)
                ->where('warehouse_id', $this->source_warehouse_id)
                ->first();
            $this->items[] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'qty' => $item->qty,
                'available_qty' => $stock ? floatval($stock->qty_on_hand) : 0,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $trf = WarehouseTransfer::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Warehouse',
            'action' => 'Delete Warehouse Transfer',
            'description' => "Warehouse Transfer {$trf->transfer_number} deleted."
        ]);

        $trf->delete();
        session()->flash('success', 'Warehouse Transfer deleted successfully.');
    }

    public function render()
    {
        $query = WarehouseTransfer::with(['sourceWarehouse', 'destinationWarehouse']);

        if ($this->search) {
            $query->where('transfer_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.warehouse-transfer-manager', [
            'transfers' => $query->orderBy('transfer_date', 'desc')->paginate(10),
            'warehouses' => Warehouse::orderBy('warehouse_name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
