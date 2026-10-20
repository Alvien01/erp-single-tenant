<?php

namespace Tests\Feature;

use App\Livewire\HRManager;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HRManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup a default attendance setting
        AttendanceSetting::create([
            'office_name' => 'Kantor Pusat',
            'office_latitude' => -6.200000,
            'office_longitude' => 106.816666,
            'allowed_radius' => 200,
            'work_start_time' => '08:00:00',
            'work_end_time' => '17:00:00',
            'late_tolerance_minutes' => 15,
            'early_checkin_minutes' => 60,
            'require_location' => false,
            'is_active' => true,
        ]);
    }

    public function test_can_render_hr_manager_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/hr');
        // If route /hr doesn't exist, we can test component rendering directly via Livewire
        $response->assertStatus(200);
    }

    public function test_auto_creates_employee_for_user_on_mount(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->actingAs($user);

        // Before mounting, no employee exists
        $this->assertDatabaseMissing('employees', [
            'email' => 'jane@example.com',
        ]);

        // Mount the Livewire component
        Livewire::test(HRManager::class);

        // After mounting, employee should have been created automatically
        $this->assertDatabaseHas('employees', [
            'email' => 'jane@example.com',
            'name' => 'Jane Doe',
        ]);
    }

    public function test_employee_can_clock_in_and_clock_out(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->actingAs($user);

        $test = Livewire::test(HRManager::class)
            ->call('clockIn', -6.200000, 106.816666, 'Kantor');

        $employee = Employee::where('email', '=', 'jane@example.com', 'and')->first();
        $this->assertNotNull($employee);

        $this->assertDatabaseHas('attendances', [
            'employee_id' => $employee->id,
            'date' => now()->format('Y-m-d'),
        ]);

        // Perform clock out
        $test->call('clockOut', -6.200000, 106.816666, 'Kantor');

        $attendance = Attendance::where('employee_id', '=', $employee->id, 'and')
            ->where('date', '=', now()->format('Y-m-d'), 'and')
            ->first();

        $this->assertNotNull($attendance->check_out);
    }

    public function test_can_create_and_update_employee(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a new employee manually via form submission
        Livewire::test(HRManager::class)
            ->set('employee_number', 'EMP-NEW-999')
            ->set('emp_name', 'Employee Baru')
            ->set('emp_email', 'baru@example.com')
            ->set('emp_phone', '0812345678')
            ->set('emp_position', 'Staff')
            ->set('emp_department', 'Finance')
            ->set('emp_salary', 6000000)
            ->set('emp_status', 'active')
            ->set('emp_transport_allowance', 30000)
            ->set('emp_meal_allowance', 25000)
            ->call('saveEmployee');

        $this->assertDatabaseHas('employees', [
            'name' => 'Employee Baru',
            'email' => 'baru@example.com',
            'department' => 'Finance',
            'salary' => 6000000,
        ]);
    }

    public function test_can_generate_payroll_and_components(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Ensure we have an employee
        $employee = Employee::create([
            'employee_number' => 'EMP-TEST-01',
            'name' => 'Test Employee',
            'email' => 'test_emp@example.com',
            'position' => 'Developer',
            'department' => 'IT',
            'join_date' => now()->format('Y-m-d'),
            'salary' => 8000000,
            'status' => 'active',
            'transport_allowance' => 30000,
            'meal_allowance' => 25000,
        ]);

        // Generate payroll
        Livewire::test(HRManager::class)
            ->set('payroll_month', now()->format('m'))
            ->set('payroll_year', now()->format('Y'))
            ->call('generatePayroll');

        $periodStr = now()->format('Y-m');

        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $employee->id,
            'period' => $periodStr,
            'basic_salary' => 8000000,
        ]);

        $payroll = Payroll::where('employee_id', '=', $employee->id, 'and')
            ->where('period', '=', $periodStr, 'and')
            ->first();

        // Check if payroll components are created
        $this->assertDatabaseHas('payroll_components', [
            'payroll_id' => $payroll->id,
            'name' => 'BPJS Kesehatan (1%)',
            'type' => 'deduction',
            'amount' => 80000,
        ]);
    }

    public function test_can_save_department(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(HRManager::class)
            ->set('dept_name', 'HR Department')
            ->set('dept_description', 'Human Resources')
            ->call('saveDepartment');

        $this->assertDatabaseHas('departments', [
            'name' => 'HR Department',
            'description' => 'Human Resources',
        ]);
    }
}
