<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MenuSetting;
use App\Models\ActivityLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

class MenuManager extends Component
{
    public $search = '';
    public $filterGroup = '';
    public $filterStatus = ''; // '', 'active', 'inactive'

    // Modal & Form State
    public $isOpen = false;
    public $isEditMode = false;
    public $menu_id = null;

    public $route_name = '';
    public $label = '';
    public $group = '';
    public $is_active = true;
    public $sort_order = 0;

    public $is_custom_route = false;
    public $is_custom_group = false;
    public $new_group_name = '';

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    public function resetInputFields()
    {
        $this->menu_id = null;
        $this->route_name = '';
        $this->label = '';
        $this->group = '';
        $this->is_active = true;
        $this->sort_order = ((int)MenuSetting::max('sort_order') ?? 0) + 10;
        $this->is_custom_route = false;
        $this->is_custom_group = false;
        $this->new_group_name = '';
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $firstGroup = MenuSetting::distinct()->whereNotNull('group')->pluck('group')->first();
        $this->group = $firstGroup ?? 'Main';
        $this->openModal();
    }

    public function updatedRouteName($value)
    {
        if ($value && !$this->isEditMode) {
            $this->label = ucwords(str_replace(['-', '_', '.'], ' ', $value));
        }
    }

    public function edit($id)
    {
        $this->resetValidation();
        $menu = MenuSetting::findOrFail($id);

        $this->menu_id = $menu->id;
        $this->route_name = $menu->route_name;
        $this->label = $menu->label;
        $this->group = $menu->group ?? '';
        $this->is_active = (bool) $menu->is_active;
        $this->sort_order = (int) $menu->sort_order;

        // Check if route exists in app routes or is custom
        $appRouteNames = $this->getRegisteredAppRoutes()->pluck('name')->toArray();
        $this->is_custom_route = !in_array($menu->route_name, $appRouteNames);

        $this->is_custom_group = false;
        $this->new_group_name = '';
        $this->isEditMode = true;

        $this->openModal();
    }

    public function store()
    {
        $targetGroup = $this->is_custom_group ? trim($this->new_group_name) : trim($this->group);

        $this->validate([
            'route_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menu_settings', 'route_name')->ignore($this->menu_id),
            ],
            'label' => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'is_active' => 'boolean',
        ], [
            'route_name.required' => 'Nama Route wajib dipilih atau diisi.',
            'route_name.unique' => 'Nama Route ini sudah terdaftar pada menu lain.',
            'label.required' => 'Label Menu wajib diisi.',
            'sort_order.required' => 'Urutan (sort order) wajib diisi.',
        ]);

        if ($this->is_custom_group && empty($targetGroup)) {
            $this->addError('new_group_name', 'Nama grup baru tidak boleh kosong.');
            return;
        }

        $menu = MenuSetting::updateOrCreate(
            ['id' => $this->menu_id],
            [
                'route_name' => trim($this->route_name),
                'label' => trim($this->label),
                'group' => $targetGroup ?: null,
                'is_active' => (bool) $this->is_active,
                'sort_order' => (int) $this->sort_order,
            ]
        );

        MenuSetting::clearCache();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Menu Manager',
            'action' => $this->isEditMode ? 'Update Menu' : 'Create Menu',
            'description' => 'Menu "' . $menu->label . '" (' . $menu->route_name . ') berhasil ' . ($this->isEditMode ? 'diperbarui' : 'dibuat') . '.',
        ]);

        session()->flash('success', 'Menu "' . $menu->label . '" berhasil ' . ($this->isEditMode ? 'diperbarui' : 'ditambahkan') . '.');
        $this->closeModal();
    }

    public function delete($id)
    {
        $menu = MenuSetting::findOrFail($id);

        if ($menu->route_name === 'manage-menu') {
            session()->flash('error', 'Menu "Manage Menu" tidak boleh dihapus agar Anda selalu dapat mengelola menu sistem.');
            return;
        }

        $label = $menu->label;
        $routeName = $menu->route_name;
        $menu->delete();

        MenuSetting::clearCache();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Menu Manager',
            'action' => 'Delete Menu',
            'description' => 'Menu "' . $label . '" (' . $routeName . ') telah dihapus.',
        ]);

        session()->flash('success', 'Menu "' . $label . '" berhasil dihapus.');
    }

    public function toggleMenu($id)
    {
        $menu = MenuSetting::findOrFail($id);
        $menu->is_active = !$menu->is_active;
        $menu->save();

        MenuSetting::clearCache();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Menu Manager',
            'action' => $menu->is_active ? 'Activate Menu' : 'Deactivate Menu',
            'description' => 'Menu "' . $menu->label . '" (' . $menu->route_name . ') telah ' . ($menu->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.',
        ]);

        session()->flash('success', 'Menu "' . $menu->label . '" berhasil ' . ($menu->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.');
    }

    public function moveOrder($id, $direction)
    {
        $menu = MenuSetting::findOrFail($id);
        if ($direction === 'up') {
            $menu->sort_order = max(0, $menu->sort_order - 1);
        } else {
            $menu->sort_order = $menu->sort_order + 1;
        }
        $menu->save();
        MenuSetting::clearCache();
    }

    public function enableAll()
    {
        MenuSetting::query()->update(['is_active' => true]);
        MenuSetting::clearCache();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Menu Manager',
            'action' => 'Enable All Menus',
            'description' => 'All menus have been activated.',
        ]);

        session()->flash('success', 'Semua menu berhasil diaktifkan.');
    }

    public function disableAll()
    {
        MenuSetting::whereNotIn('route_name', ['dashboard', 'manage-menu'])->update(['is_active' => false]);
        MenuSetting::clearCache();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Menu Manager',
            'action' => 'Disable All Menus',
            'description' => 'All menus (except Dashboard & Manage Menu) have been deactivated.',
        ]);

        session()->flash('success', 'Semua menu (kecuali Dashboard & Manage Menu) berhasil dinonaktifkan.');
    }

    public function enableGroup($group)
    {
        MenuSetting::where('group', $group)->update(['is_active' => true]);
        MenuSetting::clearCache();
        session()->flash('success', 'Semua menu di grup "' . $group . '" berhasil diaktifkan.');
    }

    public function disableGroup($group)
    {
        MenuSetting::where('group', $group)->whereNotIn('route_name', ['dashboard', 'manage-menu'])->update(['is_active' => false]);
        MenuSetting::clearCache();
        session()->flash('success', 'Semua menu di grup "' . $group . '" berhasil dinonaktifkan.');
    }

    public function syncFromRoutes()
    {
        $availableRoutes = $this->getRegisteredAppRoutes();
        $existing = MenuSetting::pluck('route_name')->toArray();
        $addedCount = 0;
        $maxOrder = MenuSetting::max('sort_order') ?? 0;

        foreach ($availableRoutes as $r) {
            if (!in_array($r['name'], $existing)) {
                $maxOrder += 5;
                MenuSetting::create([
                    'route_name' => $r['name'],
                    'label' => $r['suggested_label'],
                    'group' => 'Lainnya',
                    'is_active' => true,
                    'sort_order' => $maxOrder,
                ]);
                $addedCount++;
            }
        }

        MenuSetting::clearCache();

        if ($addedCount > 0) {
            session()->flash('success', "Sinkronisasi berhasil! {$addedCount} route baru dari routes/web.php berhasil ditambahkan ke menu.");
        } else {
            session()->flash('info', "Semua route aplikasi dari routes/web.php sudah terdaftar di menu.");
        }
    }

    public function getRegisteredAppRoutes()
    {
        return collect(Route::getRoutes()->getRoutes())
            ->filter(function ($route) {
                if (!in_array('GET', $route->methods())) {
                    return false;
                }
                $name = $route->getName();
                if (!$name || count($route->parameterNames()) > 0) {
                    return false;
                }
                $excluded = [
                    'login', 'register', 'logout', 'password.', 'verification.',
                    'livewire.', 'sanctum.', 'ignition.', '_ignition', 'storage.',
                    'midtrans.', 'up', 'home'
                ];
                foreach ($excluded as $prefix) {
                    if (str_starts_with($name, $prefix) || $name === $prefix) {
                        return false;
                    }
                }
                // Sub-pages (e.g. sales.create) inherit their parent menu, don't register them separately
                if (\App\Http\Middleware\CheckMenuAccess::resolveMenuRoute($name) !== $name) {
                    return false;
                }
                return true;
            })
            ->map(function ($route) {
                $name = $route->getName();
                $suggested = ucwords(str_replace(['-', '_', '.'], ' ', $name));
                return [
                    'name' => $name,
                    'uri' => $route->uri(),
                    'suggested_label' => $suggested,
                ];
            })
            ->unique('name')
            ->sortBy('name')
            ->values();
    }

    public function render()
    {
        $query = MenuSetting::query()->orderBy('group')->orderBy('sort_order');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('label', 'like', '%' . $this->search . '%')
                  ->orWhere('route_name', 'like', '%' . $this->search . '%')
                  ->orWhere('group', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGroup) {
            $query->where('group', $this->filterGroup);
        }

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        $menus = $query->get();
        $grouped = $menus->groupBy(fn($item) => $item->group ?: 'Tanpa Grup');
        $groups = MenuSetting::distinct()->whereNotNull('group')->pluck('group')->sort()->values();

        $totalActive = MenuSetting::where('is_active', true)->count();
        $totalInactive = MenuSetting::where('is_active', false)->count();
        $totalMenus = MenuSetting::count();

        return view('livewire.menu-manager', [
            'grouped' => $grouped,
            'groups' => $groups,
            'totalActive' => $totalActive,
            'totalInactive' => $totalInactive,
            'totalMenus' => $totalMenus,
            'appRoutes' => $this->getRegisteredAppRoutes(),
        ]);
    }
}
