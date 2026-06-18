<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\StockItem;
use App\Models\Warehouse;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
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

    // Attendance properties
    public $currentEmployeeId = null;
    public $currentEmployee = null;
    public $isAdmin = false;
    public $att_filter_date;

    protected $queryString = ['datePreset', 'customStart', 'customEnd'];

    public function mount(): void
    {
        if ($this->datePreset === 'custom') {
            $this->customStart = $this->customStart ?: now()->startOfMonth()->toDateString();
            $this->customEnd   = $this->customEnd   ?: now()->toDateString();
        }

        $this->att_filter_date = now()->format('Y-m-d');
        $this->checkAdminRole();
        $this->loadOrCreateCurrentEmployee();
    }

    private function checkAdminRole()
    {
        $user = Auth::user();
        if (!$user) {
            $this->isAdmin = false;
            return;
        }
        $this->isAdmin = isset($user->role) && $user->role === 'admin';
    }

    private function loadOrCreateCurrentEmployee()
    {
        $user = Auth::user();
        if (!$user) return;

        $employee = Employee::where('user_id', $user->id)->first();
        if (!$employee) {
            $employee = Employee::where('email', $user->email)->first();
        }
        if (!$employee) {
            $lastEmployee = Employee::orderBy('id', 'desc')->first();
            $nextNumber = $lastEmployee ? intval(substr($lastEmployee->employee_number, -3)) + 1 : 1;
            $employeeNumber = 'EMP-' . now()->format('Ymd') . '-' . sprintf('%03d', $nextNumber);

            $employee = Employee::create([
                'user_id' => $user->id,
                'employee_number' => $employeeNumber,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'position' => 'Staff',
                'department' => 'General',
                'join_date' => now()->format('Y-m-d'),
                'salary' => 0,
                'status' => 'active',
                'transport_allowance' => 30000,
                'meal_allowance' => 25000,
            ]);
        }

        if ($employee) {
            $this->currentEmployeeId = $employee->id;
            $this->currentEmployee = $employee;
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Attendance Clock-In / Clock-Out
    // ──────────────────────────────────────────────────────────────────

    public function clockIn($latitude, $longitude, $address = '')
    {
        if (!Auth::check()) {
            session()->flash('att_error', 'Silakan login terlebih dahulu.');
            return;
        }

        if (!$this->currentEmployeeId) {
            $this->loadOrCreateCurrentEmployee();
            if (!$this->currentEmployeeId) {
                session()->flash('att_error', 'Gagal membuat akun karyawan. Silakan hubungi admin.');
                return;
            }
        }

        $employee = Employee::findOrFail($this->currentEmployeeId);
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        $existing = Attendance::where('employee_id', $this->currentEmployeeId)
            ->where('date', $today)
            ->first();

        if ($existing && $existing->check_in) {
            session()->flash('att_error', 'Anda sudah melakukan clock-in hari ini.');
            return;
        }

        $setting = AttendanceSetting::where('is_active', true)->first();
        $distance = null;
        $status = 'present';
        $notes = '';

        if ($setting) {
            try {
                $workStartTime = $setting->work_start_time ?? $setting->work_start ?? '08:00:00';
                $workStart = $this->parseWorkTime($workStartTime);

                $earlyMinutes = $setting->early_checkin_minutes ?? $setting->early_checkin ?? 60;
                $earlyLimit = $workStart->copy()->subMinutes((int)$earlyMinutes);

                $lateMinutes = $setting->late_tolerance_minutes ?? $setting->late_tolerance ?? 15;
                $lateLimit = $workStart->copy()->addMinutes((int)$lateMinutes);

                $now = Carbon::now();

                if ($now->lt($earlyLimit)) {
                    session()->flash('att_error', 'Belum bisa clock-in. Waktu clock-in dimulai pukul ' . $earlyLimit->format('H:i') . '.');
                    return;
                }

                if ($now->gt($lateLimit)) {
                    $status = 'late';
                    $notes = 'Terlambat ' . $now->diffInMinutes($workStart) . ' menit';
                }

                $requireLocation = $setting->require_location ?? true;
                if ($requireLocation && $latitude && $longitude) {
                    $officeLat = $setting->office_latitude ?? $setting->latitude ?? 0;
                    $officeLng = $setting->office_longitude ?? $setting->longitude ?? 0;

                    if ($officeLat != 0 && $officeLng != 0) {
                        $distance = AttendanceSetting::calculateDistance(
                            (float)$latitude, (float)$longitude,
                            (float)$officeLat, (float)$officeLng
                        );

                        $maxRadius = $setting->allowed_radius ?? $setting->radius ?? 200;
                        if ($distance > $maxRadius) {
                            session()->flash('att_error', 'Lokasi Anda terlalu jauh dari kantor (' . round($distance) . 'm). Jarak maksimum: ' . $maxRadius . 'm.');
                            return;
                        }
                    }
                } elseif ($requireLocation && (!$latitude || !$longitude)) {
                    session()->flash('att_error', 'Lokasi GPS diperlukan untuk clock-in. Aktifkan GPS pada perangkat Anda.');
                    return;
                }
            } catch (\Exception $e) {
                \Log::error('Clock-in time parsing error: ' . $e->getMessage());
            }
        }

        Attendance::updateOrCreate(
            ['employee_id' => $this->currentEmployeeId, 'date' => $today],
            [
                'check_in' => $currentTime,
                'check_in_latitude' => $latitude ?: null,
                'check_in_longitude' => $longitude ?: null,
                'check_in_distance' => $distance,
                'check_in_address' => $address ?: null,
                'status' => $status,
                'notes' => $notes ?: null,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'HR',
            'action' => 'Clock In',
            'description' => $employee->name . ' clock-in at ' . $currentTime . ($distance ? ' (Distance: ' . round($distance) . 'm)' : ''),
        ]);

        session()->flash('att_success', 'Clock-in berhasil pada pukul ' . Carbon::parse($currentTime)->format('H:i') . ($status === 'late' ? ' (TERLAMBAT)' : '') . '.');
    }

    public function clockOut($latitude, $longitude, $address = '')
    {
        if (!Auth::check()) {
            session()->flash('att_error', 'Silakan login terlebih dahulu.');
            return;
        }

        if (!$this->currentEmployeeId) {
            session()->flash('att_error', 'Anda belum melakukan clock-in hari ini.');
            return;
        }

        $employee = Employee::findOrFail($this->currentEmployeeId);
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        $attendance = Attendance::where('employee_id', $this->currentEmployeeId)
            ->where('date', $today)->first();

        if (!$attendance || !$attendance->check_in) {
            session()->flash('att_error', 'Anda belum clock-in hari ini.');
            return;
        }

        if ($attendance->check_out) {
            session()->flash('att_error', 'Anda sudah clock-out hari ini.');
            return;
        }

        $setting = AttendanceSetting::where('is_active', true)->first();
        $distance = null;

        if ($setting && $setting->require_location && $latitude && $longitude) {
            $distance = AttendanceSetting::calculateDistance(
                $latitude, $longitude,
                $setting->office_latitude, $setting->office_longitude
            );
        }

        $attendance->update([
            'check_out' => $currentTime,
            'check_out_latitude' => $latitude,
            'check_out_longitude' => $longitude,
            'check_out_distance' => $distance,
            'check_out_address' => $address ?: null,
        ]);

        $checkIn = Carbon::parse($attendance->check_in);
        $checkOut = Carbon::parse($currentTime);
        $duration = $checkIn->diff($checkOut);
        $durationStr = $duration->format('%H jam %I menit');

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'HR',
            'action' => 'Clock Out',
            'description' => $employee->name . ' clock-out at ' . $currentTime . ' (Durasi kerja: ' . $durationStr . ')',
        ]);

        session()->flash('att_success', 'Clock-out berhasil pada pukul ' . Carbon::parse($currentTime)->format('H:i') . '. Durasi kerja: ' . $durationStr . '.');
    }

    private function parseWorkTime($time)
    {
        $time = trim($time);
        try {
            return Carbon::createFromFormat('H:i:s', $time);
        } catch (\Exception $e1) {
            try {
                return Carbon::createFromFormat('H:i', $time);
            } catch (\Exception $e2) {
                return Carbon::parse($time);
            }
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

        // ── Attendance data ──────────────────────────────────────────
        $today = now()->format('Y-m-d');
        $attendanceSetting = AttendanceSetting::where('is_active', true)->first();

        $todayAttendances = Attendance::with('employee')->where('date', $today)->get();
        $attPresent = $todayAttendances->where('status', 'present')->count();
        $attLate    = $todayAttendances->where('status', 'late')->count();
        $attAbsent  = Employee::where('status', 'active')->count() - $todayAttendances->count();
        $attAbsent  = max(0, $attAbsent);

        $myAttendance = null;
        if ($this->currentEmployeeId) {
            $myAttendance = Attendance::where('employee_id', $this->currentEmployeeId)
                ->where('date', $today)->first();
        }

        // Recent attendances (last 10 entries for today)
        $recentAttendances = Attendance::with('employee')
            ->where('date', $today)
            ->orderBy('check_in', 'desc')
            ->take(10)
            ->get();

        return view('livewire.dashboard', [
            'userRole'           => $role,
            'totalSales'         => $totalSales,
            'totalPurchases'     => $totalPurchases,
            'stockCount'         => StockItem::sum('qty_on_hand') ?? 0,
            'warehouseCount'     => Warehouse::count(),
            'employeeCount'      => Employee::where('status', 'active')->count() ?: 12,
            'recentSales'        => $recentSales,
            'activities'         => ActivityLog::orderBy('created_at', 'desc')->take(5)->get(),
            'pendingDeliveries'  => DeliveryOrder::whereIn('status', ['draft', 'ready'])->count(),
            'pendingApprovals'   => Approval::where('status', 'pending')->count(),
            'activeSchedules'    => EmployeeSchedule::whereDate('date', '>=', now()->toDateString())->count(),
            'salesTrend'         => $this->buildTrend($start, $end),
            'dateStart'          => $start,
            'dateEnd'            => $end,
            'salesLast30Days'    => $this->salesLast30Days,
            'salesCurrentFY'     => $this->salesCurrentFY,
            // Attendance data
            'attendanceSetting'  => $attendanceSetting,
            'attPresent'         => $attPresent,
            'attLate'            => $attLate,
            'attAbsent'          => $attAbsent,
            'attTotal'           => $todayAttendances->count(),
            'myAttendance'       => $myAttendance,
            'currentEmployee'    => $this->currentEmployee,
            'recentAttendances'  => $recentAttendances,
        ]);
    }
}