<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\StockItem;
use App\Models\Warehouse;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class BarcodeScanner extends Component
{
    public $scanInput = '';
    public $operation = 'intake'; // intake, count
    public $selectedWarehouseId = '';
    public $scannedItems = [];

    // Form fields for simulated trigger
    public $simulatedProductCode = '';

    public function mount()
    {
        $wh = Warehouse::first();
        if ($wh) {
            $this->selectedWarehouseId = $wh->id;
        }
    }

    public function processScan()
    {
        $code = trim($this->scanInput);
        if (empty($code)) {
            return;
        }

        $this->scanCode($code);
        $this->scanInput = ''; // Clear for next scan
    }

    public function triggerSimulatedScan()
    {
        if (empty($this->simulatedProductCode)) {
            session()->flash('scan_error', 'Please select a product to simulate scan!');
            return;
        }

        $this->scanCode($this->simulatedProductCode);
        $this->simulatedProductCode = ''; // Clear
    }

    private function scanCode($code)
    {
        $product = Product::where('code', $code)->first();

        if (!$product) {
            session()->flash('scan_error', "Unknown Barcode / SKU Code: '{$code}'!");
            return;
        }

        // Process based on operation
        if ($this->operation === 'intake') {
            // Good Receipt / Intake increments inventory stock
            $product->increment('stock', 1);

            // Increment specific Warehouse StockItem if it exists
            if ($this->selectedWarehouseId) {
                $stockItem = StockItem::firstOrCreate([
                    'product_id' => $product->id,
                    'warehouse_id' => $this->selectedWarehouseId,
                ], [
                    'qty_on_hand' => 0,
                    'qty_reserved' => 0,
                ]);
                $stockItem->increment('qty_on_hand', 1);
            }

            $actionText = 'Good Receipt Intake (+1 Qty)';
        } else {
            // Count / Verification only checks values
            $actionText = 'Physical Stock Verification';
        }

        // Add to live scanned list
        $timestamp = now()->format('H:i:s');
        array_unshift($this->scannedItems, [
            'code' => $product->code,
            'name' => $product->name,
            'unit' => $product->unit,
            'price' => $product->price,
            'timestamp' => $timestamp,
            'action' => $actionText,
            'current_stock' => $product->stock,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Warehouse',
            'action' => 'barcode_scan',
            'description' => "Scanned product code: {$code} ({$product->name}) for {$this->operation}",
            'ip_address' => request()->ip(),
        ]);

        session()->flash('scan_success', "Successfully scanned '{$product->name}'!");
    }

    public function clearScans()
    {
        $this->scannedItems = [];
        session()->flash('scan_success', 'Scan list cleared.');
    }

    public function render()
    {
        $warehouses = Warehouse::orderBy('warehouse_name')->get();
        $products = Product::orderBy('name')->get();

        return view('livewire.barcode-scanner', [
            'warehouses' => $warehouses,
            'products' => $products,
        ])->layout('layouts.app');
    }
}
