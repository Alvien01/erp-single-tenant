<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Good Receipts</h1>
            <p class="text-sm text-gray-500 mt-1">Record incoming products at warehouses and update inventory stock.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Receive Goods
        </button>
    </div>

    <!-- Alerts -->
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

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by receipt number or supplier...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">GR Number</th>
                        <th class="py-3.5 px-6">Supplier</th>
                        <th class="py-3.5 px-6">Reference / PO</th>
                        <th class="py-3.5 px-6 text-right">Items Quantity</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($receipts as $gr)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-semibold text-blue-600">{{ $gr->gr_number }}</td>
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $gr->supplier->name }}</td>
                            <td class="py-4 px-6 text-gray-600 font-mono">{{ $gr->reference ?: '-' }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900">
                                {{ number_format($gr->items->sum('qty_received'), 2) }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $gr->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $gr->status === 'received' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $gr->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($gr->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($gr->status === 'draft')
                                    <button wire:click="edit({{ $gr->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit / Process</button>
                                @else
                                    <span class="text-gray-400 font-medium">Locked</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                No good receipts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($receipts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
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
                            {{ $isEditMode ? 'Edit Good Receipt' : 'Receive New Goods' }}
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Reference Purchase Order -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reference PO</label>
                                <select wire:model.live="purchase_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select PO (Optional)</option>
                                    @foreach($purchaseOrders as $po)
                                        <option value="{{ $po->id }}">{{ $po->purchase_number }} ({{ $po->supplier->name }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Supplier -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                <select wire:model="supplier_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Reference text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Reference Invoice / DO</label>
                                <input type="text" wire:model="reference" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('reference') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="draft">Draft</option>
                                    <option value="received">Received (Increases Stock)</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                        </div>

                        <!-- GR Items -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-900 font-display">Receipt Items</h4>
                                <button type="button" wire:click="addItem" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                    + Add Item
                                </button>
                            </div>

                            <div class="space-y-4">
                                @foreach($items as $index => $item)
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                                        <!-- Product Selection -->
                                        <div class="md:col-span-4">
                                            <label class="block text-xs text-gray-500">Product</label>
                                            <select wire:model="items.{{ $index }}.product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm bg-white">
                                                <option value="">Select Product</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Warehouse Destination -->
                                        <div class="md:col-span-3">
                                            <label class="block text-xs text-gray-500">Destination Warehouse</label>
                                            <select wire:model="items.{{ $index }}.warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm bg-white">
                                                <option value="">Select Warehouse</option>
                                                @foreach($warehouses as $w)
                                                    <option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Quantity Received -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Qty Received</label>
                                            <input type="number" step="0.01" wire:model="items.{{ $index }}.qty_received" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                                        </div>

                                        <!-- Unit Price -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Unit Price (Rp)</label>
                                            <input type="number" step="0.01" wire:model="items.{{ $index }}.unit_price" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                                        </div>

                                        <!-- Actions -->
                                        <div class="md:col-span-1 text-center mt-4">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                            Process Good Receipt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
