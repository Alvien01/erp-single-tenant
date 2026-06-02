<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\Department;
use App\Models\Leave;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class HRManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'employees';

    // Payroll generation parameters
    public $payroll_month;
    public $payroll_year;
    public $isOpen = false;
    public $modalType = ''; // 'payroll', 'department', 'leave', 'employee'

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

    public function openModal($type)
    {
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
            $allowance = $attendanceCount * 50000; 
            $deduction = 0; 
            $net = $basic + $allowance - $deduction;

            Payroll::query()->create([
                'employee_id' => $emp->id,
                'period' => $periodStr,
                'basic_salary' => $basic,
                'allowances' => $allowance,
                'deductions' => $deduction,
                'total_salary' => $net,
                'status' => 'draft',
            ]);
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

    public function render()
    {
        $empQuery = Employee::query();
        if ($this->search) {
            $empQuery->where('name', 'like', '%' . $this->search . '%')
                     ->orWhere('employee_number', 'like', '%' . $this->search . '%')
                     ->orWhere('department', 'like', '%' . $this->search . '%');
        }

        $attQuery = Attendance::with('employee')->orderBy('date', 'desc');
        $payrolls = Payroll::with('employee')->orderBy('period', 'desc')->paginate(10);
        
        $departments = Department::orderBy('name')->paginate(10);
        $leaves = Leave::with('employee')->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.h-r-manager', [
            'employees' => $empQuery->paginate(10),
            'attendances' => $attQuery->paginate(10),
            'payrolls' => $payrolls,
            'departments' => $departments,
            'leaves' => $leaves,
            'allEmployees' => Employee::all(),
        ]);
    }
}
