<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

class SettingsManager extends Component
{
    use WithPagination;

    public $activeTab = 'company';
    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Company fields
    public $company_id;
    public $company_name;
    public $company_address;
    public $company_phone;
    public $company_email;
    public $company_tax_number;

    // User fields
    public $user_id;
    public $user_name;
    public $user_email;
    public $user_phone;
    public $user_role = 'user';
    public $user_password;

    public function mount()
    {
        $this->loadCompany();
    }

    public function loadCompany()
    {
        $company = Company::first();
        if ($company) {
            $this->company_id = $company->id;
            $this->company_name = $company->name;
            $this->company_address = $company->address;
            $this->company_phone = $company->phone;
            $this->company_email = $company->email;
            $this->company_tax_number = $company->tax_number;
        } else {
            $this->company_name = 'My ERP Company';
            $this->company_address = '';
            $this->company_phone = '';
            $this->company_email = '';
            $this->company_tax_number = '';
        }
    }

    public function saveCompany()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_tax_number' => 'nullable|string|max:255',
        ]);

        $company = Company::updateOrCreate(
            ['id' => $this->company_id],
            [
                'name' => $this->company_name,
                'address' => $this->company_address ?: '',
                'phone' => $this->company_phone ?: '',
                'email' => $this->company_email ?: '',
                'tax_number' => $this->company_tax_number ?: '',
            ]
        );

        $this->company_id = $company->id;

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Settings',
            'action' => 'Update Company Profile',
            'description' => 'Company profile updated to: ' . $this->company_name
        ]);

        session()->flash('success', 'Company profile saved successfully.');
    }

    // User management actions
    public function resetUserFields()
    {
        $this->user_id = null;
        $this->user_name = '';
        $this->user_email = '';
        $this->user_phone = '';
        $this->user_role = 'user';
        $this->user_password = '';
        $this->isEditMode = false;
    }

    public function createUser()
    {
        $this->resetUserFields();
        $this->isOpen = true;
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->user_name = $user->name;
        $this->user_email = $user->email;
        $this->user_phone = $user->phone;
        $this->user_role = $user->role;
        $this->user_password = '';

        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function saveUser()
    {
        $rules = [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255|unique:users,email,' . $this->user_id,
            'user_role' => 'required|string',
            'user_phone' => 'nullable|string|max:20',
        ];

        if (!$this->isEditMode) {
            $rules['user_password'] = 'required|string|min:6';
        } else {
            $rules['user_password'] = 'nullable|string|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->user_name,
            'email' => $this->user_email,
            'role' => $this->user_role,
            'phone' => $this->user_phone ?: '',
        ];

        if ($this->user_password) {
            $data['password'] = Hash::make($this->user_password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Settings',
            'action' => $this->isEditMode ? 'Update User' : 'Create User',
            'description' => 'User ' . $this->user_name . ' has been saved.'
        ]);

        session()->flash('success', 'User saved successfully.');
        $this->isOpen = false;
    }

    public function deleteUser($id)
    {
        if ($id == auth()->id()) {
            session()->flash('error', 'Cannot delete currently logged-in user.');
            return;
        }

        $user = User::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Settings',
            'action' => 'Delete User',
            'description' => 'User ' . $user->name . ' has been deleted.'
        ]);

        $user->delete();
        session()->flash('success', 'User deleted successfully.');
    }

    public function render()
    {
        $users = User::query();

        if ($this->search) {
            $users->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
        }

        return view('livewire.settings-manager', [
            'users' => $users->orderBy('name')->paginate(10),
        ]);
    }
}
