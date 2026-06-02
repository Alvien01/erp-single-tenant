<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PosTransaction;
use App\Models\PosSession;
use App\Models\Store;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;

class PosReportManager extends Component
{
    public $dateFrom;
    public $dateTo;
    public $storeFilter = '';
    public $activeTab = 'overview'; // overview | transactions | products | pnl | eod

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function render()
    {
        $query = PosTransaction::query()
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
            ->when($this->storeFilter, fn($q) => $q->where('store_id', $this->storeFilter));

        // Overview stats
        $totalRevenue = (clone $query)->sum('grand_total');
        $totalTransactions = (clone $query)->count();
        $totalDiscount = (clone $query)->sum('discount_amount');
        $totalTax = (clone $query)->sum('tax_amount');
        $avgTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Payment method breakdown
        $paymentBreakdown = (clone $query)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        // Daily revenue for chart
        $dailyRevenue = (clone $query)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'), DB::raw('COUNT(*) as trx_count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products
        $topProducts = DB::table('pos_transaction_items')
            ->join('pos_transactions', 'pos_transactions.id', '=', 'pos_transaction_items.pos_transaction_id')
            ->where('pos_transactions.status', 'completed')
            ->whereBetween('pos_transactions.created_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
            ->when($this->storeFilter, fn($q) => $q->where('pos_transactions.store_id', $this->storeFilter))
            ->select('pos_transaction_items.product_name', 
                DB::raw('SUM(pos_transaction_items.quantity) as total_qty'),
                DB::raw('SUM(pos_transaction_items.subtotal) as total_revenue'))
            ->groupBy('pos_transaction_items.product_name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Recent transactions
        $transactions = (clone $query)
            ->with(['user', 'member', 'store'])
            ->latest()
            ->paginate(20);

        // EOD Sessions
        $sessions = PosSession::query()
            ->with(['user', 'store'])
            ->whereBetween('opened_at', [$this->dateFrom . ' 00:00:00', $this->dateTo . ' 23:59:59'])
            ->when($this->storeFilter, fn($q) => $q->where('store_id', $this->storeFilter))
            ->latest()
            ->paginate(15);

        // Simple P&L
        $grossRevenue = $totalRevenue;
        $cogs = $grossRevenue * 0.6; // Simplified: 60% COGS assumption
        $grossProfit = $grossRevenue - $cogs;

        return view('livewire.pos-report-manager', [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalDiscount' => $totalDiscount,
            'totalTax' => $totalTax,
            'avgTransaction' => $avgTransaction,
            'paymentBreakdown' => $paymentBreakdown,
            'dailyRevenue' => $dailyRevenue,
            'topProducts' => $topProducts,
            'transactions' => $transactions,
            'sessions' => $sessions,
            'grossRevenue' => $grossRevenue,
            'cogs' => $cogs,
            'grossProfit' => $grossProfit,
            'stores' => Store::where('is_active', true)->get(),
        ]);
    }
}
