<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\StockValuation;
use App\Models\LandedCost;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockValuationManager extends Component
{
    public $activeTab = 'valuation'; // valuation, landed_costs
    
    // Landed Costs Form
    public $landed_cost_id;
    public $landed_cost_number;
    public $description;
    public $total_amount;
    public $purchase_id;
    
    public $isOpen = false;

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function createLandedCost()
    {
        $this->landed_cost_id = null;
        $this->landed_cost_number = 'LC-' . now()->format('Ymd') . '-' . sprintf('%04d', LandedCost::query()->count() + 1);
        $this->description = '';
        $this->total_amount = 0;
        $this->purchase_id = '';
        
        $this->isOpen = true;
    }

    public function applyLandedCost()
    {
        $this->validate([
            'landed_cost_number' => 'required|string|unique:landed_costs,landed_cost_number,' . $this->landed_cost_id,
            'total_amount' => 'required|numeric|min:0.01',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        DB::transaction(function () {
            // Save Landed Cost
            $lc = LandedCost::query()->updateOrCreate(['id' => $this->landed_cost_id], [
                'landed_cost_number' => $this->landed_cost_number,
                'description' => $this->description,
                'total_amount' => $this->total_amount,
                'purchase_id' => $this->purchase_id,
                'status' => 'applied',
            ]);

            // Load all items received under this Purchase Order
            $purchase = Purchase::query()->with('items')->findOrFail($this->purchase_id);
            $totalPurchaseQty = $purchase->items->sum('quantity');

            if ($totalPurchaseQty > 0) {
                // Distribute Landed Cost proportionally to each item's stock valuation
                $costPerUnit = $this->total_amount / $totalPurchaseQty;

                foreach ($purchase->items as $item) {
                    // Update Stock Valuation records corresponding to this GoodReceipt/Purchase
                    StockValuation::query()->where('product_id', $item->product_id)
                        ->where('reference_type', 'GoodReceipt')
                        ->increment('unit_cost', $costPerUnit);

                    StockValuation::query()->where('product_id', $item->product_id)
                        ->where('reference_type', 'GoodReceipt')
                        ->each(function ($sv) {
                            $sv->update(['total_value' => $sv->quantity * $sv->unit_cost]);
                        });
                }
            }

            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'Inventory',
                'action' => 'Apply Landed Costs',
                'description' => "Applied Landed Cost {$this->landed_cost_number} to Purchase Order #{$this->purchase_id}."
            ]);
        });

        session()->flash('success', 'Landed cost applied to stock valuation successfully.');
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.stock-valuation-manager', [
            'valuations' => StockValuation::query()->with('product')->orderByDesc('id')->paginate(15),
            'landedCosts' => LandedCost::query()->with('purchase')->orderByDesc('id')->get(),
            'purchases' => Purchase::query()->where('status', 'received')->get(),
        ]);
    }
}
