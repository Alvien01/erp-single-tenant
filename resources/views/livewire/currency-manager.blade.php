<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Multi-Currency</h1>
            <p class="text-sm text-gray-500 mt-1">Configure global exchange rates and convert transactions dynamically.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button wire:click="syncRates" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                <svg wire:loading.remove wire:target="syncRates" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3 3L22 4"></path></svg>
                <svg wire:loading wire:target="syncRates" class="animate-spin w-4 h-4 mr-2 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="syncRates">Sync Exchange Rates</span>
                <span wire:loading wire:target="syncRates">Syncing...</span>
            </button>
            <button wire:click="createCurrency" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Currency
            </button>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Currencies List -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search / Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search currency by code or name...">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3.5 px-6">Symbol</th>
                                <th class="py-3.5 px-6">Code</th>
                                <th class="py-3.5 px-6">Currency Name</th>
                                <th class="py-3.5 px-6 text-right">Exchange Rate (to Base IDR)</th>
                                <th class="py-3.5 px-6 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($currencies as $cur)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-4 px-6 text-xl font-bold font-display text-gray-900">{{ $cur->symbol }}</td>
                                    <td class="py-4 px-6 font-mono font-bold text-blue-600">{{ $cur->code }}</td>
                                    <td class="py-4 px-6 text-gray-700 font-semibold">{{ $cur->name }}</td>
                                    <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">
                                        Rp {{ number_format($cur->exchange_rate, 4, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-6 text-center space-x-2">
                                        <button wire:click="editCurrency({{ $cur->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                        @if($cur->code !== 'IDR')
                                            <button wire:click="deleteCurrency({{ $cur->id }})" wire:confirm="Delete currency?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500">
                                        No currencies found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($currencies->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $currencies->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Exchange Calculator Tool -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">
            <h3 class="text-lg font-bold font-display text-gray-900">Live Exchange Calculator</h3>
            <p class="text-xs text-gray-500">Test exchange calculation dynamically based on configured rates.</p>

            <div class="space-y-3 font-sans">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Amount</label>
                    <input type="number" wire:model="calc_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">From Currency</label>
                        <select wire:model="calc_from_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white">
                            <option value="">Select</option>
                            @foreach($allCurrencies as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->code }} ({{ $ac->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">To Currency</label>
                        <select wire:model="calc_to_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white">
                            <option value="">Select</option>
                            @foreach($allCurrencies as $ac)
                                <option value="{{ $ac->id }}">{{ $ac->code }} ({{ $ac->symbol }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button wire:click="calculateConversion" class="w-full mt-2 inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-blue-600 hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150">
                    Convert Amount
                </button>

                @if($calc_result > 0)
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md text-center">
                        <p class="text-xs text-blue-600 uppercase font-semibold">Converted Result</p>
                        <p class="text-xl font-bold font-mono text-blue-900 mt-1">
                            {{ $calc_to_symbol }} {{ number_format($calc_result, 2, ',', '.') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modals Section -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $currency_id ? 'Edit Currency' : 'Add New Currency' }}
                        </h3>
                        <div class="mt-4 space-y-4 font-sans">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Currency Code</label>
                                    <input type="text" wire:model="code" placeholder="e.g. USD" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Symbol</label>
                                    <input type="text" wire:model="symbol" placeholder="e.g. $" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('symbol') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Currency Name</label>
                                <input type="text" wire:model="name" placeholder="e.g. US Dollar" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Exchange Rate (to Base IDR)</label>
                                <input type="number" step="0.000001" wire:model="exchange_rate" placeholder="e.g. 16250" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('exchange_rate') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="saveCurrency" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                            Save Currency
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
