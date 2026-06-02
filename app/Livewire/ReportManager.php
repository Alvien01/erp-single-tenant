<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\StockItem;
use App\Models\Product;
use App\Models\Payroll;
use App\Models\Account;
use App\Models\Journal;
use Illuminate\Support\Facades\DB;

class ReportManager extends Component
{
    public $activeTab = 'sales';

    // Filters
    public $start_date;
    public $end_date;
    public $warehouse_id;

    public function mount()
    {
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
    }

    public function getSalesData()
    {
        return Sale::with('customer')
            ->whereBetween('sale_date', [$this->start_date, $this->end_date])
            ->where('status', 'delivered')
            ->orderBy('sale_date', 'desc')
            ->get();
    }

    public function getTopProducts()
    {
        return DB::table('sale_items')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->join('products', 'products.id', '=', 'sale_items.product_id')
            ->select('products.name', DB::raw('SUM(sale_items.quantity) as total_qty'), DB::raw('SUM(sale_items.total_price) as total_revenue'))
            ->whereBetween('sales.sale_date', [$this->start_date, $this->end_date])
            ->where('sales.status', 'delivered')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();
    }

    public function getPurchasesData()
    {
        return Purchase::with('supplier')
            ->whereBetween('purchase_date', [$this->start_date, $this->end_date])
            ->where('status', 'received')
            ->orderBy('purchase_date', 'desc')
            ->get();
    }

    public function getStockData()
    {
        return StockItem::with(['product', 'warehouse'])
            ->orderBy('qty_on_hand', 'desc')
            ->get();
    }

    public function getLowStockData()
    {
        return Product::whereRaw('stock <= min_stock')
            ->orderBy('stock')
            ->get();
    }

    public function getPayrollData()
    {
        // Extract year and month from start_date/end_date to match format 'YYYY-MM'
        $periodStart = date('Y-m', strtotime($this->start_date));
        $periodEnd = date('Y-m', strtotime($this->end_date));

        return Payroll::with('employee')
            ->whereBetween('period', [$periodStart, $periodEnd])
            ->orderBy('period', 'desc')
            ->get();
    }

    public function getProfitLossData()
    {
        // Revenues: delivered sales grand total
        $revenue = Sale::whereBetween('sale_date', [$this->start_date, $this->end_date])
            ->where('status', 'delivered')
            ->sum('grand_total');

        // Cost of Goods Sold / Purchases: received purchases
        $cogs = Purchase::whereBetween('purchase_date', [$this->start_date, $this->end_date])
            ->where('status', 'received')
            ->sum('grand_total');

        // Operating Expenses (Payroll)
        $periodStart = date('Y-m', strtotime($this->start_date));
        $periodEnd = date('Y-m', strtotime($this->end_date));
        $payroll = Payroll::whereBetween('period', [$periodStart, $periodEnd])
            ->sum('total_salary');

        // Other expenses from journals (debits on expense accounts)
        $expenseAccountIds = Account::where('type', 'expense')->pluck('id');
        $otherExpenses = Journal::whereIn('account_id', $expenseAccountIds)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'debit')
            ->sum('amount');

        $totalExpenses = $cogs + $payroll + $otherExpenses;
        $netProfit = $revenue - $totalExpenses;

        return [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'payroll' => $payroll,
            'other_expenses' => $otherExpenses,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
        ];
    }

    public function getBalanceSheetData()
    {
        $assets = Account::where('type', 'asset')->orderBy('code')->get();
        $liabilities = Account::where('type', 'liability')->orderBy('code')->get();
        $equity = Account::where('type', 'equity')->orderBy('code')->get();

        return [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'equity' => $equity,
            'total_assets' => $assets->sum('balance'),
            'total_liabilities' => $liabilities->sum('balance'),
            'total_equity' => $equity->sum('balance'),
        ];
    }

    public function getCashFlowData()
    {
        // Operating cash flows: Cash receipts from sales, Cash paid for purchases & payroll & other expenses.
        $cashAccounts = Account::where('type', 'asset')
            ->where(function($q) {
                $q->where('name', 'like', '%cash%')
                  ->orWhere('name', 'like', '%bank%')
                  ->orWhere('name', 'like', '%kas%')
                  ->orWhere('code', 'like', '11%');
            })
            ->pluck('id');

        $receipts = Journal::whereIn('account_id', $cashAccounts)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'debit')
            ->sum('amount');

        $payments = Journal::whereIn('account_id', $cashAccounts)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'credit')
            ->sum('amount');

        $netOperating = $receipts - $payments;

        // Investing cash flows (Equipment/Assets acquisitions)
        $investingAccounts = Account::where('type', 'asset')
            ->whereNotIn('id', $cashAccounts)
            ->pluck('id');

        $investingOut = Journal::whereIn('account_id', $investingAccounts)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'debit')
            ->sum('amount');
        
        $investingIn = Journal::whereIn('account_id', $investingAccounts)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'credit')
            ->sum('amount');

        $netInvesting = $investingIn - $investingOut;

        // Financing cash flows (Equity adjustments)
        $financingAccounts = Account::where('type', 'equity')->pluck('id');
        $financingIn = Journal::whereIn('account_id', $financingAccounts)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'credit')
            ->sum('amount');

        $financingOut = Journal::whereIn('account_id', $financingAccounts)
            ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
            ->where('type', 'debit')
            ->sum('amount');

        $netFinancing = $financingIn - $financingOut;

        $netIncrease = $netOperating + $netInvesting + $netFinancing;

        return [
            'receipts' => $receipts,
            'payments' => $payments,
            'net_operating' => $netOperating,
            'investing_in' => $investingIn,
            'investing_out' => $investingOut,
            'net_investing' => $netInvesting,
            'financing_in' => $financingIn,
            'financing_out' => $financingOut,
            'net_financing' => $netFinancing,
            'net_increase' => $netIncrease,
        ];
    }

    public function getTrialBalanceData()
    {
        $accounts = Account::orderBy('code')->get();
        $totalDebit = 0;
        $totalCredit = 0;
        $lines = [];

        foreach ($accounts as $acc) {
            $debit = 0;
            $credit = 0;

            if (in_array($acc->type, ['asset', 'expense'])) {
                if ($acc->balance >= 0) {
                    $debit = $acc->balance;
                } else {
                    $credit = abs($acc->balance);
                }
            } else {
                if ($acc->balance >= 0) {
                    $credit = $acc->balance;
                } else {
                    $debit = abs($acc->balance);
                }
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $lines[] = [
                'code' => $acc->code,
                'name' => $acc->name,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return [
            'lines' => $lines,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
        ];
    }

    public function getAgingData()
    {
        // AR Aging based on unpaid Sales Orders
        $sales = Sale::where('status', '!=', 'paid')->get();
        $ar = [
            'current' => 0,
            'thirty' => 0,
            'sixty' => 0,
            'ninety' => 0,
            'over_ninety' => 0,
            'total' => 0,
        ];

        foreach ($sales as $sale) {
            $days = now()->diffInDays(\Carbon\Carbon::parse($sale->sale_date), false);
            $days = abs($days);
            $amt = $sale->grand_total;

            if ($days <= 30) {
                $ar['current'] += $amt;
            } elseif ($days <= 60) {
                $ar['thirty'] += $amt;
            } elseif ($days <= 90) {
                $ar['sixty'] += $amt;
            } else {
                $ar['over_ninety'] += $amt;
            }
            $ar['total'] += $amt;
        }

        // AP Aging based on unpaid Purchases
        $purchases = Purchase::where('status', '!=', 'paid')->get();
        $ap = [
            'current' => 0,
            'thirty' => 0,
            'sixty' => 0,
            'ninety' => 0,
            'over_ninety' => 0,
            'total' => 0,
        ];

        foreach ($purchases as $p) {
            $days = now()->diffInDays(\Carbon\Carbon::parse($p->purchase_date), false);
            $days = abs($days);
            $amt = $p->grand_total;

            if ($days <= 30) {
                $ap['current'] += $amt;
            } elseif ($days <= 60) {
                $ap['thirty'] += $amt;
            } elseif ($days <= 90) {
                $ap['sixty'] += $amt;
            } else {
                $ap['over_ninety'] += $amt;
            }
            $ap['total'] += $amt;
        }

        return [
            'ar' => $ar,
            'ap' => $ap,
        ];
    }

    public function render()
    {
        return view('livewire.report-manager', [
            'sales' => $this->getSalesData(),
            'topProducts' => $this->getTopProducts(),
            'purchases' => $this->getPurchasesData(),
            'stocks' => $this->getStockData(),
            'lowStocks' => $this->getLowStockData(),
            'payrolls' => $this->getPayrollData(),
            'pl' => $this->getProfitLossData(),
            'bs' => $this->getBalanceSheetData(),
            'cf' => $this->getCashFlowData(),
            'tb' => $this->getTrialBalanceData(),
            'aging' => $this->getAgingData(),
        ]);
    }
}
