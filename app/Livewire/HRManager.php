<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Payroll;
use App\Models\Department;
use App\Models\Leave;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HRManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'employees';

    // Payroll generation parameters
    public $payroll_month;
    public $payroll_year;
    public $isOpen = false;
    public $modalType = ''; // 'payroll', 'department', 'leave', 'employee', 'attendance-settings'

    // Employee fields
    public $emp_id;
    public $employee_number;
    public $emp_name;
    public $emp_email;
    public $emp_phone;
    public $emp_position;
    public $emp_department;
    public $emp_join_date;
    public $emp_salary = 0;
    public $emp_status = 'active';
    
    // Employee allowance fields
    public $emp_transport_allowance = 30000;
    public $emp_meal_allowance = 25000;

    // Department fields
    public $dept_id;
    public $dept_name;
    public $dept_description;
    public $dept_is_active = true;

    // Leave fields
    public $leave_id;
    public $leave_employee_id;
    public $leave_start_date;
    public $leave_end_date;
    public $leave_type = 'annual';
    public $leave_reason;
    public $leave_status = 'pending';

    // Attendance Clock-In/Out fields
    public $clock_latitude;
    public $clock_longitude;
    public $clock_address = '';

    // Attendance Settings fields
    public $setting_id;
    public $setting_office_name = 'Kantor Pusat';
    public $setting_latitude;
    public $setting_longitude;
    public $setting_radius = 200;
    public $setting_work_start = '08:00';
    public $setting_work_end = '17:00';
    public $setting_late_tolerance = 15;
    public $setting_early_checkin = 60;
    public $setting_require_location = true;

    // Attendance filter
    public $att_filter_date;

    // Current user's employee
    public $currentEmployeeId = null;
    public $currentEmployee = null;

    // Flag untuk cek apakah user adalah admin
    public $isAdmin = false;

    public function mount()
    {
        $this->att_filter_date = now()->format('Y-m-d');
        $this->checkAdminRole();
        $this->loadOrCreateCurrentEmployee();
    }

    /**
     * Check if current user has admin role
     */
    private function checkAdminRole()
    {
        $user = Auth::user();
        if (!$user) {
            $this->isAdmin = false;
            return;
        }

        // Cek role dari user
        // Sesuaikan dengan implementasi role di sistem Anda
        // Misal: menggunakan kolom 'role' atau 'is_admin'
        $this->isAdmin = isset($user->role) && $user->role === 'admin';
        
        // Atau jika menggunakan Spatie Permission:
        // $this->isAdmin = $user->hasRole('admin');
        
        // Atau jika menggunakan kolom is_admin:
        // $this->isAdmin = $user->is_admin ?? false;
    }

    /**
     * Load or create employee record for the currently logged-in user
     */
    private function loadOrCreateCurrentEmployee()
    {
        $user = Auth::user();
        if (!$user) {
            return;
        }

        // Cari employee berdasarkan user_id
        $employee = Employee::where('user_id', $user->id)->first();
        
        if (!$employee) {
            // Coba cari berdasarkan email
            $employee = Employee::where('email', $user->email)->first();
        }

        if (!$employee) {
            // Buat employee baru otomatis untuk user ini
            $employee = $this->createEmployeeFromUser($user);
        }

        if ($employee) {
            $this->currentEmployeeId = $employee->id;
            $this->currentEmployee = $employee;
        }
    }

    /**
     * Create employee automatically from user data
     */
    private function createEmployeeFromUser($user)
    {
        // Generate employee number
        $lastEmployee = Employee::orderBy('id', 'desc')->first();
        $nextNumber = $lastEmployee ? intval(substr($lastEmployee->employee_number, -3)) + 1 : 1;
        $employeeNumber = 'EMP-' . now()->format('Ymd') . '-' . sprintf('%03d', $nextNumber);

        // Buat employee dengan data dari user
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

        // Log aktivitas
        ActivityLog::create([
            'user_id' => $user->id,
            'module' => 'HR',
            'action' => 'Auto Create Employee',
            'description' => 'Employee automatically created for user: ' . $user->name . ' (' . $user->email . ')',
        ]);

        session()->flash('info', 'Akun karyawan otomatis dibuat untuk ' . $user->name . '.');

        return $employee;
    }

    public function openModal($type)
    {
        // Validasi akses untuk attendance-settings
        if ($type === 'attendance-settings' && !$this->isAdmin) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengatur pengaturan absensi.');
            return;
        }

        $this->modalType = $type;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetPayrollFields();
        $this->resetDepartmentFields();
        $this->resetLeaveFields();
        $this->resetEmployeeFields();
    }

    public function resetPayrollFields()
    {
        $this->payroll_month = now()->format('m');
        $this->payroll_year = now()->format('Y');
    }

    public function resetDepartmentFields()
    {
        $this->dept_id = null;
        $this->dept_name = '';
        $this->dept_description = '';
        $this->dept_is_active = true;
    }

    public function resetLeaveFields()
    {
        $this->leave_id = null;
        $this->leave_employee_id = '';
        $this->leave_start_date = now()->format('Y-m-d');
        $this->leave_end_date = now()->format('Y-m-d');
        $this->leave_type = 'annual';
        $this->leave_reason = '';
        $this->leave_status = 'pending';
    }

    public function resetEmployeeFields()
    {
        $this->emp_id = null;
        $this->employee_number = 'EMP-' . now()->format('Ymd') . '-' . sprintf('%03d', Employee::query()->count() + 1);
        $this->emp_name = '';
        $this->emp_email = '';
        $this->emp_phone = '';
        $this->emp_position = '';
        $this->emp_department = '';
        $this->emp_join_date = now()->format('Y-m-d');
        $this->emp_salary = 0;
        $this->emp_status = 'active';
        $this->emp_transport_allowance = 30000;
        $this->emp_meal_allowance = 25000;
    }

    // Employee CRUD Actions
    public function createEmployee()
    {
        $this->resetEmployeeFields();
        $this->openModal('employee');
    }

    public function editEmployee($id)
    {
        $emp = Employee::query()->findOrFail($id);
        $this->emp_id = $emp->id;
        $this->employee_number = $emp->employee_number;
        $this->emp_name = $emp->name;
        $this->emp_email = $emp->email;
        $this->emp_phone = $emp->phone;
        $this->emp_position = $emp->position;
        $this->emp_department = $emp->department;
        $this->emp_join_date = $emp->join_date;
        $this->emp_salary = $emp->salary;
        $this->emp_status = $emp->status;
        $this->emp_transport_allowance = $emp->transport_allowance ?? 30000;
        $this->emp_meal_allowance = $emp->meal_allowance ?? 25000;

        $this->openModal('employee');
    }

    public function saveEmployee()
    {
        $this->validate([
            'employee_number' => 'required|string|unique:employees,employee_number,' . $this->emp_id,
            'emp_name' => 'required|string|max:255',
            'emp_email' => 'nullable|email|max:255',
            'emp_phone' => 'nullable|string|max:20',
            'emp_salary' => 'required|numeric|min:0',
            'emp_transport_allowance' => 'required|numeric|min:0',
            'emp_meal_allowance' => 'required|numeric|min:0',
        ]);

        Employee::query()->updateOrCreate(
            ['id' => $this->emp_id],
            [
                'employee_number' => $this->employee_number,
                'name' => $this->emp_name,
                'email' => $this->emp_email,
                'phone' => $this->emp_phone,
                'position' => $this->emp_position,
                'department' => $this->emp_department,
                'join_date' => $this->emp_join_date,
                'salary' => $this->emp_salary,
                'status' => $this->emp_status,
                'transport_allowance' => $this->emp_transport_allowance,
                'meal_allowance' => $this->emp_meal_allowance,
            ]
        );

        session()->flash('success', 'Employee saved successfully.');
        $this->closeModal();
    }

    public function deleteEmployee($id)
    {
        Employee::query()->findOrFail($id)->delete();
        session()->flash('success', 'Employee deleted successfully.');
    }

    public function createPayroll()
    {
        $this->resetPayrollFields();
        $this->openModal('payroll');
    }

    public function generatePayroll()
    {
        $this->validate([
            'payroll_month' => 'required|numeric|between:1,12',
            'payroll_year' => 'required|numeric|min:2020|max:2030',
        ]);

        $employees = Employee::query()->get();

        if ($employees->isEmpty()) {
            session()->flash('error', 'No employees found to generate payroll.');
            return;
        }

        $periodStr = sprintf('%04d-%02d', $this->payroll_year, $this->payroll_month);

        $count = 0;
        foreach ($employees as $emp) {
            $exists = Payroll::query()->where('employee_id', $emp->id)
                ->where('period', $periodStr)
                ->exists();

            if ($exists) {
                continue;
            }

            $attendanceCount = Attendance::query()->where('employee_id', $emp->id)
                ->whereMonth('date', $this->payroll_month)
                ->whereYear('date', $this->payroll_year)
                ->where('status', 'present')
                ->count();

            if ($attendanceCount === 0) {
                $attendanceCount = 20;
            }

            $basic = floatval($emp->salary);

            $transportPerDay = $emp->transport_allowance ?? 30000;
            $mealPerDay = $emp->meal_allowance ?? 25000;
            
            $transportAllowance = $attendanceCount * $transportPerDay;
            $mealAllowance = $attendanceCount * $mealPerDay;
            $totalAllowance = $transportAllowance + $mealAllowance;

            // DEDUCTIONS
            $bpjsJht = $basic * 0.02;
            $bpjsJp  = $basic * 0.01;
            $bpjsKetenagakerjaan = $bpjsJht + $bpjsJp;
            $bpjsKesehatan = $basic * 0.01;

            $grossAnnual = ($basic + $totalAllowance) * 12;
            $ptkpStatus = 54000000;
            $taxableIncome = max(0, $grossAnnual - $ptkpStatus - ($bpjsKetenagakerjaan * 12) - ($bpjsKesehatan * 12));
            $pph21Annual = $this->calculatePph21($taxableIncome);
            $pph21Monthly = round($pph21Annual / 12);

            $totalDeduction = $bpjsKetenagakerjaan + $bpjsKesehatan + $pph21Monthly;
            $net = $basic + $totalAllowance - $totalDeduction;

            $payroll = Payroll::query()->create([
                'employee_id' => $emp->id,
                'period' => $periodStr,
                'basic_salary' => $basic,
                'allowances' => $totalAllowance,
                'deductions' => $totalDeduction,
                'total_salary' => $net,
                'status' => 'draft',
            ]);

            $components = [
                ['name' => 'Tunjangan Transport (Rp ' . number_format($transportPerDay, 0, ',', '.') . '/hari)', 'type' => 'allowance', 'amount' => $transportAllowance],
                ['name' => 'Tunjangan Makan (Rp ' . number_format($mealPerDay, 0, ',', '.') . '/hari)', 'type' => 'allowance', 'amount' => $mealAllowance],
                ['name' => 'BPJS Ketenagakerjaan (JHT 2%)', 'type' => 'deduction', 'amount' => $bpjsJht],
                ['name' => 'BPJS Ketenagakerjaan (JP 1%)', 'type' => 'deduction', 'amount' => $bpjsJp],
                ['name' => 'BPJS Kesehatan (1%)', 'type' => 'deduction', 'amount' => $bpjsKesehatan],
                ['name' => 'PPh 21', 'type' => 'deduction', 'amount' => $pph21Monthly],
            ];

            foreach ($components as $comp) {
                \App\Models\PayrollComponent::create([
                    'payroll_id' => $payroll->id,
                    'name' => $comp['name'],
                    'type' => $comp['type'],
                    'amount' => $comp['amount'],
                ]);
            }

            $count++;
        }

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'HR',
            'action' => 'Generate Payroll',
            'description' => 'Generated payroll draft for ' . $count . ' employees for period ' . $periodStr
        ]);

        session()->flash('success', 'Payroll draft generated successfully for ' . $count . ' records.');
        $this->closeModal();
    }

    private function calculatePph21($taxableIncome)
    {
        if ($taxableIncome <= 0) return 0;

        $tax = 0;
        $brackets = [
            [60000000, 0.05],
            [250000000 - 60000000, 0.15],
            [500000000 - 250000000, 0.25],
            [5000000000 - 500000000, 0.30],
            [PHP_INT_MAX, 0.35],
        ];

        $remaining = $taxableIncome;
        foreach ($brackets as [$limit, $rate]) {
            if ($remaining <= 0) break;
            $taxable = min($remaining, $limit);
            $tax += $taxable * $rate;
            $remaining -= $taxable;
        }

        return round($tax);
    }

    public function processPayment($id)
    {
        $payroll = Payroll::query()->findOrFail($id);
        $payroll->status = 'paid';
        $payroll->save();

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'HR',
            'action' => 'Pay Employee Salary',
            'description' => 'Salary payment completed for ' . $payroll->employee->name . ' for period ' . $payroll->period
        ]);

        session()->flash('success', 'Salary status updated to Paid.');
    }

    // Department actions
    public function createDepartment()
    {
        $this->resetDepartmentFields();
        $this->openModal('department');
    }

    public function saveDepartment()
    {
        $this->validate([
            'dept_name' => 'required|string|max:255',
            'dept_description' => 'nullable|string|max:255',
        ]);

        Department::updateOrCreate(
            ['id' => $this->dept_id],
            [
                'name' => $this->dept_name,
                'description' => $this->dept_description ?: '',
                'is_active' => $this->dept_is_active,
            ]
        );

        session()->flash('success', 'Department saved successfully.');
        $this->closeModal();
    }

    public function editDepartment($id)
    {
        $dept = Department::findOrFail($id);
        $this->dept_id = $dept->id;
        $this->dept_name = $dept->name;
        $this->dept_description = $dept->description;
        $this->dept_is_active = $dept->is_active;

        $this->openModal('department');
    }

    public function deleteDepartment($id)
    {
        $dept = Department::findOrFail($id);
        $dept->delete();
        session()->flash('success', 'Department deleted successfully.');
    }

    // Leave actions
    public function createLeave()
    {
        $this->resetLeaveFields();
        $this->openModal('leave');
    }

    public function saveLeave()
    {
        $this->validate([
            'leave_employee_id' => 'required|exists:employees,id',
            'leave_start_date' => 'required|date',
            'leave_end_date' => 'required|date|after_or_equal:leave_start_date',
            'leave_type' => 'required|string',
            'leave_reason' => 'nullable|string|max:255',
        ]);

        Leave::updateOrCreate(
            ['id' => $this->leave_id],
            [
                'employee_id' => $this->leave_employee_id,
                'start_date' => $this->leave_start_date,
                'end_date' => $this->leave_end_date,
                'type' => $this->leave_type,
                'reason' => $this->leave_reason ?: '',
                'status' => $this->leave_status,
            ]
        );

        session()->flash('success', 'Leave request saved successfully.');
        $this->closeModal();
    }

    public function editLeave($id)
    {
        $leave = Leave::findOrFail($id);
        $this->leave_id = $leave->id;
        $this->leave_employee_id = $leave->employee_id;
        $this->leave_start_date = $leave->start_date;
        $this->leave_end_date = $leave->end_date;
        $this->leave_type = $leave->type;
        $this->leave_reason = $leave->reason;
        $this->leave_status = $leave->status;

        $this->openModal('leave');
    }

    public function approveLeave($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'approved';
        $leave->save();
        session()->flash('success', 'Leave request approved.');
    }

    public function rejectLeave($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 'rejected';
        $leave->save();
        session()->flash('success', 'Leave request rejected.');
    }

    // ──────────────────────────────────────────────────────────────────
    // Attendance Clock-In / Clock-Out - ALL USERS CAN CLOCK
    // ──────────────────────────────────────────────────────────────────

    public function clockIn($latitude, $longitude, $address = '')
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return;
        }

        if (!$this->currentEmployeeId) {
            $this->loadOrCreateCurrentEmployee();
            if (!$this->currentEmployeeId) {
                session()->flash('error', 'Gagal membuat akun karyawan. Silakan hubungi admin.');
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
            session()->flash('error', 'Anda sudah melakukan clock-in hari ini.');
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
                    session()->flash('error', 'Belum bisa clock-in. Waktu clock-in dimulai pukul ' . $earlyLimit->format('H:i') . '.');
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
                            (float)$latitude,
                            (float)$longitude,
                            (float)$officeLat,
                            (float)$officeLng
                        );

                        $maxRadius = $setting->allowed_radius ?? $setting->radius ?? 200;
                        if ($distance > $maxRadius) {
                            session()->flash('error', 'Lokasi Anda terlalu jauh dari kantor (' . round($distance) . 'm). Jarak maksimum: ' . $maxRadius . 'm.');
                            return;
                        }
                    }
                } elseif ($requireLocation && (!$latitude || !$longitude)) {
                    session()->flash('error', 'Lokasi GPS diperlukan untuk clock-in. Aktifkan GPS pada perangkat Anda.');
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

        session()->flash('success', 'Clock-in berhasil pada pukul ' . Carbon::parse($currentTime)->format('H:i') . ($status === 'late' ? ' (TERLAMBAT)' : '') . '.');
    }

    public function clockOut($latitude, $longitude, $address = '')
    {
        if (!Auth::check()) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return;
        }

        if (!$this->currentEmployeeId) {
            session()->flash('error', 'Anda belum melakukan clock-in hari ini.');
            return;
        }

        $employee = Employee::findOrFail($this->currentEmployeeId);
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        $attendance = Attendance::where('employee_id', $this->currentEmployeeId)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            session()->flash('error', 'Anda belum clock-in hari ini.');
            return;
        }

        if ($attendance->check_out) {
            session()->flash('error', 'Anda sudah clock-out hari ini.');
            return;
        }

        $setting = AttendanceSetting::where('is_active', true)->first();
        $distance = null;

        if ($setting && $setting->require_location && $latitude && $longitude) {
            $distance = AttendanceSetting::calculateDistance(
                $latitude,
                $longitude,
                $setting->office_latitude,
                $setting->office_longitude
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

        session()->flash('success', 'Clock-out berhasil pada pukul ' . Carbon::parse($currentTime)->format('H:i') . '. Durasi kerja: ' . $durationStr . '.');
    }

    // Attendance Settings - Hanya untuk admin
    public function openAttendanceSettings()
    {
        // Validasi akses admin
        if (!$this->isAdmin) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengatur pengaturan absensi.');
            return;
        }

        $setting = AttendanceSetting::where('is_active', true)->first();

        if ($setting) {
            $this->setting_id = $setting->id;
            $this->setting_office_name = $setting->office_name;
            $this->setting_latitude = $setting->office_latitude;
            $this->setting_longitude = $setting->office_longitude;
            $this->setting_radius = $setting->allowed_radius;
            $this->setting_work_start = $setting->work_start_time;
            $this->setting_work_end = $setting->work_end_time;
            $this->setting_late_tolerance = $setting->late_tolerance_minutes;
            $this->setting_early_checkin = $setting->early_checkin_minutes;
            $this->setting_require_location = $setting->require_location;
        } else {
            $this->setting_id = null;
            $this->setting_office_name = 'Kantor Pusat';
            $this->setting_latitude = null;
            $this->setting_longitude = null;
            $this->setting_radius = 200;
            $this->setting_work_start = '08:00';
            $this->setting_work_end = '17:00';
            $this->setting_late_tolerance = 15;
            $this->setting_early_checkin = 60;
            $this->setting_require_location = true;
        }

        $this->openModal('attendance-settings');
    }

    public function saveAttendanceSettings()
    {
        // Validasi akses admin
        if (!$this->isAdmin) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengatur pengaturan absensi.');
            return;
        }

        $this->validate([
            'setting_office_name' => 'required|string|max:255',
            'setting_latitude' => 'required|numeric|between:-90,90',
            'setting_longitude' => 'required|numeric|between:-180,180',
            'setting_radius' => 'required|integer|min:50|max:5000',
            'setting_work_start' => 'required',
            'setting_work_end' => 'required',
            'setting_late_tolerance' => 'required|integer|min:0|max:120',
            'setting_early_checkin' => 'required|integer|min:0|max:180',
        ]);

        AttendanceSetting::updateOrCreate(
            ['id' => $this->setting_id],
            [
                'office_name' => $this->setting_office_name,
                'office_latitude' => $this->setting_latitude,
                'office_longitude' => $this->setting_longitude,
                'allowed_radius' => $this->setting_radius,
                'work_start_time' => $this->setting_work_start,
                'work_end_time' => $this->setting_work_end,
                'late_tolerance_minutes' => $this->setting_late_tolerance,
                'early_checkin_minutes' => $this->setting_early_checkin,
                'require_location' => $this->setting_require_location,
                'is_active' => true,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'HR',
            'action' => 'Update Attendance Settings',
            'description' => 'Updated attendance settings: ' . $this->setting_office_name . ' (radius: ' . $this->setting_radius . 'm)',
        ]);

        session()->flash('success', 'Pengaturan absensi berhasil disimpan.');
        $this->closeModal();
    }

    public function render()
    {
        $empQuery = Employee::query();
        if ($this->search) {
            $empQuery->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('employee_number', 'like', '%' . $this->search . '%')
                ->orWhere('department', 'like', '%' . $this->search . '%');
        }

        $attQuery = Attendance::with('employee')->orderBy('date', 'desc');
        if ($this->att_filter_date) {
            $attQuery->where('date', $this->att_filter_date);
        }

        $payrolls = Payroll::with(['employee', 'components'])->orderBy('period', 'desc')->paginate(10);

        $departments = Department::orderBy('name')->paginate(10);
        $leaves = Leave::with('employee')->orderBy('created_at', 'desc')->paginate(10);

        $attendanceSetting = AttendanceSetting::where('is_active', true)->first();

        return view('livewire.h-r-manager', [
            'employees' => $empQuery->paginate(10),
            'attendances' => $attQuery->paginate(10),
            'payrolls' => $payrolls,
            'departments' => $departments,
            'leaves' => $leaves,
            'allEmployees' => Employee::all(),
            'attendanceSetting' => $attendanceSetting,
            'currentEmployee' => $this->currentEmployee,
            'isAdmin' => $this->isAdmin, // Kirim ke view
        ]);
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
                try {
                    return Carbon::createFromFormat('g:i A', $time);
                } catch (\Exception $e3) {
                    return Carbon::parse($time);
                }
            }
        }
    }
}