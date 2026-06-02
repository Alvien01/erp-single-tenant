<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Warehouse Stock Transfers</h1>
            <p class="text-sm text-gray-500 mt-1">Move products between warehouses, update location stock ledger balances, and check stock history.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Initiate Transfer
        </button>
    </div>

    <!-- Toast / Feedback Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-rose-800 rounded-lg bg-rose-50 border border-rose-200" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search Transfer number...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Transfer ID</th>
                        <th class="py-3.5 px-6">Source Facility</th>
                        <th class="py-3.5 px-6">Destination Facility</th>
                        <th class="py-3.5 px-6">Transfer Date</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transfers as $trf)
                        @php
                            $statusColors = match($trf->status) {
                                'draft' => 'bg-gray-100 text-gray-700',
                                'completed' => 'bg-emerald-100 text-emerald-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ $trf->transfer_number }}</td>
                            <td class="py-4 px-6 text-gray-900">{{ $trf->sourceWarehouse->warehouse_name }}</td>
                            <td class="py-4 px-6 text-gray-900">{{ $trf->destinationWarehouse->warehouse_name }}</td>
                            <td class="py-4 px-6 text-gray-500">{{ $trf->transfer_date }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors }}">
                                    {{ ucfirst($trf->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2 font-display font-medium text-xs">
                                @if($trf->status === 'draft')
                                    <button wire:click="completeTransfer({{ $trf->id }})" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">Execute & Dispatch</button>
                                @endif
                                <button wire:click="edit({{ $trf->id }})" class="text-gray-600 hover:text-gray-900 cursor-pointer">View / Edit</button>
                                <button wire:click="delete({{ $trf->id }})" wire:confirm="Are you sure you want to delete this transfer?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                No warehouse transfers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Edit Warehouse Transfer' : 'Initiate Stock Transfer' }}
                        </h3>
                        
                        <div class="mt-4 space-y-4 text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Transfer Number -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Transfer ID</label>
                                    <input type="text" wire:model="transfer_number" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 bg-gray-50 font-mono" readonly>
                                    @error('transfer_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Date -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Transfer Date</label>
                                    <input type="date" wire:model="transfer_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    @error('transfer_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Status Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                        <option value="draft">Draft</option>
                                        <option value="completed">Completed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Source Warehouse Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Source Warehouse</label>
                                    <select wire:model.live="source_warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" {{ $isEditMode ? 'disabled' : '' }}>
                                        <option value="">Select Source Warehouse</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('source_warehouse_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Destination Warehouse Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Destination Warehouse</label>
                                    <select wire:model="destination_warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                        <option value="">Select Destination Warehouse</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('destination_warehouse_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Internal Notes / Transfer Details</label>
                                <textarea wire:model="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="Reason for transfer, driver info, tracking logs..."></textarea>
                            </div>

                            <!-- Transfer Items List -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Transfer Lines Ledger</h4>
                                    <button type="button" wire:click="addItem" class="text-xs font-bold text-blue-600 hover:text-blue-800 cursor-pointer">+ Add Transfer Line</button>
                                </div>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50 font-semibold text-gray-500">
                                            <tr>
                                                <th class="py-2 px-4 text-left">Product Selection</th>
                                                <th class="py-2 px-4 text-right">Available Qty</th>
                                                <th class="py-2 px-4 text-right">Qty to Transfer</th>
                                                <th class="py-2 px-4 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($items as $index => $item)
                                                <tr>
                                                    <td class="py-2 px-4">
                                                        <select wire:model.live="items.{{ $index }}.product_id" class="w-full border border-gray-300 rounded px-2 py-1">
                                                            <option value="">Select Product</option>
                                                            @foreach($products as $p)
                                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="py-2 px-4 text-right font-mono text-gray-500">
                                                        {{ number_format($item['available_qty'], 2) }}
                                                    </td>
                                                    <td class="py-2 px-4 text-right font-mono">
                                                        <input type="number" step="0.01" min="0.01" max="{{ $item['available_qty'] }}" wire:model="items.{{ $index }}.qty" class="w-24 text-right border border-gray-300 rounded px-1.5 py-0.5">
                                                    </td>
                                                    <td class="py-2 px-4 text-center">
                                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 cursor-pointer">Remove</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="py-6 text-center text-gray-400 italic">No lines added yet. Click "+ Add Transfer Line" above.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Save Transfer Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
