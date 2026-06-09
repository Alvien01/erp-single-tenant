<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Sales Quotations</h1>
            <p class="text-sm text-gray-500 mt-1">Manage price offers and quotations sent to customers.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Quotation
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
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by quotation number or customer...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">SQ Number</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Valid Until</th>
                        <th class="py-3.5 px-6 text-right">Estimated Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($quotations as $sq)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-semibold text-blue-600">{{ $sq->sq_number }}</td>
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $sq->customer->name }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $sq->valid_until }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900">
                                Rp {{ number_format($sq->items->sum('subtotal'), 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $sq->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $sq->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $sq->status === 'converted' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $sq->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($sq->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <a href="{{ route('sales-quotations.pdf.stream', $sq->id) }}" target="_blank" class="text-purple-600 hover:text-purple-900 font-medium cursor-pointer mr-2" title="View PDF">
                                    PDF
                                </a>
                                @if($sq->status === 'draft' || $sq->status === 'sent')
                                    <button wire:click="edit({{ $sq->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="convertToOrder({{ $sq->id }})" class="text-emerald-600 hover:text-emerald-900 font-medium cursor-pointer">Convert to Order</button>
                                    <button wire:click="cancelQuotation({{ $sq->id }})" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Cancel</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                No quotations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $quotations->links() }}
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
                            {{ $isEditMode ? 'Edit Sales Quotation' : 'Add New Sales Quotation' }}
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Customer (Inline Searchable + Create) -->
                            <div class="relative" x-data="{ }" @click.away="$wire.set('showCustomerDropdown', false)">
                                <label class="block text-sm font-medium text-gray-700">Customer</label>

                                @if($selectedCustomerName)
                                    {{-- Selected customer chip --}}
                                    <div class="mt-1 flex items-center justify-between w-full border border-emerald-300 bg-emerald-50 rounded-md py-2 px-3 sm:text-sm">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            <span class="font-medium text-emerald-800">{{ $selectedCustomerName }}</span>
                                        </div>
                                        <button type="button" wire:click="clearCustomer" class="text-gray-400 hover:text-red-500 transition cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </div>
                                @else
                                    {{-- Search input --}}
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </span>
                                        <input
                                            type="text"
                                            wire:model.live.debounce.300ms="customerSearch"
                                            wire:keydown.enter.prevent="createAndSelectCustomer"
                                            class="mt-1 block w-full pl-9 pr-4 border border-gray-300 rounded-md py-2 px-3 sm:text-sm focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Type to search or create new customer..."
                                            autocomplete="off"
                                        >
                                    </div>

                                    {{-- Dropdown results --}}
                                    @if($showCustomerDropdown && $customerSearch)
                                        <div class="absolute z-50 mt-1 w-full bg-white rounded-md shadow-lg border border-gray-200 max-h-60 overflow-y-auto">
                                            @if($filteredCustomers->count() > 0)
                                                @foreach($filteredCustomers as $c)
                                                    <button type="button"
                                                        wire:click="selectCustomer({{ $c->id }})"
                                                        class="w-full text-left px-4 py-2.5 hover:bg-blue-50 transition flex items-center gap-3 cursor-pointer border-b border-gray-100 last:border-0">
                                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                                            <span class="text-blue-700 text-xs font-bold">{{ strtoupper(substr($c->name, 0, 2)) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $c->name }}</div>
                                                            @if($c->company_name)
                                                                <div class="text-xs text-gray-500">{{ $c->company_name }}</div>
                                                            @endif
                                                        </div>
                                                    </button>
                                                @endforeach
                                            @endif

                                            {{-- Create new option --}}
                                            <button type="button"
                                                wire:click="createAndSelectCustomer"
                                                class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 transition flex items-center gap-3 cursor-pointer bg-gray-50 border-t border-gray-200">
                                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-emerald-700">Create "<span class="font-bold">{{ $customerSearch }}</span>"</div>
                                                    <div class="text-xs text-gray-500">Add as new customer & select</div>
                                                </div>
                                            </button>
                                        </div>
                                    @endif
                                @endif
                                @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Valid Until -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Valid Until</label>
                                <input type="date" wire:model="valid_until" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('valid_until') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="draft">Draft</option>
                                    <option value="sent">Sent</option>
                                    <option value="converted">Converted</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                        </div>

                        <!-- Quotation Items -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-900 font-display">Items / Products</h4>
                                <button type="button" wire:click="addItem" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                                    + Add Item
                                </button>
                            </div>

                            <div class="space-y-4">
                                @foreach($items as $index => $item)
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                                        <!-- Product Selection -->
                                        <div class="md:col-span-3">
                                            <label class="block text-xs text-gray-500">Product</label>
                                            <select wire:model.live="items.{{ $index }}.product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm bg-white">
                                                <option value="">Select Product</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0) }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Qty</label>
                                            <input type="number" step="0.01" wire:model.live.debounce.300ms="items.{{ $index }}.qty" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                                        </div>

                                        <!-- Unit Price -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Unit Price</label>
                                            <input type="number" step="0.01" wire:model.live.debounce.300ms="items.{{ $index }}.price" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                                        </div>

                                        <!-- Discount -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Discount (Rp)</label>
                                            <input type="number" step="1" wire:model.live.debounce.300ms="items.{{ $index }}.discount" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                                        </div>

                                        <!-- Total Price -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Total Price</label>
                                            <div class="mt-1 block w-full py-1.5 px-3 bg-gray-50 border border-gray-200 rounded-md text-sm font-mono text-right text-gray-700">
                                                Rp {{ number_format(((float)($item['qty'] ?? 0) * (float)($item['price'] ?? 0)) - (float)($item['discount'] ?? 0), 0, ',', '.') }}
                                            </div>
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
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display">
                            Save Quotation
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
