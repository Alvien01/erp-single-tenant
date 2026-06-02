<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\User;
use App\Models\BillingInvoice;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class PlatformAdminPanel extends Component
{
    public $activeTab = 'overview'; // overview | tenants | revenue | plans

    // Tenant management filters
    public $searchTenant = '';
    public $filterStatus = '';

    // Tenant detail modal
    public $selectedTenant = null;
    public $showTenantModal = false;

    public function viewTenantDetail($tenantId)
    {
        $this->selectedTenant = Tenant::with(['plan', 'settings', 'tenantUsers.user', 'subscriptions'])
            ->find($tenantId);
        $this->showTenantModal = true;
    }

    public function closeTenantModal()
    {
        $this->showTenantModal = false;
        $this->selectedTenant = null;
    }

    public function toggleTenantStatus($tenantId)
    {
        $tenant = Tenant::find($tenantId);
        if ($tenant) {
            $tenant->update(['is_active' => !$tenant->is_active]);
            session()->flash('message', "Tenant {$tenant->name} " . ($tenant->is_active ? 'diaktifkan' : 'dinonaktifkan') . ".");
        }
    }

    public function render()
    {
        // KPI Metrics
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();
        $totalUsers = User::count();
        $totalRevenue = BillingInvoice::where('status', 'paid')->sum('amount');
        $monthlyRevenue = BillingInvoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        // Tenant listing with filters
        $tenantsQuery = Tenant::with(['plan', 'settings'])
            ->withCount(['tenantUsers', 'subscriptions']);

        if ($this->searchTenant) {
            $tenantsQuery->where(function ($q) {
                $q->where('name', 'like', "%{$this->searchTenant}%")
                  ->orWhere('slug', 'like', "%{$this->searchTenant}%");
            });
        }

        if ($this->filterStatus === 'active') {
            $tenantsQuery->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $tenantsQuery->where('is_active', false);
        }

        $tenants = $tenantsQuery->latest()->get();

        // Revenue breakdown by plan
        $revenueByPlan = DB::table('billing_invoices')
            ->join('subscriptions', 'billing_invoices.subscription_id', '=', 'subscriptions.id')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('billing_invoices.status', 'paid')
            ->select('plans.name as plan_name', DB::raw('SUM(billing_invoices.amount) as total'))
            ->groupBy('plans.name')
            ->get();

        // Plan distribution
        $planDistribution = Tenant::select('plan_id', DB::raw('COUNT(*) as count'))
            ->groupBy('plan_id')
            ->with('plan')
            ->get();

        $plans = Plan::withCount('tenants')->where('is_active', true)->get();

        return view('livewire.platform-admin-panel', [
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'tenants' => $tenants,
            'revenueByPlan' => $revenueByPlan,
            'planDistribution' => $planDistribution,
            'plans' => $plans,
        ])->layout('layouts.app');
    }
}
