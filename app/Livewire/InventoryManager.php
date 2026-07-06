<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockItem;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\ActivityLog;

class InventoryManager extends Component
{
    use WithPagination;

    public $search = '';
    public $warehouse_id = '';
    
    // Tab switching (dashboard, stock, adjustments)
    public $activeTab = 'dashboard';

    // Adjustment Form fields
    public $adj_warehouse_id;
    public $adj_type = 'opname';
    public $adj_items = []; // array of ['product_id', 'system_qty', 'actual_qty']

    public $isOpen = false;

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetAdjustmentFields();
    }

    public function resetAdjustmentFields()
    {
        $this->adj_warehouse_id = '';
        $this->adj_type = 'opname';
        $this->adj_items = [];
    }

    public function createAdjustment()
    {
        $this->resetAdjustmentFields();
        $this->addAdjustmentItem();
        $this->openModal();
    }

    public function addAdjustmentItem()
    {
        $this->adj_items[] = [
            'product_id' => '',
            'system_qty' => 0,
            'actual_qty' => 0,
            'difference' => 0
        ];
    }

    public function removeAdjustmentItem($index)
    {
        unset($this->adj_items[$index]);
        $this->adj_items = array_values($this->adj_items);
    }

    public function updatedAdjItems($value, $name)
    {
        $parts = explode('.', $name);
        if (count($parts) === 3) {
            $index = $parts[1];
            $field = $parts[2];

            if ($field === 'product_id' && $value) {
                $stock = StockItem::where('product_id', $value)
                    ->where('warehouse_id', $this->adj_warehouse_id)
                    ->first();
                $this->adj_items[$index]['system_qty'] = $stock ? $stock->qty_on_hand : 0;
            }

            $sys = floatval($this->adj_items[$index]['system_qty'] ?? 0);
            $act = floatval($this->adj_items[$index]['actual_qty'] ?? 0);
            $this->adj_items[$index]['difference'] = $act - $sys;
        }
    }

    public function storeAdjustment()
    {
        $this->validate([
            'adj_warehouse_id' => 'required|exists:warehouses,id',
            'adj_type' => 'required',
            'adj_items.*.product_id' => 'required|exists:products,id',
            'adj_items.*.actual_qty' => 'required|numeric|min:0',
        ]);

        $adj = StockAdjustment::create([
            'adj_number' => 'ADJ-' . now()->format('Ymd') . '-' . sprintf('%04d', StockAdjustment::count() + 1),
            'warehouse_id' => $this->adj_warehouse_id,
            'adjusted_by' => auth()->id() ?? 1,
            'type' => $this->adj_type,
        ]);

        foreach ($this->adj_items as $item) {
            $diff = floatval($item['difference']);
            
            StockAdjustmentItem::create([
                'stock_adjustment_id' => $adj->id,
                'product_id' => $item['product_id'],
                'system_qty' => $item['system_qty'],
                'actual_qty' => $item['actual_qty'],
                'difference' => $diff,
            ]);

            $stock = StockItem::firstOrCreate([
                'product_id' => $item['product_id'],
                'warehouse_id' => $this->adj_warehouse_id,
            ]);
            $stock->qty_on_hand = $item['actual_qty'];
            $stock->save();

            $product = Product::find($item['product_id']);
            if ($product) {
                $product->stock = StockItem::where('product_id', $item['product_id'])->sum('qty_on_hand');
                $product->save();
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Inventory',
            'action' => 'Create Stock Adjustment',
            'description' => 'Stock adjustment ' . $adj->adj_number . ' completed for warehouse ' . Warehouse::find($this->adj_warehouse_id)->warehouse_name
        ]);

        session()->flash('success', 'Stock adjustment completed successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $stockQuery = StockItem::with(['product', 'warehouse']);
        if ($this->search) {
            $stockQuery->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->warehouse_id) {
            $stockQuery->where('warehouse_id', $this->warehouse_id);
        }

        $adjustments = StockAdjustment::with(['warehouse', 'adjustedBy'])->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.inventory-manager', [
            'stocks' => $stockQuery->paginate(10),
            'warehouses' => Warehouse::all(),
            'adjustments' => $adjustments,
            'products' => Product::all(),
            'stats' => $this->getDashboardStats(),
        ]);
    }

    public function getDashboardStats()
    {
        // Low stock items
        $lowStock = StockItem::with(['product', 'warehouse'])
            ->where('qty_on_hand', '<', 15)
            ->orderBy('qty_on_hand', 'asc')
            ->take(5)
            ->get();

        // Total valuation by warehouse
        $valuationByWarehouse = StockItem::select('warehouse_id', \DB::raw('sum(qty_on_hand) as total_qty'))
            ->with('warehouse')
            ->groupBy('warehouse_id')
            ->get();

        return [
            'totalStockQty' => StockItem::sum('qty_on_hand') ?? 0,
            'totalValuation' => \App\Models\StockValuation::sum('total_value') ?? 0,
            'pendingReceipts' => \App\Models\GoodReceipt::where('status', 'draft')->count(),
            'pendingTransfers' => \App\Models\WarehouseTransfer::whereIn('status', ['draft', 'ready'])->count(),
            'lowStock' => $lowStock,
            'valuationByWarehouse' => $valuationByWarehouse,
            'recentAdjustments' => StockAdjustment::with(['warehouse', 'adjustedBy'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
        ];
    }
}
