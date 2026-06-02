<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-5">
            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
        </div>
        <div class="relative z-10 space-y-2">
            <span class="bg-amber-500/20 text-amber-300 border border-amber-400/30 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">Platform Admin</span>
            <h1 class="text-3xl font-extrabold tracking-tight">Super Admin Dashboard</h1>
            <p class="text-gray-300 max-w-xl text-sm">Kelola semua tenant, pantau pendapatan, dan kontrol platform SaaS secara keseluruhan.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-gray-200 bg-white rounded-xl p-1.5 shadow-sm">
        @foreach(['overview' => 'Overview', 'tenants' => 'Tenant Management', 'revenue' => 'Revenue', 'plans' => 'Plan Config'] as $tab => $label)
        <button wire:click="$set('activeTab', '{{ $tab }}')" class="flex-1 md:flex-none px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === $tab ? 'bg-gray-900 text-white shadow-md' : 'text-gray-600 hover:bg-gray-50' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    @if(session()->has('message'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm text-sm font-medium">{{ session('message') }}</div>
    @endif

    <!-- OVERVIEW -->
    @if($activeTab === 'overview')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Tenants</div>
            <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalTenants }}</div>
            <div class="text-xs text-emerald-600 font-semibold mt-1">{{ $activeTenants }} aktif</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Users</div>
            <div class="text-3xl font-extrabold text-gray-900 mt-2">{{ $totalUsers }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Total Revenue</div>
            <div class="text-3xl font-extrabold text-gray-900 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Bulan Ini</div>
            <div class="text-3xl font-extrabold text-gray-900 mt-2">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Plan Distribution -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Distribusi Tenant per Plan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($planDistribution as $pd)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex items-center justify-between">
                <div>
                    <div class="font-bold text-gray-800">{{ $pd->plan->name ?? 'No Plan' }}</div>
                    <div class="text-xs text-gray-400">Plan ID: {{ $pd->plan_id ?? '-' }}</div>
                </div>
                <div class="text-2xl font-extrabold text-indigo-600">{{ $pd->count }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- TENANTS -->
    @if($activeTab === 'tenants')
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Daftar Semua Tenant</h3>
                <p class="text-xs text-gray-500">Kelola, aktifkan/nonaktifkan, dan lihat detail tenant.</p>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <input wire:model.live.debounce.300ms="searchTenant" type="text" placeholder="Cari tenant..." class="w-full md:w-64 rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <select wire:model.live="filterStatus" class="rounded-lg border-gray-300 text-sm shadow-sm">
                    <option value="">Semua</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-400">
                        <th class="py-3 px-4">Tenant</th>
                        <th class="py-3 px-4">Plan</th>
                        <th class="py-3 px-4">Users</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Created</th>
                        <th class="py-3 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @foreach($tenants as $t)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="py-3 px-4">
                            <div class="font-semibold text-gray-900">{{ $t->name }}</div>
                            <div class="text-xs text-gray-400">{{ $t->slug }}.erp.com</div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700">{{ $t->plan->name ?? 'N/A' }}</span>
                        </td>
                        <td class="py-3 px-4 font-semibold">{{ $t->tenant_users_count }}</td>
                        <td class="py-3 px-4">
                            @if($t->is_active)
                                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Aktif</span>
                            @else
                                <span class="inline-flex items-center gap-1 text-red-500 font-semibold text-xs"><span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-500">{{ $t->created_at->format('d M Y') }}</td>
                        <td class="py-3 px-4 flex items-center gap-2">
                            <button wire:click="viewTenantDetail({{ $t->id }})" class="px-2.5 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 border border-indigo-200 rounded-lg transition">Detail</button>
                            <button wire:click="toggleTenantStatus({{ $t->id }})" class="px-2.5 py-1 text-xs font-semibold {{ $t->is_active ? 'text-red-600 hover:bg-red-50 border-red-200' : 'text-emerald-600 hover:bg-emerald-50 border-emerald-200' }} border rounded-lg transition">
                                {{ $t->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tenant Detail Modal -->
    @if($showTenantModal && $selectedTenant)
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="closeTenantModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[85vh] overflow-y-auto p-6 space-y-5">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">{{ $selectedTenant->name }}</h3>
                    <p class="text-xs text-gray-400">{{ $selectedTenant->slug }}.erp.com • ID: {{ $selectedTenant->id }}</p>
                </div>
                <button wire:click="closeTenantModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-gray-50 rounded-xl p-3 border"><span class="text-xs text-gray-400 block">Plan</span><span class="font-bold text-gray-800">{{ $selectedTenant->plan->name ?? 'N/A' }}</span></div>
                <div class="bg-gray-50 rounded-xl p-3 border"><span class="text-xs text-gray-400 block">Status</span><span class="font-bold {{ $selectedTenant->is_active ? 'text-emerald-600' : 'text-red-500' }}">{{ $selectedTenant->is_active ? 'Aktif' : 'Nonaktif' }}</span></div>
                <div class="bg-gray-50 rounded-xl p-3 border"><span class="text-xs text-gray-400 block">Company</span><span class="font-bold text-gray-800">{{ $selectedTenant->settings->company_name ?? '-' }}</span></div>
                <div class="bg-gray-50 rounded-xl p-3 border"><span class="text-xs text-gray-400 block">Timezone</span><span class="font-bold text-gray-800">{{ $selectedTenant->settings->timezone ?? '-' }}</span></div>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-800 mb-2">Anggota Tim ({{ $selectedTenant->tenantUsers->count() }})</h4>
                <div class="space-y-2">
                    @foreach($selectedTenant->tenantUsers as $tu)
                    <div class="flex items-center justify-between p-2.5 bg-gray-50 border rounded-lg">
                        <div class="flex items-center gap-2">
                            <img class="w-7 h-7 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($tu->user->name ?? 'U') }}&color=4F46E5&background=EEF2FF&size=28" alt="">
                            <div><div class="text-sm font-semibold text-gray-800">{{ $tu->user->name ?? '-' }}</div><div class="text-[11px] text-gray-400">{{ $tu->user->email ?? '-' }}</div></div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $tu->role === 'owner' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600' }}">{{ $tu->role }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- REVENUE -->
    @if($activeTab === 'revenue')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pendapatan per Plan</h3>
            @if($revenueByPlan->isEmpty())
                <p class="text-sm text-gray-400 py-8 text-center">Belum ada data pendapatan.</p>
            @else
                <div class="space-y-3">
                    @foreach($revenueByPlan as $rp)
                    <div class="flex items-center justify-between p-3.5 bg-gray-50 border border-gray-100 rounded-xl">
                        <span class="font-semibold text-gray-800 text-sm">{{ $rp->plan_name }}</span>
                        <span class="font-bold text-indigo-700">Rp {{ number_format($rp->total, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Ringkasan Keuangan</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-emerald-50 border border-emerald-100 rounded-xl">
                    <span class="text-sm font-medium text-emerald-700">Total Revenue (All Time)</span>
                    <span class="text-lg font-extrabold text-emerald-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <span class="text-sm font-medium text-blue-700">Revenue Bulan Ini</span>
                    <span class="text-lg font-extrabold text-blue-800">Rp {{ number_format($monthlyRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-amber-50 border border-amber-100 rounded-xl">
                    <span class="text-sm font-medium text-amber-700">Active Tenants</span>
                    <span class="text-lg font-extrabold text-amber-800">{{ $activeTenants }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- PLANS -->
    @if($activeTab === 'plans')
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Konfigurasi Plan Aktif</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach($plans as $plan)
            <div class="border border-gray-200 rounded-2xl p-5 space-y-3 hover:border-indigo-300 transition">
                <div class="flex justify-between items-start">
                    <h4 class="text-lg font-bold text-gray-800">{{ $plan->name }}</h4>
                    <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $plan->tenants_count }} tenants</span>
                </div>
                <div class="text-2xl font-extrabold text-gray-900">
                    @if($plan->price_monthly > 0) Rp {{ number_format($plan->price_monthly, 0, ',', '.') }} <span class="text-sm text-gray-400 font-normal">/mo</span>
                    @else Free @endif
                </div>
                <div class="text-xs text-gray-500 space-y-1 border-t pt-3">
                    <div>Max Users: <strong>{{ $plan->max_users ?? '∞' }}</strong></div>
                    <div>Max Products: <strong>{{ $plan->max_products ?? '∞' }}</strong></div>
                    <div>Max Warehouses: <strong>{{ $plan->max_warehouses ?? '∞' }}</strong></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
