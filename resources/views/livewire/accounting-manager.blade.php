<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Akuntansi & Sistem Informasi Akuntansi (SIA)</h1>
            <p class="text-sm text-gray-500 mt-1 font-sans">Manage accounts, double-entry journals, detailed ledgers, period closing, and fixed assets depreciation.</p>
        </div>
        @if($activeTab === 'assets')
            <div class="flex space-x-2">
                <button wire:click="runDepreciation" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Run Depreciation
                </button>
                <button wire:click="createAsset" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Register Asset
                </button>
            </div>
        @elseif($activeTab === 'journals')
            <button wire:click="createJournal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Post Journal Entry
            </button>
        @endif
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 font-sans" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 font-sans" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'coa')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'coa' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Chart of Accounts
            </button>
            <button wire:click="$set('activeTab', 'journals')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'journals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Journal Ledger
            </button>
            <button wire:click="$set('activeTab', 'ledger_detail')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'ledger_detail' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Buku Besar Detail
            </button>
            <button wire:click="$set('activeTab', 'closing')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'closing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Tutup Buku
            </button>
            <button wire:click="$set('activeTab', 'assets')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'assets' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Fixed Assets
            </button>
        </nav>
    </div>

    @if($activeTab === 'coa')
        <!-- Search Accounts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 font-sans">
            <div class="flex-1 max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search accounts by code or name...">
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Account Code</th>
                            <th class="py-3.5 px-6">Account Name</th>
                            <th class="py-3.5 px-6">Type</th>
                            <th class="py-3.5 px-6 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($accounts as $acc)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $acc->code }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $acc->name }}</td>
                                <td class="py-4 px-6 text-gray-500">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($acc->type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-medium text-gray-950">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">No accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'journals')
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 font-sans">
            <div class="flex space-x-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Journal Type</label>
                    <select wire:model.live="filter_journal_type" class="mt-1 block w-48 border border-gray-300 rounded-md py-1.5 px-3 text-sm bg-white">
                        <option value="">-- All Types --</option>
                        <option value="general">General Ledger</option>
                        <option value="adjustment">Adjusting Entry</option>
                        <option value="closing">Closing Entry</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Journals Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Reference</th>
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Account</th>
                            <th class="py-3.5 px-6">Description</th>
                            <th class="py-3.5 px-6 text-center">Type</th>
                            <th class="py-3.5 px-6 text-right">Debit</th>
                            <th class="py-3.5 px-6 text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($journals as $j)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $j->reference_number }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $j->transaction_date }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $j->account->code }} - {{ $j->account->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $j->description }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        {{ $j->journal_type === 'closing' ? 'bg-red-100 text-red-800' : ($j->journal_type === 'adjustment' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($j->journal_type ?: 'general') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-600 font-semibold">{{ $j->type === 'debit' ? 'Rp '.number_format($j->amount, 0, ',', '.') : '-' }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-600 font-semibold">{{ $j->type === 'credit' ? 'Rp '.number_format($j->amount, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No journal ledger entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($journals->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $journals->links() }}
                </div>
            @endif
        </div>

    @elseif($activeTab === 'ledger_detail')
        <!-- Account and Date filter -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 font-sans space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Select Account</label>
                    <select wire:model.live="selected_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white">
                        <option value="">-- Choose Account --</option>
                        @foreach($allAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Start Date</label>
                    <input type="date" wire:model.live="ledger_start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">End Date</label>
                    <input type="date" wire:model.live="ledger_end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                </div>
            </div>
        </div>

        @if($selected_account_id)
            <!-- Ledger Detail view -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Buku Besar: {{ $allAccounts->find($selected_account_id)->name }}</h3>
                        <p class="text-sm text-gray-500 font-mono">Code: {{ $allAccounts->find($selected_account_id)->code }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Opening Balance:</span>
                        <div class="text-lg font-bold text-gray-900 font-mono">Rp {{ number_format($openingBalance, 2, ',', '.') }}</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3.5 px-6">Date</th>
                                <th class="py-3.5 px-6">Ref / Journal No</th>
                                <th class="py-3.5 px-6">Description</th>
                                <th class="py-3.5 px-6 text-right">Debit</th>
                                <th class="py-3.5 px-6 text-right">Credit</th>
                                <th class="py-3.5 px-6 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Opening row -->
                            <tr class="bg-blue-50 bg-opacity-30">
                                <td class="py-4 px-6 text-gray-400 font-medium" colspan="3">Opening Balance</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900 font-bold">Rp {{ number_format($openingBalance, 2, ',', '.') }}</td>
                            </tr>
                            @forelse($ledgerEntries as $entry)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-4 px-6 text-gray-500">{{ $entry->transaction_date }}</td>
                                    <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $entry->reference_number }}</td>
                                    <td class="py-4 px-6 text-gray-700">{{ $entry->description }}</td>
                                    <td class="py-4 px-6 text-right font-mono text-emerald-600 font-semibold">
                                        {{ $entry->type === 'debit' ? 'Rp '.number_format($entry->amount, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-red-600 font-semibold">
                                        {{ $entry->type === 'credit' ? 'Rp '.number_format($entry->amount, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-gray-900 font-bold">
                                        Rp {{ number_format($entry->running_balance, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">No postings in this period.</td>
                                </tr>
                            @endforelse
                            <!-- Closing row -->
                            <tr class="bg-emerald-50 bg-opacity-30">
                                <td class="py-4 px-6 text-gray-900 font-bold" colspan="3">Closing Balance</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-700 font-bold">Rp {{ number_format($closingBalance, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500 font-sans">
                Please select an account above to view details.
            </div>
        @endif

    @elseif($activeTab === 'closing')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-sans">
            <!-- Tutup Buku form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4 lg:col-span-1">
                <h3 class="text-lg font-bold text-gray-900">Execute Period Closing</h3>
                <p class="text-sm text-gray-500">Closing nominal accounts (revenues & expenses) zeroing them out and transferring the net balance to Laba Ditahan (Retained Earnings).</p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Closing Date</label>
                    <input type="date" wire:model="closing_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('closing_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Closing Note / Remarks</label>
                    <textarea wire:model="closing_note" rows="3" placeholder="e.g. Closing Q2 2026" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                    @error('closing_note') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button wire:click="executeClosing" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none font-semibold cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Close Fiscal Period
                    </button>
                </div>
            </div>

            <!-- Tutup Buku History -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4 lg:col-span-2">
                <h3 class="text-lg font-bold text-gray-900">Period Closing History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3.5 px-6">Closing Date</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Closed By</th>
                                <th class="py-3.5 px-6">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($closings as $c)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-4 px-6 font-mono text-gray-900 font-semibold">{{ $c->closing_date }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">UID: {{ $c->closed_by }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $c->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-500">No periods closed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <!-- Fixed Assets Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Asset Code</th>
                            <th class="py-3.5 px-6">Asset Name</th>
                            <th class="py-3.5 px-6 text-right">Cost Price</th>
                            <th class="py-3.5 px-6 text-right">Salvage Value</th>
                            <th class="py-3.5 px-6 text-center">Useful Life (Yrs)</th>
                            <th class="py-3.5 px-6 text-right">Current Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($assets as $asset)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $asset->asset_code }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $asset->asset_name }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($asset->residual_value, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">{{ $asset->useful_life_months }} mo ({{ round($asset->useful_life_months / 12, 1) }} yrs)</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">
                                    @php
                                    $dep = $asset->depreciations->sum('amount');
                                    $current = $asset->purchase_price - $dep;
                                    @endphp
                                    Rp {{ number_format($current, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No assets recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assets->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Journal Post Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">Post Double-Entry Journal Entry</h3>
                        <div class="mt-4 space-y-4">
                            <!-- Date & Journal Type -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" wire:model="journal_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('journal_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Journal Type</label>
                                    <select wire:model="journal_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="general">General Journal</option>
                                        <option value="adjustment">Adjusting Journal</option>
                                        <option value="closing">Closing Journal</option>
                                    </select>
                                    @error('journal_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" wire:model="description" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. Sales cash deposit, office utility payment">
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Debit Account -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Debit Account (+Asset / +Expense)</label>
                                <select wire:model="debit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                                @error('debit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Credit Account -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Credit Account (+Liability / +Equity / +Revenue)</label>
                                <select wire:model="credit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                                @error('credit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount (IDR)</label>
                                <input type="number" wire:model="amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                        <button type="button" wire:click="storeJournal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">Post Journal</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Asset Register Modal -->
    @if($isOpenAssetModal)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isOpenAssetModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="$set('isOpenAssetModal', false)" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">Register Fixed Asset</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asset Code</label>
                                <input type="text" wire:model="asset_code" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-gray-50 font-mono" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                                <input type="text" wire:model="asset_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. Server Rack, Delivery Van">
                                @error('asset_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <select wire:model="asset_category" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="Electronics">Electronics & IT</option>
                                    <option value="Vehicles">Vehicles</option>
                                    <option value="Machinery">Machinery & Factory Equipment</option>
                                    <option value="Buildings">Buildings & Real Estate</option>
                                    <option value="Equipment">Office Equipment</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                                    <input type="date" wire:model="asset_purchase_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_purchase_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Purchase Cost (IDR)</label>
                                    <input type="number" wire:model="asset_purchase_price" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_purchase_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Useful Life (Months)</label>
                                    <input type="number" wire:model="asset_useful_life" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_useful_life') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Residual Value (IDR)</label>
                                    <input type="number" wire:model="asset_residual_value" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_residual_value') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('isOpenAssetModal', false)" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                        <button type="button" wire:click="storeAsset" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Save Asset</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
