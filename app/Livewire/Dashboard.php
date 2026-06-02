<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\StockItem;
use App\Models\Warehouse;
use App\Models\Employee;
use App\Models\ActivityLog;
use App\Models\DeliveryOrder;
use App\Models\Approval;
use App\Models\EmployeeSchedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class Dashboard extends Component
{
    public string $datePreset   = 'this_month';
    public string $customStart  = '';
    public string $customEnd    = '';
    public bool   $showDatePicker = false;

    protected $queryString = ['datePreset', 'customStart', 'customEnd'];

    public function mount(): void
    {
        if ($this->datePreset === 'custom') {
            $this->customStart = $this->customStart ?: now()->startOfMonth()->toDateString();
            $this->customEnd   = $this->customEnd   ?: now()->toDateString();
        }
    }

    // Apply preset and close picker
    public function applyPreset(string $preset): void
    {
        $this->datePreset    = $preset;
        $this->showDatePicker = false;

        if ($preset === 'custom') {
            $this->showDatePicker = true;
        }
    }

    // Apply custom range
    public function applyCustomRange(): void
    {
        $this->datePreset    = 'custom';
        $this->showDatePicker = false;
    }

    // Compute Carbon date range from current preset
    protected function getDateRange(): array
    {
        $now = Carbon::now();

        // Financial year: April 1 – March 31
        $currentFYStart = $now->month >= 4
            ? Carbon::create($now->year, 4, 1)->startOfDay()
            : Carbon::create($now->year - 1, 4, 1)->startOfDay();
        $currentFYEnd = $currentFYStart->copy()->addYear()->subDay()->endOfDay();

        $lastFYStart = $currentFYStart->copy()->subYear();
        $lastFYEnd   = $currentFYStart->copy()->subDay()->endOfDay();

        return match ($this->datePreset) {
            'today'              => [$now->copy()->startOfDay(),            $now->copy()->endOfDay()],
            'yesterday'          => [$now->copy()->subDay()->startOfDay(),  $now->copy()->subDay()->endOfDay()],
            'last_7_days'        => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            'last_30_days'       => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
            'this_month'         => [$now->copy()->startOfMonth(),          $now->copy()->endOfMonth()],
            'last_month'         => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_month_ly'      => [$now->copy()->subYear()->startOfMonth(), $now->copy()->subYear()->endOfMonth()],
            'this_year'          => [$now->copy()->startOfYear(),           $now->copy()->endOfYear()],
            'last_year'          => [$now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()],
            'current_fy'         => [$currentFYStart,                      $currentFYEnd],
            'last_fy'            => [$lastFYStart,                         $lastFYEnd],
            'custom'             => [
                Carbon::parse($this->customStart)->startOfDay(),
                Carbon::parse($this->customEnd)->endOfDay(),
            ],
            default              => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    // Human-readable label for current range
    public function getDateLabelProperty(): string
    {
        [$start, $end] = $this->getDateRange();
        $labels = [
            'today'         => 'Today',
            'yesterday'     => 'Yesterday',
            'last_7_days'   => 'Last 7 Days',
            'last_30_days'  => 'Last 30 Days',
            'this_month'    => 'This Month (' . now()->format('F Y') . ')',
            'last_month'    => 'Last Month (' . now()->subMonth()->format('F Y') . ')',
            'this_month_ly' => 'This Month Last Year (' . now()->subYear()->format('F Y') . ')',
            'this_year'     => 'This Year (' . now()->year . ')',
            'last_year'     => 'Last Year (' . (now()->year - 1) . ')',
            'current_fy'    => 'Current Financial Year',
            'last_fy'       => 'Last Financial Year',
            'custom'        => $start->format('d M Y') . ' – ' . $end->format('d M Y'),
        ];
        return $labels[$this->datePreset] ?? 'Custom Range';
    }

    // Build 6-bar trend aligned to selected range
    protected function buildTrend(Carbon $start, Carbon $end): array
    {
        $diff   = $start->diffInDays($end);
        $trend  = [];

        if ($diff <= 14) {
            // Day-by-day for short ranges
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $label = $cursor->format('d M');
                $trend[$label] = Sale::whereDate('sale_date', $cursor->toDateString())->sum('grand_total') ?? 0;
                $cursor->addDay();
                if (count($trend) >= 7) break; // cap at 7 bars
            }
        } elseif ($diff <= 92) {
            // Week buckets
            $cursor = $start->copy()->startOfWeek();
            for ($i = 0; $i < 6; $i++) {
                $wStart = $cursor->copy();
                $wEnd   = $cursor->copy()->endOfWeek();
                $label  = 'W' . $cursor->isoWeek();
                $trend[$label] = Sale::whereBetween('sale_date', [$wStart->toDateString(), $wEnd->toDateString()])->sum('grand_total') ?? 0;
                $cursor->addWeek();
                if ($cursor->gt($end)) break;
            }
        } else {
            // Month buckets (6 bars)
            for ($i = 5; $i >= 0; $i--) {
                $date  = $end->copy()->subMonths($i);
                $label = $date->format('M y');
                $trend[$label] = Sale::whereYear('sale_date', $date->year)
                    ->whereMonth('sale_date', $date->month)
                    ->sum('grand_total') ?? 0;
            }
        }

        return $trend;
    }

    // Get Sales Last 30 Days Data
    public function getSalesLast30DaysProperty(): array
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $label = $date->format('d M');
            $sales = Sale::whereDate('sale_date', $date->toDateString())->sum('grand_total') ?? 0;
            $data[$label] = $sales;
        }
        return $data;
    }

    // Get Sales Current Financial Year Data
    public function getSalesCurrentFYProperty(): array
    {
        $now = Carbon::now();
        $fyStart = $now->month >= 4 
            ? Carbon::create($now->year, 4, 1) 
            : Carbon::create($now->year - 1, 4, 1);
        
        $data = [];
        for ($i = 0; $i < 12; $i++) {
            $date = $fyStart->copy()->addMonths($i);
            if ($date->gt($now)) break;
            
            $label = $date->format('M Y');
            $sales = Sale::whereYear('sale_date', $date->year)
                ->whereMonth('sale_date', $date->month)
                ->sum('grand_total') ?? 0;
            $data[$label] = $sales;
        }
        
        return $data;
    }

    public function render()
    {
        $user = Auth::user() ?? (object)['role' => 'admin'];
        $role = $user->role ?? 'admin';

        [$start, $end] = $this->getDateRange();

        $totalSales = Sale::whereBetween('sale_date', [$start->toDateString(), $end->toDateString()])
            ->sum('grand_total') ?? 0;

        $totalPurchases = Purchase::whereBetween('created_at', [$start, $end])
            ->sum('grand_total') ?? 0;

        $recentSales = Sale::with('customer')
            ->whereBetween('sale_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('sale_date', 'desc')
            ->take(5)
            ->get();

        return view('livewire.dashboard', [
            'userRole'         => $role,
            'totalSales'       => $totalSales,
            'totalPurchases'   => $totalPurchases,
            'stockCount'       => StockItem::sum('qty_on_hand') ?? 0,
            'warehouseCount'   => Warehouse::count(),
            'employeeCount'    => Employee::where('status', 'active')->count() ?: 12,
            'recentSales'      => $recentSales,
            'activities'       => ActivityLog::orderBy('created_at', 'desc')->take(5)->get(),
            'pendingDeliveries'=> DeliveryOrder::whereIn('status', ['draft', 'ready'])->count(),
            'pendingApprovals' => Approval::where('status', 'pending')->count(),
            'activeSchedules'  => EmployeeSchedule::whereDate('date', '>=', now()->toDateString())->count(),
            'salesTrend'       => $this->buildTrend($start, $end),
            'dateStart'        => $start,
            'dateEnd'          => $end,
            'salesLast30Days'  => $this->salesLast30Days,
            'salesCurrentFY'   => $this->salesCurrentFY,
        ]);
    }
}