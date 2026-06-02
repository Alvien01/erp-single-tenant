<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Inventory & Warehousing</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor real-time stock balances across warehouses and perform stock counts (opname).</p>
        </div>
        <button wire:click="createAdjustment" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Stock Adjustment
        </button>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'stock')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'stock' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Stock Overview
            </button>
            <button wire:click="$set('activeTab', 'adjustments')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'adjustments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Adjustment Logs
            </button>
        </nav>
    </div>

    @if($activeTab === 'stock')
        <!-- Search & Filter Stock -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by code or name...">
            </div>
            
            <div class="w-full md:w-64">
                <select wire:model.live="warehouse_id" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Stock Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Warehouse</th>
                            <th class="py-3.5 px-6">Product Code</th>
                            <th class="py-3.5 px-6">Product Name</th>
                            <th class="py-3.5 px-6 text-right">Available Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($stocks as $stock)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $stock->warehouse->warehouse_name }}</td>
                                <td class="py-4 px-6 font-mono text-blue-600 font-medium">{{ $stock->product->code }}</td>
                                <td class="py-4 px-6 font-medium text-gray-700">{{ $stock->product->name }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900 font-semibold">{{ number_format($stock->qty_on_hand, 0, ',', '.') }} {{ $stock->product->unit }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">
                                    No stock records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stocks->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $stocks->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Adjustment Logs Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Adjustment #</th>
                            <th class="py-3.5 px-6">Warehouse</th>
                            <th class="py-3.5 px-6">Type</th>
                            <th class="py-3.5 px-6">Adjusted By</th>
                            <th class="py-3.5 px-6">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($adjustments as $adj)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono font-medium text-blue-600">{{ $adj->adj_number }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $adj->warehouse->warehouse_name }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $adj->type === 'opname' ? 'bg-amber-100 text-amber-800' : ($adj->type === 'addition' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($adj->type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">{{ $adj->adjustedBy->name ?? 'System' }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $adj->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    No stock adjustments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($adjustments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $adjustments->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Stock Adjustment Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            New Stock Adjustment / Opname
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Warehouse -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Warehouse</label>
                                <select wire:model.live="adj_warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                                @error('adj_warehouse_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Adjustment Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Adjustment Type</label>
                                <select wire:model="adj_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="opname">Opname (Physical Count)</option>
                                    <option value="addition">Addition (Write-in)</option>
                                    <option value="reduction">Reduction (Write-off)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Item list -->
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-900 font-display">Adjustment Items</h4>
                                <button type="button" wire:click="addAdjustmentItem" @disabled(empty($adj_warehouse_id)) class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 cursor-pointer">
                                    Add Line Item
                                </button>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="pb-2">Product</th>
                                        <th class="pb-2 w-32 text-right">System Qty</th>
                                        <th class="pb-2 w-32 text-right">Actual Qty</th>
                                        <th class="pb-2 w-32 text-right">Difference</th>
                                        <th class="pb-2 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($adj_items as $index => $item)
                                        <tr class="py-2">
                                            <td class="py-2 pr-2">
                                                <select wire:model.live="adj_items.{{ $index }}.product_id" class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm bg-white">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('adj_items.'.$index.'.product_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="py-2 pr-2 text-right font-mono text-sm align-middle pt-3 text-gray-500">
                                                {{ number_format($item['system_qty'] ?? 0, 0) }}
                                            </td>
                                            <td class="py-2 pr-2">
                                                <input type="number" wire:model.live="adj_items.{{ $index }}.actual_qty" class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm text-right">
                                            </td>
                                            <td class="py-2 pr-2 text-right font-mono text-sm align-middle pt-3 font-semibold {{ ($item['difference'] ?? 0) < 0 ? 'text-red-600' : (($item['difference'] ?? 0) > 0 ? 'text-emerald-600' : 'text-gray-900') }}">
                                                {{ ($item['difference'] ?? 0) > 0 ? '+' : '' }}{{ number_format($item['difference'] ?? 0, 0) }}
                                            </td>
                                            <td class="py-2 text-center align-middle">
                                                <button type="button" wire:click="removeAdjustmentItem({{ $index }})" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                            Cancel
                        </button>
                        <button type="button" wire:click="storeAdjustment" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">
                            Complete Adjustment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
