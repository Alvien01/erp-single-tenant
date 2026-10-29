<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use App\Models\User;
use App\Models\MenuSetting;
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
    public $selected_menus = [];

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
        $this->selected_menus = [];
        $this->isEditMode = false;
    }

    public function createUser()
    {
        $this->resetUserFields();
        $this->applyRolePreset();
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

        if ($user->allowed_menus !== null) {
            $this->selected_menus = is_array($user->allowed_menus) ? $user->allowed_menus : [];
        } else {
            $this->selected_menus = $user->role === 'admin'
                ? MenuSetting::where('is_active', true)->pluck('route_name')->toArray()
                : $this->getPresetForRole($user->role);
        }

        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function updatedUserRole($value)
    {
        // When changing role, automatically apply the preset for convenience
        $this->applyRolePreset();
    }

    public function applyRolePreset()
    {
        $this->selected_menus = $this->getPresetForRole($this->user_role);
    }

    public function selectAllMenus()
    {
        $this->selected_menus = MenuSetting::where('is_active', true)->pluck('route_name')->toArray();
    }

    public function deselectAllMenus()
    {
        $this->selected_menus = [];
    }

    public function toggleGroupMenus(string $group)
    {
        $groupRoutes = MenuSetting::where('is_active', true)
            ->where('group', $group)
            ->pluck('route_name')
            ->toArray();

        $intersect = array_intersect($groupRoutes, $this->selected_menus);
        $allSelected = count($intersect) === count($groupRoutes);

        if ($allSelected) {
            $this->selected_menus = array_values(array_diff($this->selected_menus, $groupRoutes));
        } else {
            $this->selected_menus = array_values(array_unique(array_merge($this->selected_menus, $groupRoutes)));
        }
    }

    public function getPresetForRole(string $role): array
    {
        $allActive = MenuSetting::where('is_active', true)->pluck('route_name')->toArray();

        return match($role) {
            'admin' => $allActive,
            'manager' => array_values(array_intersect($allActive, [
                'dashboard', 'products', 'categories', 'customers', 'suppliers', 'warehouses',
                'crm', 'pos', 'pos-stores', 'pos-members', 'pos-promos', 'pos-reports',
                'sales-quotations', 'sales-orders', 'sales', 'delivery-orders', 'returns', 'invoices',
                'subscriptions', 'rentals', 'bom', 'production-orders', 'plm', 'advanced-manufacturing',
                'good-receipts', 'inventory', 'stock-valuation', 'hpp-calculator', 'quality-control',
                'barcode-scanner', 'warehouse-transfers', 'reordering-rules', 'advanced-logistics',
                'hr', 'expenses', 'recruitment', 'appraisals', 'schedules',
                'purchases', 'purchase-requests', 'rfqs', 'fleet', 'maintenance', 'fsm',
                'discuss', 'documents', 'projects-manager', 'reports'
            ])),
            'finance' => array_values(array_intersect($allActive, [
                'dashboard', 'customers', 'suppliers', 'sales', 'invoices', 'returns',
                'accounting', 'cash-bank', 'accounts-receivable', 'accounts-payable',
                'bank-reconciliation', 'taxes', 'currencies', 'budgets', 'approvals',
                'advanced-accounting', 'purchases', 'expenses', 'reports', 'documents'
            ])),
            'warehouse' => array_values(array_intersect($allActive, [
                'dashboard', 'products', 'categories', 'suppliers', 'warehouses',
                'good-receipts', 'inventory', 'stock-valuation', 'quality-control',
                'barcode-scanner', 'warehouse-transfers', 'reordering-rules',
                'advanced-logistics', 'delivery-orders', 'returns', 'documents'
            ])),
            'hr' => array_values(array_intersect($allActive, [
                'dashboard', 'hr', 'expenses', 'recruitment', 'appraisals',
                'schedules', 'discuss', 'documents'
            ])),
            default => array_values(array_intersect($allActive, [
                'dashboard', 'products', 'pos', 'crm', 'discuss', 'documents'
            ])),
        };
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
            'allowed_menus' => $this->user_role === 'admin' ? null : array_values(array_unique($this->selected_menus)),
        ];

        if ($this->user_password) {
            $data['password'] = Hash::make($this->user_password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Settings',
            'action' => $this->isEditMode ? 'Update User' : 'Create User',
            'description' => 'User ' . $this->user_name . ' has been saved with ' . ($this->user_role === 'admin' ? 'All (Admin)' : count($this->selected_menus)) . ' accessible menus.'
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

        $rawMenuGroups = MenuSetting::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group');

        $menuGroupsData = $rawMenuGroups->map(function ($groupMenus, $groupName) {
            $groupRoutes = $groupMenus->pluck('route_name')->toArray();
            $intersect = array_intersect($groupRoutes, $this->selected_menus);
            $allSelected = count($groupRoutes) > 0 && count($intersect) === count($groupRoutes);

            $menus = $groupMenus->map(function ($menu) {
                return [
                    'route_name' => $menu->route_name,
                    'label' => $menu->label,
                    'is_checked' => in_array($menu->route_name, $this->selected_menus, true),
                ];
            });

            return [
                'group_name' => $groupName,
                'selected_count' => count($intersect),
                'total_count' => count($groupRoutes),
                'all_selected' => $allSelected,
                'menus' => $menus,
            ];
        });

        return view('livewire.settings-manager', [
            'users' => $users->orderBy('name')->paginate(10),
            'menuGroupsData' => $menuGroupsData,
            'totalActiveMenus' => MenuSetting::where('is_active', true)->count(),
        ]);
    }
}
