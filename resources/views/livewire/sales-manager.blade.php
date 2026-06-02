<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Sales Transactions</h1>
            <p class="text-sm text-gray-500 mt-1">Manage quotations, orders, and sales invoicing records.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Invoice
        </button>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by Invoice # or Customer...">
        </div>
        
        <div class="w-full md:w-64">
            <select wire:model.live="status" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Invoice #</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Sale Date</th>
                        <th class="py-3.5 px-6 text-right">Tax (11%)</th>
                        <th class="py-3.5 px-6 text-right">Total Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-medium text-blue-600">{{ $sale->invoice_number }}</td>
                            <td class="py-4 px-6 font-medium text-gray-900">{{ $sale->customer->name }}</td>
                            <td class="py-4 px-6">{{ $sale->sale_date }}</td>
                            <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right font-mono font-semibold text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                @php
                                $badgeClass = match($sale->status) {
                                    'draft'     => 'bg-gray-100 text-gray-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'shipped'   => 'bg-amber-100 text-amber-700',
                                    'delivered' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <button wire:click="edit({{ $sale->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                <button wire:click="delete({{ $sale->id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                No sales invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $isEditMode ? 'Edit Sales Invoice' : 'New Sales Invoice' }}
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Invoice # -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Invoice Number</label>
                                <input type="text" wire:model="invoice_number" readonly class="mt-1 block w-full border border-gray-300 rounded-md bg-gray-50 py-2 px-3 sm:text-sm">
                            </div>

                            <!-- Customer -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Customer</label>
                                <select wire:model="customer_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? 'Individual' }})</option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select wire:model="sale_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="draft">Draft</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Sale Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Sale Date</label>
                                <input type="date" wire:model="sale_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('sale_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="date" wire:model="due_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                            </div>
                        </div>

                        <!-- Item list -->
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-semibold text-gray-900">Line Items</h4>
                                <button type="button" wire:click="addItem" class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                                    Add Item
                                </button>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="pb-2">Product</th>
                                        <th class="pb-2 w-28">Quantity</th>
                                        <th class="pb-2 w-44 text-right">Unit Price</th>
                                        <th class="pb-2 w-44 text-right">Subtotal</th>
                                        <th class="pb-2 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($items as $index => $item)
                                        <tr class="py-2">
                                            <td class="py-2 pr-2">
                                                <select wire:model.live="items.{{ $index }}.product_id" class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm bg-white">
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} (Stock: {{ $p->stock }})</option>
                                                    @endforeach
                                                </select>
                                                @error('items.'.$index.'.product_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="py-2 pr-2">
                                                <input type="number" wire:model.live="items.{{ $index }}.quantity" class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm">
                                                @error('items.'.$index.'.quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </td>
                                            <td class="py-2 pr-2 text-right">
                                                <input type="number" wire:model.live="items.{{ $index }}.unit_price" class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm text-right">
                                            </td>
                                            <td class="py-2 pr-2 text-right font-mono text-sm align-middle pt-3">
                                                Rp {{ number_format($item['total'] ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="py-2 text-center align-middle">
                                                <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 font-medium">
                                                    Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Calculations -->
                        <div class="mt-6 border-t border-gray-200 pt-4 flex flex-col items-end space-y-2">
                            <div class="flex justify-between w-64 text-sm text-gray-500">
                                <span>Subtotal:</span>
                                <span class="font-mono">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between w-64 text-sm text-gray-500">
                                <span>PPN (11%):</span>
                                <span class="font-mono">Rp {{ number_format($tax_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between w-64 text-base font-semibold text-gray-900 border-t border-gray-100 pt-2">
                                <span>Grand Total:</span>
                                <span class="font-mono">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Notes / Terms</label>
                            <textarea wire:model="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="Invoice notes, banking info, etc."></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">
                            Save Invoice
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
