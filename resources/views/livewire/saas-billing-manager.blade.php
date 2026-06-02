<div class="space-y-6">
    <!-- Header banner -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-indigo-800 rounded-2xl p-6 md:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10">
            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <div class="relative z-10 space-y-2">
            <span class="bg-indigo-500/30 text-indigo-200 border border-indigo-400/30 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">SaaS Control Center</span>
            <h1 class="text-3xl font-extrabold font-display tracking-tight">{{ $tenant->name ?? 'My Workspace' }}</h1>
            <p class="text-indigo-100 max-w-xl text-sm md:text-base">Manage subscription plans, check active feature modules, update company localization settings, and inspect system billing invoices.</p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 bg-white rounded-xl p-1.5 shadow-sm">
        <button wire:click="$set('activeTab', 'overview')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'overview' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            Overview
        </button>
        <button wire:click="$set('activeTab', 'plans')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'plans' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            Pricing Plans
        </button>
        <button wire:click="$set('activeTab', 'invoices')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'invoices' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Billing Invoices
        </button>
        <button wire:click="$set('activeTab', 'settings')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'settings' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Tenant Config
        </button>
        <button wire:click="$set('activeTab', 'team')" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $activeTab === 'team' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Manage Team
        </button>
    </div>

    <!-- Feedback messages -->
    @if(session()->has('message'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-medium text-sm">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    <!-- Content Sections -->
    <div class="grid grid-cols-1 gap-6">

        <!-- 1. OVERVIEW TAB -->
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Current Subscription Details -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div class="space-y-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Current Subscription</h3>
                                <p class="text-xs text-gray-500">Active package details & plan usage</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $subscription ? ucfirst($subscription->status) : 'Trial' }}
                            </span>
                        </div>

                        <!-- Current Plan Card -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h4 class="text-2xl font-extrabold text-blue-700">{{ $tenant->plan->name ?? 'Starter Plan' }}</h4>
                                <p class="text-sm text-gray-500 mt-1">Pricing: 
                                    <span class="font-semibold text-gray-800">
                                        @if($tenant->plan && $tenant->plan->price_monthly > 0)
                                            Rp {{ number_format($tenant->plan->price_monthly, 0, ',', '.') }} / month
                                        @else
                                            Free / Trial
                                        @endif
                                    </span>
                                </p>
                            </div>
                            <div class="flex flex-col text-right">
                                <span class="text-xs text-gray-400">Ends at:</span>
                                <span class="text-sm font-semibold text-gray-800">
                                    {{ $subscription && $subscription->ends_at ? $subscription->ends_at->format('d M Y') : 'Unlimited' }}
                                </span>
                            </div>
                        </div>

                        <!-- Usage Limits -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-800">Workspace Feature Limits</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="text-xs font-medium text-gray-400">Max Users</div>
                                    <div class="text-lg font-bold text-gray-800 mt-1">
                                        {{ $tenant->plan->max_users ?? 'Unlimited' }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="text-xs font-medium text-gray-400">Max Products</div>
                                    <div class="text-lg font-bold text-gray-800 mt-1">
                                        {{ $tenant->plan->max_products ?? 'Unlimited' }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <div class="text-xs font-medium text-gray-400">Warehouses / Stores</div>
                                    <div class="text-lg font-bold text-gray-800 mt-1">
                                        {{ $tenant->plan->max_warehouses ?? 'Unlimited' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <button wire:click="$set('activeTab', 'plans')" class="w-full md:w-auto px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700 transition duration-150">
                            Upgrade Plan
                        </button>
                    </div>
                </div>

                <!-- Right: Module Feature Flag Toggles -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Feature Activation</h3>
                            <p class="text-xs text-gray-500">Toggle enabled features in this Workspace</p>
                        </div>

                        <div class="space-y-3 pt-2">
                            @php
                                $availableModules = [
                                    'pos' => ['POS Terminal', 'Point of sale, register, and cashier sessions'],
                                    'hrm' => ['HR & Payroll', 'Employee records, attendance, and payslip generation'],
                                    'crm' => ['CRM Pipeline', 'Lead capturing, support tickets, and conversion rate tracking'],
                                    'accounting' => ['Accounting Module', 'Journal entry ledger, assets, and balance sheets'],
                                    'inventory' => ['Advanced Inventory', 'Multi-warehouse logistics, transfers, and barcode scanning'],
                                    'sales' => ['B2B Sales', 'Quotations, invoices, and product orders'],
                                    'purchasing' => ['Procurement PO', 'Purchase orders, RFQs, and vendor management'],
                                ];
                            @endphp

                            @foreach($availableModules as $slug => $meta)
                                <div class="flex items-center justify-between p-3.5 bg-gray-50 hover:bg-gray-100/70 border border-gray-100 rounded-xl transition duration-150">
                                    <div class="space-y-0.5">
                                        <div class="text-sm font-bold text-gray-800">{{ $meta[0] }}</div>
                                        <div class="text-[11px] text-gray-400 leading-normal">{{ $meta[1] }}</div>
                                    </div>
                                    <button wire:click="toggleModule('{{ $slug }}')" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ in_array($slug, $enabledModules) ? 'bg-blue-600' : 'bg-gray-200' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ in_array($slug, $enabledModules) ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 2. PRICING PLANS TAB -->
        @if($activeTab === 'plans')
            <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-8">
                <div class="text-center max-w-xl mx-auto space-y-2">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Flexible SaaS Subscriptions</h3>
                    <p class="text-sm text-gray-500">Pick the plan that suits your company size. Upgrade or downgrade at any time instantly.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($plans as $plan)
                        @php
                            $isCurrent = $tenant->plan_id === $plan->id;
                        @endphp
                        <div class="border {{ $isCurrent ? 'border-blue-500 ring-2 ring-blue-500/10 bg-blue-50/10' : 'border-gray-200 hover:border-gray-300' }} rounded-2xl p-6 flex flex-col justify-between relative transition duration-150">
                            @if($isCurrent)
                                <span class="absolute top-4 right-4 bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full">Current Plan</span>
                            @endif

                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-lg font-bold text-gray-800">{{ $plan->name }}</h4>
                                    <div class="mt-2 flex items-baseline">
                                        <span class="text-3xl font-extrabold text-gray-900">
                                            @if($plan->price_monthly > 0)
                                                Rp {{ number_format($plan->price_monthly, 0, ',', '.') }}
                                            @else
                                                Free
                                            @endif
                                        </span>
                                        @if($plan->price_monthly > 0)
                                            <span class="ml-1 text-sm text-gray-400">/mo</span>
                                        @endif
                                    </div>
                                </div>

                                <ul class="space-y-2.5 text-sm text-gray-500 border-t border-gray-100 pt-4">
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Max Users: <span class="font-semibold text-gray-700">{{ $plan->max_users ?? 'Unlimited' }}</span></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Max Products: <span class="font-semibold text-gray-700">{{ $plan->max_products ?? 'Unlimited' }}</span></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Warehouses: <span class="font-semibold text-gray-700">{{ $plan->max_warehouses ?? 'Unlimited' }}</span></span>
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        <span>Full API Access</span>
                                    </li>
                                </ul>
                            </div>

                            <button wire:click="subscribeToPlan({{ $plan->id }})" 
                                    @if($isCurrent) disabled @endif
                                    class="w-full mt-6 py-2.5 rounded-lg text-sm font-semibold transition-all duration-150 {{ $isCurrent ? 'bg-gray-100 text-gray-400 cursor-default' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-md' }}">
                                {{ $isCurrent ? 'Current Plan' : 'Select Plan' }}
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. INVOICES TAB -->
        @if($activeTab === 'invoices')
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Billing History</h3>
                        <p class="text-xs text-gray-500">View and download all previous subscription invoices</p>
                    </div>
                </div>

                @if($invoices->isEmpty())
                    <div class="py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-semibold">No invoices found</p>
                        <p class="text-xs text-gray-400 mt-1">Invoices will show up here once billing cycles generate.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                    <th class="py-3 px-4">Invoice ID</th>
                                    <th class="py-3 px-4">Due Date</th>
                                    <th class="py-3 px-4">Amount</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Paid At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                @foreach($invoices as $inv)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="py-3 px-4 font-semibold text-gray-900">#INV-{{ sprintf('%05d', $inv->id) }}</td>
                                        <td class="py-3 px-4">{{ $inv->due_date ? $inv->due_date->format('d M Y') : '-' }}</td>
                                        <td class="py-3 px-4 font-medium text-gray-900">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider {{ $inv->status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                                {{ $inv->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-xs text-gray-500">
                                            {{ $inv->paid_at ? $inv->paid_at->format('d M Y H:i') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        <!-- 4. TENANT CONFIG TAB -->
        @if($activeTab === 'settings')
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tenant Localization & Info</h3>
                    <p class="text-xs text-gray-500">Configure parameters specific to this Tenant Workspace.</p>
                </div>

                <form wire:submit="saveSettings" class="space-y-4 max-w-2xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="company_name" :value="__('Company Name')" />
                            <x-text-input wire:model="company_name" id="company_name" class="block mt-1 w-full" type="text" required />
                            <x-input-error :messages="$errors->get('company_name')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="text" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea wire:model="address" id="address" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="currency_code" :value="__('Currency Code')" />
                            <select wire:model="currency_code" id="currency_code" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="IDR">IDR (Indonesian Rupiah)</option>
                                <option value="USD">USD (US Dollar)</option>
                                <option value="SGD">SGD (Singapore Dollar)</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency_code')" class="mt-1" />
                        </div>

                        <div>
                            <x-input-label for="timezone" :value="__('Timezone')" />
                            <select wire:model="timezone" id="timezone" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                                <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                                <option value="UTC">UTC (Universal Time Coordinated)</option>
                            </select>
                            <x-input-error :messages="$errors->get('timezone')" class="mt-1" />
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-blue-700 transition">
                            Save Workspace Configuration
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- 5. TEAM TAB -->
        @if($activeTab === 'team')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left: Invite Member Form -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 h-fit">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Invite Team Member</h3>
                            <p class="text-xs text-gray-500">Send an invitation to join this Workspace</p>
                        </div>

                        @if(session()->has('error'))
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-3 rounded-lg text-xs font-semibold">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="sendInvitation" class="space-y-4 pt-2">
                            <div>
                                <x-input-label for="inviteEmail" :value="__('Email Address')" />
                                <x-text-input wire:model="inviteEmail" id="inviteEmail" class="block mt-1 w-full text-sm" type="email" placeholder="member@company.com" required />
                                <x-input-error :messages="$errors->get('inviteEmail')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="inviteRole" :value="__('Workspace Role')" />
                                <select wire:model="inviteRole" id="inviteRole" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                    <option value="user">User (Standard)</option>
                                    <option value="manager">Manager</option>
                                    <option value="finance">Finance</option>
                                    <option value="warehouse">Warehouse Manager</option>
                                    <option value="hr">HR Specialist</option>
                                    <option value="admin">Workspace Admin</option>
                                </select>
                                <x-input-error :messages="$errors->get('inviteRole')" class="mt-1" />
                            </div>

                            <button type="submit" class="w-full px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-semibold shadow-md hover:bg-indigo-700 transition">
                                Send Invitation Link
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right: Active Members & Pending Invitations -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Active Members -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Active Workspace Members</h3>
                            <p class="text-xs text-gray-500">Users who currently have access to this tenant</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-gray-100 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                        <th class="py-3 px-4">User</th>
                                        <th class="py-3 px-4">Role</th>
                                        <th class="py-3 px-4">Status</th>
                                        <th class="py-3 px-4">Joined At</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                    @foreach($members as $member)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="py-3 px-4 flex items-center gap-3">
                                                <img class="w-8 h-8 rounded-full border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name ?? 'User') }}&color=4F46E5&background=EEF2FF" alt="">
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ $member->user->name ?? 'Name' }}</div>
                                                    <div class="text-xs text-gray-400">{{ $member->user->email ?? 'Email' }}</div>
                                                </div>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ $member->role === 'owner' ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-700' }}">
                                                    {{ $member->role }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-xs text-gray-500">
                                                {{ $member->joined_at ? $member->joined_at->format('d M Y') : $member->created_at->format('d M Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pending Invitations -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Pending Invitations</h3>
                            <p class="text-xs text-gray-500">Sent invitations that are waiting to be accepted</p>
                        </div>

                        @if($invitations->isEmpty())
                            <div class="py-8 text-center text-gray-400 text-sm">
                                No pending invitations.
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($invitations as $inv)
                                    <div class="flex flex-col md:flex-row md:items-center justify-between p-4 bg-gray-50 border border-gray-100 rounded-xl gap-4">
                                        <div class="space-y-1">
                                            <div class="text-sm font-semibold text-gray-800">{{ $inv->email }}</div>
                                            <div class="text-xs text-gray-400 flex items-center gap-2">
                                                <span>Role: <strong class="text-gray-600 uppercase">{{ $inv->role }}</strong></span>
                                                <span>•</span>
                                                <span>Expires: <strong class="text-gray-600">{{ $inv->expires_at->format('d M Y') }}</strong></span>
                                            </div>
                                            <!-- Shareable invitation link for demo/testing -->
                                            <div class="mt-2 flex items-center gap-2 p-1.5 bg-indigo-50 border border-indigo-100 rounded-lg text-[10px] text-indigo-700 select-all cursor-pointer font-mono" title="Klik untuk menyalin">
                                                <svg class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                <span>{{ url('/invitations/accept/' . $inv->token) }}</span>
                                            </div>
                                        </div>
                                        <button wire:click="cancelInvitation({{ $inv->id }})" class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 border border-red-200 rounded-lg transition">
                                            Batalkan
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
