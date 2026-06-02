<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Inventory Reordering Rules</h1>
            <p class="text-sm text-gray-500 mt-1">Configure stock replenishment minimums and maximum trigger limits to automate drafting purchase requisitions.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Rule
        </button>
    </div>

    <!-- Toast / Feedback Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('info'))
        <div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200" role="alert">
            <span class="font-medium">Check Result:</span> {{ session('info') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-lg bg-rose-50 border border-rose-200" role="alert">
            <span class="font-medium">Trigger Alert!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search product name...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Product</th>
                        <th class="py-3.5 px-6">Warehouse</th>
                        <th class="py-3.5 px-6 text-right">Min Qty Trigger</th>
                        <th class="py-3.5 px-6 text-right">Max Stock Target</th>
                        <th class="py-3.5 px-6 text-right">Order Pack Size</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rules as $rule)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-bold text-gray-900">{{ $rule->product->name }}</td>
                            <td class="py-4 px-6 text-gray-700">{{ $rule->warehouse->warehouse_name }}</td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-rose-700 bg-rose-50/50">{{ number_format($rule->min_qty, 2) }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-800">{{ number_format($rule->max_qty, 2) }}</td>
                            <td class="py-4 px-6 text-right font-mono text-blue-700 bg-blue-50/30">{{ number_format($rule->qty_multiple, 2) }}</td>
                            <td class="py-4 px-6 text-center space-x-3 font-display font-medium text-xs">
                                <button wire:click="triggerReplenishmentCheck({{ $rule->id }})" class="text-blue-600 hover:text-blue-900 cursor-pointer">Run Replenish Check</button>
                                <button wire:click="edit({{ $rule->id }})" class="text-gray-600 hover:text-gray-900 cursor-pointer">Edit</button>
                                <button wire:click="delete({{ $rule->id }})" wire:confirm="Are you sure you want to delete this reordering rule?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                No reordering rules configured.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rules->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $rules->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Edit Reordering Trigger' : 'Configure Reordering Trigger' }}
                        </h3>
                        
                        <div class="mt-4 space-y-4 text-sm">
                            <!-- Product Selection -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Product Item</label>
                                <select wire:model="product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="">Select Product</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Code: {{ $p->code }})</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Warehouse Selection -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Warehouse Facility</label>
                                <select wire:model="warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="">Select Warehouse</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <!-- Min Qty -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Min stock (Trigger)</label>
                                    <input type="number" step="0.01" wire:model="min_qty" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-right font-mono">
                                    @error('min_qty') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Max Qty -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Max stock (Target)</label>
                                    <input type="number" step="0.01" wire:model="max_qty" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-right font-mono">
                                    @error('max_qty') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Order Qty -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Order pack size</label>
                                    <input type="number" step="0.01" wire:model="order_qty" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-right font-mono">
                                    @error('order_qty') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Save Rule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
