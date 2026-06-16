<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MenuSetting;
use App\Models\ActivityLog;

class MenuManager extends Component
{
    public $search = '';
    public $filterGroup = '';

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
            'description' => 'Menu "' . $menu->label . '" (' . $menu->route_name . ') has been ' . ($menu->is_active ? 'activated' : 'deactivated') . '.',
        ]);

        session()->flash('success', 'Menu "' . $menu->label . '" berhasil ' . ($menu->is_active ? 'diaktifkan' : 'dinonaktifkan') . '.');
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
        // Keep dashboard always active
        MenuSetting::where('route_name', '!=', 'dashboard')->update(['is_active' => false]);
        MenuSetting::clearCache();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Menu Manager',
            'action' => 'Disable All Menus',
            'description' => 'All menus (except Dashboard) have been deactivated.',
        ]);

        session()->flash('success', 'Semua menu (kecuali Dashboard) berhasil dinonaktifkan.');
    }

    public function enableGroup($group)
    {
        MenuSetting::where('group', $group)->update(['is_active' => true]);
        MenuSetting::clearCache();

        session()->flash('success', 'Semua menu di grup "' . $group . '" berhasil diaktifkan.');
    }

    public function disableGroup($group)
    {
        MenuSetting::where('group', $group)->where('route_name', '!=', 'dashboard')->update(['is_active' => false]);
        MenuSetting::clearCache();

        session()->flash('success', 'Semua menu di grup "' . $group . '" berhasil dinonaktifkan.');
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

        $menus = $query->get();
        $grouped = $menus->groupBy('group');
        $groups = MenuSetting::distinct()->pluck('group')->sort()->values();

        $totalActive = MenuSetting::where('is_active', true)->count();
        $totalInactive = MenuSetting::where('is_active', false)->count();
        $totalMenus = MenuSetting::count();

        return view('livewire.menu-manager', [
            'grouped' => $grouped,
            'groups' => $groups,
            'totalActive' => $totalActive,
            'totalInactive' => $totalInactive,
            'totalMenus' => $totalMenus,
        ]);
    }
}
