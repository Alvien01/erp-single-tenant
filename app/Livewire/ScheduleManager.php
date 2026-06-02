<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Shift;
use App\Models\EmployeeSchedule;
use App\Models\Employee;
use App\Models\ActivityLog;

class ScheduleManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'schedules'; // schedules, shifts

    // Shift Form Fields
    public $shift_id;
    public $name;
    public $start_time = '08:00';
    public $end_time = '17:00';

    // Roster Assignment Form Fields
    public $roster_id;
    public $employee_id;
    public $shift_id_roster;
    public $date;
    public $notes;

    public $isOpenShiftModal = false;
    public $isEditShiftMode = false;

    public $isOpenRosterModal = false;
    public $isEditRosterMode = false;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
    }

    // Shift Methods
    public function openShiftModal()
    {
        $this->isOpenShiftModal = true;
    }

    public function closeShiftModal()
    {
        $this->isOpenShiftModal = false;
        $this->resetShiftFields();
    }

    public function resetShiftFields()
    {
        $this->shift_id = null;
        $this->name = '';
        $this->start_time = '08:00';
        $this->end_time = '17:00';
        $this->isEditShiftMode = false;
    }

    public function createShift()
    {
        $this->resetShiftFields();
        $this->openShiftModal();
    }

    public function storeShift()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Shift::updateOrCreate(['id' => $this->shift_id], [
            'name' => $this->name,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'HR',
            'action' => $this->isEditShiftMode ? 'Update Shift' : 'Create Shift',
            'description' => "Work Shift {$this->name} successfully saved."
        ]);

        session()->flash('success', 'Work Shift saved successfully.');
        $this->closeShiftModal();
    }

    public function editShift($id)
    {
        $shift = Shift::findOrFail($id);
        $this->shift_id = $shift->id;
        $this->name = $shift->name;
        $this->start_time = $shift->start_time;
        $this->end_time = $shift->end_time;

        $this->isEditShiftMode = true;
        $this->openShiftModal();
    }

    public function deleteShift($id)
    {
        Shift::findOrFail($id)->delete();
        session()->flash('success', 'Shift deleted successfully.');
    }

    // Roster / Schedule Assignment Methods
    public function openRosterModal()
    {
        $this->isOpenRosterModal = true;
    }

    public function closeRosterModal()
    {
        $this->isOpenRosterModal = false;
        $this->resetRosterFields();
    }

    public function resetRosterFields()
    {
        $this->roster_id = null;
        $this->employee_id = '';
        $this->shift_id_roster = '';
        $this->date = now()->format('Y-m-d');
        $this->notes = '';
        $this->isEditRosterMode = false;
    }

    public function createRoster()
    {
        $this->resetRosterFields();
        $this->openRosterModal();
    }

    public function storeRoster()
    {
        $this->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id_roster' => 'required|exists:shifts,id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        EmployeeSchedule::updateOrCreate(['id' => $this->roster_id], [
            'employee_id' => $this->employee_id,
            'shift_id' => $this->shift_id_roster,
            'date' => $this->date,
            'notes' => $this->notes,
        ]);

        $employee = Employee::find($this->employee_id);
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'HR',
            'action' => $this->isEditRosterMode ? 'Update Roster' : 'Create Roster',
            'description' => " Roster schedule for {$employee->name} on {$this->date} saved."
        ]);

        session()->flash('success', 'Employee Roster schedule assigned successfully.');
        $this->closeRosterModal();
    }

    public function editRoster($id)
    {
        $roster = EmployeeSchedule::findOrFail($id);
        $this->roster_id = $roster->id;
        $this->employee_id = $roster->employee_id;
        $this->shift_id_roster = $roster->shift_id;
        $this->date = $roster->date;
        $this->notes = $roster->notes;

        $this->isEditRosterMode = true;
        $this->openRosterModal();
    }

    public function deleteRoster($id)
    {
        $roster = EmployeeSchedule::findOrFail($id);
        $roster->delete();
        session()->flash('success', 'Roster schedule deleted.');
    }

    public function render()
    {
        $rosterQuery = EmployeeSchedule::with(['employee', 'shift']);

        if ($this->search) {
            $rosterQuery->whereHas('employee', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.schedule-manager', [
            'schedules' => $rosterQuery->orderBy('date', 'desc')->paginate(10),
            'shifts' => Shift::orderBy('name')->paginate(10),
            'allShifts' => Shift::orderBy('name')->get(),
            'employees' => Employee::orderBy('name')->get(),
        ]);
    }
}
