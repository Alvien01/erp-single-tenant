<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Delivery Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Manage physical product deliveries, logistics tracking, and warehouse shipping orders.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create DO
        </button>
    </div>

    <!-- Toast / Feedback Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search DO number or customer...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">DO Number</th>
                        <th class="py-3.5 px-6">Sales Order</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Warehouse</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($deliveryOrders as $do)
                        @php
                            $statusColors = match($do->status) {
                                'draft' => 'bg-gray-100 text-gray-700',
                                'ready' => 'bg-blue-100 text-blue-800',
                                'shipped' => 'bg-amber-100 text-amber-800',
                                'delivered' => 'bg-emerald-100 text-emerald-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ $do->do_number }}</td>
                            <td class="py-4 px-6 font-mono text-blue-600">{{ $do->salesOrder->so_number }}</td>
                            <td class="py-4 px-6 text-gray-900">{{ $do->salesOrder->customer->name }}</td>
                            <td class="py-4 px-6 text-gray-700">{{ $do->warehouse->warehouse_name }}</td>
                            <td class="py-4 px-6 text-gray-500">{{ $do->delivery_date }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors }}">
                                    {{ ucfirst($do->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2 font-display font-medium text-xs">
                                @if($do->status === 'draft')
                                    <button wire:click="updateStatus({{ $do->id }}, 'ready')" class="text-blue-600 hover:text-blue-900 cursor-pointer">Mark Ready</button>
                                @elseif($do->status === 'ready')
                                    <button wire:click="updateStatus({{ $do->id }}, 'shipped')" class="text-amber-600 hover:text-amber-900 cursor-pointer">Ship Items</button>
                                @elseif($do->status === 'shipped')
                                    <button wire:click="updateStatus({{ $do->id }}, 'delivered')" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">Mark Delivered</button>
                                @endif
                                <button wire:click="edit({{ $do->id }})" class="text-gray-600 hover:text-gray-900 cursor-pointer">View / Edit</button>
                                <button wire:click="delete({{ $do->id }})" wire:confirm="Are you sure you want to delete this DO?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                No delivery orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deliveryOrders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $deliveryOrders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Edit Delivery Order' : 'Create Delivery Order' }}
                        </h3>
                        
                        <div class="mt-4 space-y-4 font-sans text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- DO Number -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">DO Number</label>
                                    <input type="text" wire:model="do_number" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 bg-gray-50" readonly>
                                    @error('do_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Sales Order Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Sales Order Reference</label>
                                    <select wire:model.live="sales_order_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" {{ $isEditMode ? 'disabled' : '' }}>
                                        <option value="">Select Sales Order</option>
                                        @foreach($salesOrders as $so)
                                            <option value="{{ $so->id }}">{{ $so->so_number }} - {{ $so->customer->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sales_order_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Warehouse Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Source Warehouse</label>
                                    <select wire:model="warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                        <option value="">Select Warehouse</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->warehouse_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Delivery Date -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Delivery Date</label>
                                    <input type="date" wire:model="delivery_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    @error('delivery_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Status Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                        <option value="draft">Draft</option>
                                        <option value="ready">Ready to Ship</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Internal Notes / Delivery Instruction</label>
                                <textarea wire:model="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="Add specific shipping constraints or courier detail..."></textarea>
                            </div>

                            <!-- Delivery Items List -->
                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Delivery Items Ledger</h4>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50 font-semibold text-gray-500">
                                            <tr>
                                                <th class="py-2 px-4 text-left">Product Name</th>
                                                <th class="py-2 px-4 text-right">Qty Ordered</th>
                                                <th class="py-2 px-4 text-right">Qty to Deliver</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($items as $index => $item)
                                                <tr>
                                                    <td class="py-2.5 px-4 text-gray-800 font-medium">{{ $item['name'] }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono">{{ number_format($item['qty_ordered'], 2) }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono">
                                                        <input type="number" step="0.01" min="0" max="{{ $item['qty_ordered'] }}" wire:model="items.{{ $index }}.qty_delivered" class="w-20 text-right border border-gray-300 rounded px-1.5 py-0.5">
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-6 text-center text-gray-400 italic">Select a Sales Order to populate items.</td>
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
                            Save Delivery Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
