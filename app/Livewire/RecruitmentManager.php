<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JobPosition;
use App\Models\Applicant;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class RecruitmentManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'applicants'; // applicants, jobs
    public $selectedJobId = '';

    // Job Position Form Fields
    public $job_id;
    public $job_title;
    public $job_department;
    public $job_expected_employees = 1;
    public $job_status = 'open';
    public $job_description;

    // Applicant Form Fields
    public $applicant_id;
    public $applicant_name;
    public $applicant_email;
    public $applicant_phone;
    public $applicant_job_position_id;
    public $applicant_status = 'applied';
    public $applicant_applied_date;
    public $applicant_notes;

    public $modalType = null; // job, applicant
    public $isEdit = false;

    protected $queryString = ['search', 'activeTab', 'selectedJobId'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->isEdit = $id ? true : false;

        if ($type === 'job') {
            if ($id) {
                $job = JobPosition::findOrFail($id);
                $this->job_id = $job->id;
                $this->job_title = $job->title;
                $this->job_department = $job->department;
                $this->job_expected_employees = $job->expected_employees;
                $this->job_status = $job->status;
                $this->job_description = $job->description;
            } else {
                $this->resetJobFields();
            }
        } elseif ($type === 'applicant') {
            if ($id) {
                $app = Applicant::findOrFail($id);
                $this->applicant_id = $app->id;
                $this->applicant_name = $app->name;
                $this->applicant_email = $app->email;
                $this->applicant_phone = $app->phone;
                $this->applicant_job_position_id = $app->job_position_id;
                $this->applicant_status = $app->status;
                $this->applicant_applied_date = $app->applied_date ? $app->applied_date->format('Y-m-d') : null;
                $this->applicant_notes = $app->notes;
            } else {
                $this->resetApplicantFields();
            }
        }
    }

    public function closeModal()
    {
        $this->modalType = null;
        $this->isEdit = false;
    }

    private function resetJobFields()
    {
        $this->job_id = null;
        $this->job_title = '';
        $this->job_department = '';
        $this->job_expected_employees = 1;
        $this->job_status = 'open';
        $this->job_description = '';
    }

    private function resetApplicantFields()
    {
        $this->applicant_id = null;
        $this->applicant_name = '';
        $this->applicant_email = '';
        $this->applicant_phone = '';
        $this->applicant_job_position_id = '';
        $this->applicant_status = 'applied';
        $this->applicant_applied_date = date('Y-m-d');
        $this->applicant_notes = '';
    }

    public function saveJob()
    {
        $this->validate([
            'job_title' => 'required|string|max:255',
            'job_department' => 'nullable|string|max:255',
            'job_expected_employees' => 'required|integer|min:1',
            'job_status' => 'required|in:open,closed',
            'job_description' => 'nullable|string',
        ]);

        $data = [
            'title' => $this->job_title,
            'department' => $this->job_department,
            'expected_employees' => $this->job_expected_employees,
            'status' => $this->job_status,
            'description' => $this->job_description,
        ];

        if ($this->isEdit) {
            $job = JobPosition::findOrFail($this->job_id);
            $job->update($data);
            $action = 'Updated job position: ' . $this->job_title;
        } else {
            JobPosition::create($data);
            $action = 'Created job position: ' . $this->job_title;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Recruitment',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Job position saved successfully!');
        $this->closeModal();
    }

    public function saveApplicant()
    {
        $this->validate([
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email|max:255',
            'applicant_phone' => 'nullable|string',
            'applicant_job_position_id' => 'required|exists:job_positions,id',
            'applicant_status' => 'required|in:applied,interview,offered,hired,rejected',
            'applicant_applied_date' => 'required|date',
            'applicant_notes' => 'nullable|string',
        ]);

        $data = [
            'name' => $this->applicant_name,
            'email' => $this->applicant_email,
            'phone' => $this->applicant_phone,
            'job_position_id' => $this->applicant_job_position_id,
            'status' => $this->applicant_status,
            'applied_date' => $this->applicant_applied_date,
            'notes' => $this->applicant_notes,
        ];

        if ($this->isEdit) {
            $app = Applicant::findOrFail($this->applicant_id);
            $app->update($data);
            $action = 'Updated applicant: ' . $this->applicant_name;
        } else {
            Applicant::create($data);
            $action = 'Registered applicant: ' . $this->applicant_name;
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Recruitment',
            'action' => $this->isEdit ? 'update' : 'create',
            'description' => $action,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Applicant details saved successfully!');
        $this->closeModal();
    }

    public function updateApplicantStatus($id, $status)
    {
        $app = Applicant::findOrFail($id);
        $app->update(['status' => $status]);

        session()->flash('success', 'Applicant stage updated to: ' . ucfirst($status));
    }

    public function deleteJob($id)
    {
        $job = JobPosition::findOrFail($id);
        $title = $job->title;
        $job->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Recruitment',
            'action' => 'delete',
            'description' => 'Deleted job position: ' . $title,
            'ip_address' => request()->ip(),
        ]);

        session()->flash('success', 'Job position deleted successfully!');
    }

    public function deleteApplicant($id)
    {
        $app = Applicant::findOrFail($id);
        $name = $app->name;
        $app->delete();

        session()->flash('success', 'Applicant deleted successfully!');
    }

    public function render()
    {
        $jobs = JobPosition::orderBy('title')->get();

        $jobsQuery = JobPosition::withCount('applicants')
            ->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%');
            });

        $applicantsQuery = Applicant::with('jobPosition')
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        if ($this->selectedJobId) {
            $applicantsQuery->where('job_position_id', $this->selectedJobId);
        }

        return view('livewire.recruitment-manager', [
            'jobsList' => $jobsQuery->paginate(10),
            'applicantsList' => $applicantsQuery->paginate(10),
            'jobs' => $jobs,
        ])->layout('layouts.app');
    }
}
