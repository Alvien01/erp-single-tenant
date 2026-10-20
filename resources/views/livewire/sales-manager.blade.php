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
                        <th class="py-3.5 px-6 text-right">Subtotal (DPP)</th>
                        <th class="py-3.5 px-6 text-right">Tax (Pajak)</th>
                        <th class="py-3.5 px-6 text-right">Grand Total</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-medium text-blue-600">{{ $sale->invoice_number }}</td>
                            <td class="py-4 px-6 font-medium text-gray-900">{{ $sale->customer->name ?? ($sale->customer_name ?? '-') }}</td>
                            <td class="py-4 px-6">{{ $sale->sale_date }}</td>
                            <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
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
                                <a href="{{ route('sales.pdf.stream', $sale->id) }}" target="_blank" class="text-purple-600 hover:text-purple-900 font-medium cursor-pointer inline-flex items-center" title="Cetak PDF">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    PDF
                                </a>
                                <button wire:click="edit({{ $sale->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                <button wire:click="delete({{ $sale->id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
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

    <!-- Modal Form (Create / Edit & Preview) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $isPreview ? 'sm:max-w-4xl' : 'sm:max-w-4xl' }} sm:w-full sm:p-6 max-h-[95vh] overflow-y-auto">
                    
                    <!-- Modal Header with Tab Switcher -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-title">
                                {{ $isEditMode ? 'Edit Sales Invoice' : 'New Sales Invoice' }}
                            </h3>
                            
                            <!-- Tab Switcher -->
                            <div class="inline-flex p-1 bg-gray-100 rounded-lg text-xs font-medium">
                                <button type="button" 
                                        wire:click="closePreview" 
                                        class="px-3 py-1 rounded-md transition-all cursor-pointer {{ !$isPreview ? 'bg-white shadow-sm text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}">
                                    Form Input
                                </button>
                                <button type="button" 
                                        wire:click="openPreview" 
                                        class="px-3 py-1 rounded-md transition-all cursor-pointer flex items-center gap-1.5 {{ $isPreview ? 'bg-white shadow-sm text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-900' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Preview Invoice
                                </button>
                            </div>
                        </div>

                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if(!$isPreview)
                        <!-- ================= FORM INPUT VIEW ================= -->
                        <div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Invoice # -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Invoice Number</label>
                                    <input type="text" wire:model="invoice_number" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white" placeholder="e.g. INVC/RAM/I/20261019">
                                    @error('invoice_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Customer Selection / Manual Input -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                                        <button type="button" wire:click="toggleCustomerMode" class="text-xs text-blue-600 hover:text-blue-800 font-medium inline-flex items-center gap-1 cursor-pointer">
                                            @if($customer_mode === 'custom')
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                                Pilih dari Daftar
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                Input Manual
                                            @endif
                                        </button>
                                    </div>

                                    @if($customer_mode === 'custom')
                                        <input type="text" wire:model="customer_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nama customer...">
                                        @error('customer_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    @else
                                        <select wire:model.live="customer_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->company_name ?? 'Individual' }})</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    @endif
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
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-semibold text-gray-900">Line Items</h4>
                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-medium">{{ count($items) }} item</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                wire:click="roundAllSubtotals" 
                                                class="inline-flex items-center px-2.5 py-1.5 border border-emerald-300 shadow-sm text-xs font-semibold rounded text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:outline-none cursor-pointer transition-colors"
                                                title="Bulatkan semua subtotal produk ke ribuan terdekat">
                                            <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                            Bulatkan Semua
                                        </button>
                                        <button type="button" 
                                                wire:click="autoAllSubtotals" 
                                                class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none cursor-pointer transition-colors"
                                                title="Kembalikan semua subtotal ke kalkulasi otomatis (Qty × Unit Price)">
                                            <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            Reset Otomatis
                                        </button>
                                        <button type="button" wire:click="addItem" class="inline-flex items-center px-2.5 py-1.5 border border-blue-600 shadow-sm text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none cursor-pointer transition-colors">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Add Item
                                        </button>
                                    </div>
                                </div>

                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            <th class="pb-2">Product</th>
                                            <th class="pb-2 w-32">Model</th>
                                            <th class="pb-2 w-20">Quantity</th>
                                            <th class="pb-2 w-32 text-right">Unit Price</th>
                                            <th class="pb-2 w-52 text-right">Subtotal</th>
                                            <th class="pb-2 w-16"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($items as $index => $item)
                                            <tr class="py-2">
                                                <td class="py-2 pr-2">
                                                    <div class="flex items-center gap-1.5">
                                                        @if(!empty($item['is_custom']))
                                                            <input type="text" 
                                                                   wire:model.live="items.{{ $index }}.product_name" 
                                                                   placeholder="Ketik nama produk..." 
                                                                   class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                            <button type="button" 
                                                                    wire:click="toggleProductMode({{ $index }})" 
                                                                    class="px-2 py-1.5 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded whitespace-nowrap inline-flex items-center gap-1 cursor-pointer transition-colors"
                                                                    title="Pilih dari daftar produk katalog">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                                                Pilih
                                                            </button>
                                                        @else
                                                            <select wire:model.live="items.{{ $index }}.product_id" 
                                                                    class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                                <option value="">Pilih Produk</option>
                                                                @foreach($products as $p)
                                                                    <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} (Stock: {{ $p->stock }})</option>
                                                                @endforeach
                                                            </select>
                                                            <button type="button" 
                                                                    wire:click="toggleProductMode({{ $index }})" 
                                                                    class="px-2 py-1.5 text-xs font-medium text-purple-600 hover:text-purple-800 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded whitespace-nowrap inline-flex items-center gap-1 cursor-pointer transition-colors"
                                                                    title="Ketik nama produk sendiri">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                                Manual
                                                            </button>
                                                        @endif
                                                    </div>
                                                    @error('items.'.$index.'.product_name') <span class="text-red-500 text-xs mt-0.5 block">{{ $message }}</span> @enderror
                                                    @error('items.'.$index.'.product_id') <span class="text-red-500 text-xs mt-0.5 block">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="py-2 pr-2">
                                                    <input type="text" 
                                                           wire:model.live.debounce.150ms="items.{{ $index }}.model" 
                                                           placeholder="Model / SKU" 
                                                           class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                    @error('items.'.$index.'.model') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="py-2 pr-2">
                                                    <input type="number" 
                                                           step="any" 
                                                           min="0"
                                                           wire:model.live.debounce.150ms="items.{{ $index }}.quantity" 
                                                           class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                    @error('items.'.$index.'.quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="py-2 pr-2 text-right">
                                                    <input type="number" 
                                                           step="any" 
                                                           min="0"
                                                           wire:model.live.debounce.150ms="items.{{ $index }}.unit_price" 
                                                           class="w-full border border-gray-300 rounded-md py-1.5 px-2 text-sm text-right focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                                    @error('items.'.$index.'.unit_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="py-2 pr-2 text-right align-top">
                                                    <div class="flex items-center gap-1 justify-end">
                                                        <div class="relative w-36 rounded-md shadow-sm">
                                                            <span class="absolute inset-y-0 left-0 pl-2 flex items-center text-xs font-semibold text-gray-400">Rp</span>
                                                            <input type="number" 
                                                                   step="any" 
                                                                   min="0"
                                                                   wire:model.live.debounce.250ms="items.{{ $index }}.total" 
                                                                   placeholder="0"
                                                                   title="Edit subtotal secara manual"
                                                                   class="w-full border rounded-md py-1.5 pl-7 pr-2 text-xs text-right font-mono font-semibold focus:outline-none focus:ring-1 transition-colors {{ !empty($item['is_rounded']) ? 'border-emerald-400 bg-emerald-50/50 text-emerald-900 focus:ring-emerald-500' : (!empty($item['is_custom_total']) ? 'border-amber-400 bg-amber-50/50 text-amber-900 focus:ring-amber-500' : 'border-gray-300 bg-white text-gray-900 focus:ring-blue-500') }}">
                                                        </div>
                                                        
                                                        <!-- Button Bulatkan -->
                                                        <button type="button" 
                                                                wire:click="roundItemSubtotal({{ $index }})" 
                                                                title="Bulatkan subtotal ke ribuan terdekat"
                                                                class="px-1.5 py-1 text-[11px] font-semibold border rounded transition-colors cursor-pointer {{ !empty($item['is_rounded']) ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-emerald-700 hover:bg-emerald-50 border-gray-300' }}">
                                                            .000
                                                        </button>
                                                        
                                                        <!-- Button Auto -->
                                                        <button type="button" 
                                                                wire:click="autoItemSubtotal({{ $index }})" 
                                                                title="Kembalikan ke kalkulasi otomatis (Qty × Unit Price)"
                                                                class="p-1 text-xs border rounded transition-colors cursor-pointer {{ (empty($item['is_custom_total']) && empty($item['is_rounded'])) ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-gray-500 hover:bg-gray-100 border-gray-300' }}">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                        </button>
                                                    </div>
                                                    <div class="mt-0.5 text-right">
                                                        @if(!empty($item['is_rounded']))
                                                            <span class="text-[10px] text-emerald-700 font-semibold inline-flex items-center gap-0.5">
                                                                <svg class="w-2.5 h-2.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                                Dibulatkan
                                                            </span>
                                                        @elseif(!empty($item['is_custom_total']))
                                                            <span class="text-[10px] text-amber-700 font-semibold inline-flex items-center gap-0.5">
                                                                <svg class="w-2.5 h-2.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                                Diedit Manual
                                                            </span>
                                                        @else
                                                            <span class="text-[10px] text-gray-400">Otomatis</span>
                                                        @endif
                                                    </div>
                                                    @error('items.'.$index.'.total') <span class="text-red-500 text-xs block mt-0.5">{{ $message }}</span> @enderror
                                                </td>
                                                <td class="py-2 text-center align-middle">
                                                    <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">
                                                        Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Bottom Section: Notes & Rincian Pembayaran -->
                            <div class="mt-6 border-t border-gray-200 pt-5 grid grid-cols-1 lg:grid-cols-12 gap-6">
                                <!-- Left Column: Notes & Information -->
                                <div class="lg:col-span-5 flex flex-col justify-between space-y-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                            Catatan / Ketentuan (Notes / Terms)
                                        </label>
                                        <textarea wire:model="notes" rows="4" class="block w-full border border-gray-300 rounded-lg p-2.5 text-xs bg-white text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm" placeholder="Contoh: Pembayaran tempo 14 hari, transfer bank ke rekening CV Radi Amartha Mahardika..."></textarea>
                                    </div>

                                    <div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-1.5">
                                        <div class="font-semibold flex items-center gap-1.5 text-blue-900">
                                            <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                            Kalkulasi Otomatis
                                        </div>
                                        <p class="text-[11px] leading-relaxed text-blue-800">
                                            <strong>Grand Total</strong> dihitung langsung dari total subtotal produk di atas (dikurangi Diskon, ditambah Pajak & Ongkir). Nilai <strong>Subtotal (DPP)</strong> dibiarkan 0.
                                        </p>
                                    </div>
                                </div>

                                <!-- Right Column: Rincian Pembayaran Card -->
                                <div class="lg:col-span-7">
                                    <div class="bg-gray-50/90 border border-gray-200 rounded-xl p-4 sm:p-5 shadow-sm space-y-3.5">
                                        <div class="flex items-center justify-between pb-2 border-b border-gray-200">
                                            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                Rincian Pembayaran
                                            </h4>
                                            <span class="text-[11px] font-normal text-gray-500">Kalkulasi Otomatis / Manual</span>
                                        </div>

                                        <!-- Subtotal Produk (Total akumulasi dari produk) -->
                                        <div class="bg-blue-50/60 border border-blue-200 rounded-lg p-3">
                                            <div class="flex items-center justify-between mb-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-xs font-bold text-gray-800">Subtotal Produk</span>
                                                    <span class="text-[11px] text-gray-500">({{ count($items) }} item)</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <button type="button" 
                                                            wire:click="roundAllSubtotals" 
                                                            class="px-2 py-0.5 text-[11px] font-semibold text-emerald-700 bg-white hover:bg-emerald-50 border border-emerald-300 rounded shadow-xs transition-colors cursor-pointer"
                                                            title="Bulatkan semua subtotal produk">
                                                        Bulatkan Semua
                                                    </button>
                                                    <button type="button" 
                                                            wire:click="autoAllSubtotals" 
                                                            class="px-2 py-0.5 text-[11px] font-semibold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded shadow-xs transition-colors cursor-pointer"
                                                            title="Reset semua ke perhitungan Qty × Harga">
                                                        Auto Semua
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-[11px] text-blue-800">Total nilai barang / jasa</span>
                                                <span class="text-base font-bold font-mono text-blue-950">
                                                    Rp {{ number_format($this->getItemsSubtotal(), 0, ',', '.') }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Subtotal (DPP) -->
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <label class="text-xs font-semibold text-gray-700">Subtotal (DPP)</label>
                                                <div class="flex items-center gap-1">
                                                    <button type="button" 
                                                            wire:click="roundDppSubtotal" 
                                                            class="text-[10px] font-medium text-emerald-700 hover:text-emerald-800 underline cursor-pointer"
                                                            title="Bulatkan nominal DPP ke ribuan">
                                                        Bulatkan
                                                    </button>
                                                    <span class="text-[11px] text-gray-400">|</span>
                                                    <span class="text-[11px] text-gray-500">Dasar Pengenaan Pajak</span>
                                                </div>
                                            </div>
                                            <div class="relative rounded-lg shadow-sm">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">Rp</span>
                                                <input type="number" 
                                                       step="any" 
                                                       min="0" 
                                                       wire:model.live.debounce.150ms="total_amount" 
                                                       placeholder="0"
                                                       class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-3 text-sm text-right font-mono font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            </div>
                                        </div>

                                        <!-- Diskon -->
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <label class="text-xs font-semibold text-gray-700">Diskon</label>
                                                <span class="text-[11px] text-gray-500">Potongan harga invoice</span>
                                            </div>
                                            <div class="relative rounded-lg shadow-sm">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-red-500">- Rp</span>
                                                <input type="number" 
                                                       step="any" 
                                                       min="0" 
                                                       wire:model.live.debounce.150ms="discount_amount" 
                                                       placeholder="0"
                                                       class="w-full border border-gray-300 rounded-lg py-2 pl-12 pr-3 text-sm text-right font-mono font-semibold text-red-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            </div>
                                        </div>

                                        <!-- Pajak (Pilihan Tarif & Input Nominal) -->
                                        <div>
                                            <div class="flex items-center justify-between mb-1.5">
                                                <label class="text-xs font-semibold text-gray-700">Pajak</label>
                                                <span class="text-[11px] text-gray-500">Pilih tarif & sesuaikan nominal</span>
                                            </div>

                                            <!-- Opsi Pilihan Tarif Pajak -->
                                            <div class="grid grid-cols-3 gap-1.5 mb-2">
                                                <button type="button" 
                                                        wire:click="setTaxType('pph_05')" 
                                                        class="py-1.5 px-2 text-xs font-semibold rounded-lg border transition-all text-center cursor-pointer {{ $tax_type === 'pph_05' ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                                                    PPh (0,5%)
                                                </button>
                                                <button type="button" 
                                                        wire:click="setTaxType('ppn_12')" 
                                                        class="py-1.5 px-2 text-xs font-semibold rounded-lg border transition-all text-center cursor-pointer {{ ($tax_type === 'ppn_12' || $tax_type === 'ppn_11') ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                                                    PPN (12%)
                                                </button>
                                                <button type="button" 
                                                        wire:click="setTaxType('none')" 
                                                        class="py-1.5 px-2 text-xs font-semibold rounded-lg border transition-all text-center cursor-pointer {{ $tax_type === 'none' ? 'bg-blue-600 border-blue-600 text-white shadow-sm' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                                                    Bebas Pajak (0%)
                                                </button>
                                            </div>

                                            <!-- Input Nominal Pajak -->
                                            <div class="relative rounded-lg shadow-sm">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">Rp</span>
                                                <input type="number" 
                                                       step="any" 
                                                       min="0" 
                                                       wire:model.live.debounce.150ms="tax_amount" 
                                                       placeholder="0"
                                                       class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-3 text-sm text-right font-mono font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            </div>
                                        </div>

                                        <!-- Shipping Price -->
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <label class="text-xs font-semibold text-gray-700">Shipping Price</label>
                                                <span class="text-[11px] text-gray-500">Biaya pengiriman / ongkir</span>
                                            </div>
                                            <div class="relative rounded-lg shadow-sm">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">+ Rp</span>
                                                <input type="number" 
                                                       step="any" 
                                                       min="0" 
                                                       wire:model.live.debounce.150ms="shipping_price" 
                                                       placeholder="0"
                                                       class="w-full border border-gray-300 rounded-lg py-2 pl-12 pr-3 text-sm text-right font-mono font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            </div>
                                        </div>

                                        <!-- Grand Total Card -->
                                        <div class="pt-2">
                                            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-xl p-4 text-white shadow-md flex items-center justify-between">
                                                <div>
                                                    <div class="text-[11px] uppercase tracking-wider text-blue-200 font-semibold">Grand Total</div>
                                                    <div class="text-[10px] text-blue-100 opacity-90 mt-0.5">DPP - Diskon + Pajak + Shipping</div>
                                                </div>
                                                <div class="text-xl sm:text-2xl font-bold font-mono text-white tracking-tight">
                                                    Rp {{ number_format($grand_total, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Footer Actions -->
                        <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                            <div class="flex items-center gap-2">
                                @if($isEditMode && $sale_id)
                                    <a href="{{ route('sales.pdf.stream', $sale_id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-purple-300 shadow-sm text-sm font-medium rounded-md text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak PDF
                                    </a>
                                @endif
                                <button type="button" wire:click="openPreview" class="inline-flex items-center px-3.5 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none cursor-pointer">
                                    <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Preview Invoice
                                </button>
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                                    Cancel
                                </button>
                                <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">
                                    Save Invoice
                                </button>
                            </div>
                        </div>

                    @else
                        <!-- ================= LIVE INVOICE PREVIEW VIEW ================= -->
                        <div class="mt-4">
                            <!-- Info Notice Banner -->
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between text-xs text-blue-700">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    <span>Live Preview ini menampilkan format proforma invoice secara langsung sesuai data yang Anda masukkan sebelum disimpan.</span>
                                </div>
                                <button type="button" onclick="printInvoicePreview()" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium inline-flex items-center gap-1 shadow-sm transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    Cetak Preview
                                </button>
                            </div>

                            <!-- Paper Sheet Box -->
                            <div class="bg-gray-100 p-3 sm:p-6 rounded-xl overflow-x-auto border border-gray-200">
                                <div id="invoice-printable-area" class="bg-white mx-auto shadow-md border border-gray-300 p-6 sm:p-10 text-black max-w-[760px]" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.4;">
                                    
                                    <!-- Company Header -->
                                    <table class="w-full mb-3" style="border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 115px; vertical-align: middle;">
                                                @if(file_exists(public_path('assets/logo.png')))
                                                    <img src="{{ asset('assets/logo.png') }}" class="h-16 w-auto object-contain" alt="Logo">
                                                @endif
                                            </td>
                                            <td style="padding-left: 12px; vertical-align: middle;">
                                                <div class="font-bold text-black uppercase tracking-wide text-sm leading-tight">
                                                    {{ config('app.company_name', 'CV. RADI AMARTHA MAHARDIKA') }}
                                                </div>
                                                <div class="text-[11px] italic text-gray-700 mt-0.5">
                                                    "Your Trusted Industrial & Lab Equipment Partner"
                                                </div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Title -->
                                    <div class="text-center font-bold text-sm my-3 tracking-wider uppercase text-black">
                                        PROFORMA INVOICE
                                    </div>

                                    <!-- Metadata & Customer Info -->
                                    @php
                                        $selectedCustomer = ($customer_mode === 'select' && $customer_id) ? \App\Models\Customer::find($customer_id) : null;
                                        $custName = $customer_mode === 'select' ? ($selectedCustomer?->name ?? '—') : ($customer_name ?: '—');
                                        $custCompany = $selectedCustomer?->company_name ?? '';
                                        $custAddress = $selectedCustomer?->address ?? '—';
                                        $custEmail = $selectedCustomer?->email ?? '—';
                                        $custPhone = $selectedCustomer?->phone ?? '—';
                                        $formattedDate = $sale_date ? \Carbon\Carbon::parse($sale_date)->locale('id')->translatedFormat('d F Y') : now()->locale('id')->translatedFormat('d F Y');
                                    @endphp
                                    <table class="w-full text-xs mb-3" style="line-height: 1.5; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 70px;">Nomor</td>
                                            <td style="width: 15px;">:</td>
                                            <td class="font-mono font-medium">{{ $invoice_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>:</td>
                                            <td>{{ $formattedDate }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="pt-2 font-medium">Kepada Yth.</td>
                                        </tr>
                                        <tr>
                                            <td>Nama</td>
                                            <td>:</td>
                                            <td class="font-semibold">
                                                {{ $custName }}
                                                @if(!empty($custCompany) && ($selectedCustomer?->type ?? '') === 'company')
                                                    - {{ $custCompany }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Alamat</td>
                                            <td>:</td>
                                            <td>{{ $custAddress }}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>:</td>
                                            <td>{{ $custEmail }}</td>
                                        </tr>
                                        <tr>
                                            <td>No. Hp</td>
                                            <td>:</td>
                                            <td>{{ $custPhone }}</td>
                                        </tr>
                                    </table>

                                    <!-- Line Items Table -->
                                    <table class="w-full text-xs" style="border-collapse: collapse;">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="border border-black px-2 py-1 text-center font-bold" style="width: 5%;">No.</th>
                                                <th class="border border-black px-2 py-1 text-left font-bold" style="width: 37%;">Product</th>
                                                <th class="border border-black px-2 py-1 text-center font-bold" style="width: 16%;">Model</th>
                                                <th class="border border-black px-2 py-1 text-right font-bold" style="width: 16%;">Harga/unit</th>
                                                <th class="border border-black px-2 py-1 text-center font-bold" style="width: 8%;">QTY</th>
                                                <th class="border border-black px-2 py-1 text-right font-bold" style="width: 18%;">Total Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $i => $item)
                                                @php
                                                    $pModel = null;
                                                    if (empty($item['is_custom']) && !empty($item['product_id'])) {
                                                        $pModel = \App\Models\Product::find($item['product_id']);
                                                    }
                                                    $pName = !empty($item['is_custom']) ? ($item['product_name'] ?: 'Item Custom') : ($pModel?->name ?? ($item['product_name'] ?: '—'));
                                                    $itemModel = !empty($item['model']) ? $item['model'] : ($pModel?->sku ?: ($pModel?->unit ?: '—'));
                                                    $rowSubtotal = is_numeric($item['total'] ?? null) ? floatval($item['total']) : (floatval($item['quantity'] ?? 0) * floatval($item['unit_price'] ?? 0));
                                                @endphp
                                                <tr>
                                                    <td class="border border-black px-2 py-1 text-center">{{ $i + 1 }}</td>
                                                    <td class="border border-black px-2 py-1 text-left">{{ $pName }}</td>
                                                    <td class="border border-black px-2 py-1 text-center">{{ $itemModel }}</td>
                                                    <td class="border border-black px-2 py-1 text-right font-mono">Rp{{ number_format(floatval($item['unit_price'] ?? 0), 0, ',', '.') }}</td>
                                                    <td class="border border-black px-2 py-1 text-center">{{ (int)($item['quantity'] ?? 0) }}</td>
                                                    <td class="border border-black px-2 py-1 text-right font-mono">Rp{{ number_format($rowSubtotal, 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach

                                            <!-- Bottom calculation rows -->
                                            <tr>
                                                <td colspan="5" class="border border-black px-2 py-1 text-center font-bold">Subtotal (DPP)</td>
                                                <td class="border border-black px-2 py-1 text-right font-mono">Rp{{ number_format($total_amount, 0, ',', '.') }}</td>
                                            </tr>

                                            @if(($discount_amount ?? 0) > 0)
                                            <tr>
                                                <td colspan="5" class="border border-black px-2 py-1 text-center font-bold">Diskon</td>
                                                <td class="border border-black px-2 py-1 text-right font-mono">- Rp{{ number_format($discount_amount, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif

                                            @php
                                                $previewTaxLabel = match($tax_type) {
                                                    'pph_05' => 'PPh 0,5%',
                                                    'ppn_12', 'ppn_11' => 'PPN 12%',
                                                    'none' => 'Bebas Pajak (0%)',
                                                    default => 'PPh 0,5%'
                                                };
                                            @endphp
                                            <tr>
                                                <td colspan="5" class="border border-black px-2 py-1 text-center font-bold">{{ $previewTaxLabel }}</td>
                                                <td class="border border-black px-2 py-1 text-right font-mono">Rp{{ number_format($tax_amount, 0, ',', '.') }}</td>
                                            </tr>

                                            @if(($shipping_price ?? 0) > 0)
                                            <tr>
                                                <td colspan="5" class="border border-black px-2 py-1 text-center font-bold">Shipping Price</td>
                                                <td class="border border-black px-2 py-1 text-right font-mono">Rp{{ number_format($shipping_price, 0, ',', '.') }}</td>
                                            </tr>
                                            @endif

                                            <tr>
                                                <td colspan="5" class="border border-black px-2 py-1 text-center font-bold">Total Harga</td>
                                                <td class="border border-black px-2 py-1 text-right font-bold font-mono">Rp{{ number_format($grand_total, 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Terbilang & Bank Info Box -->
                                    <div class="border border-black border-t-0 p-2 text-xs" style="line-height: 1.45;">
                                        <div class="font-bold mb-1.5 uppercase">
                                            # {{ \App\Helpers\Terbilang::rupiah($grand_total) }} #
                                        </div>
                                        <div class="font-bold mt-1">Informasi Bank:</div>
                                        <div class="font-bold">{{ config('app.company_bank_info', 'BLU By BCA Digital — 090190400040') }}</div>
                                        <div class="font-bold">Atas Nama: {{ config('app.company_bank_holder', 'MUHAMMAD RIZKY RAMADHAN') }}</div>
                                    </div>

                                    <!-- Signature Block -->
                                    <table class="w-full mt-6 text-xs" style="border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 55%;"></td>
                                            <td style="width: 45%; text-align: center;">
                                                <div class="text-xs">CV Radi Amartha Mahardika</div>
                                                <div style="height: 50px;"></div>
                                                <div class="font-bold underline text-xs">{{ config('app.company_director', 'Rediana Putri Nurrohma, S. Biotek') }}</div>
                                                <div class="text-xs">{{ config('app.company_director_title', 'Direktur') }}</div>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- Bottom Company Footer -->
                                    <div class="mt-6 pt-3 text-[10px] text-gray-700 leading-tight" style="border-top: 1px dashed #ccc;">
                                        <div class="font-bold text-black">{{ config('app.company_name', 'CV. RADI AMARTHA MAHARDIKA') }}</div>
                                        <div class="mt-0.5">{{ config('app.company_address', 'Jalan E2 No. C1, Komp. BAKN, Sumur Batu, Kemayoran, Jakarta Pusat') }}</div>
                                        <div class="mt-0.5">Telp: {{ config('app.company_phone', '+62-888-0868-6293') }} | Email: {{ config('app.company_email', 'Marketing@radiamartha.com') }} | Web: {{ config('app.company_website', 'Radiamartha.com') }}</div>
                                    </div>

                                </div>
                            </div>

                            <!-- Preview Bottom Actions -->
                            <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                                <button type="button" wire:click="closePreview" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none cursor-pointer">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                                    Kembali ke Form
                                </button>

                                <div class="flex items-center space-x-3">
                                    <button type="button" onclick="printInvoicePreview()" class="inline-flex items-center px-4 py-2 border border-blue-600 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none cursor-pointer">
                                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Cetak / Print
                                    </button>
                                    <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none cursor-pointer">
                                        Save Invoice
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif

    <!-- Print JavaScript Helper -->
    <script>
    function printInvoicePreview() {
        const printEl = document.getElementById('invoice-printable-area');
        if (!printEl) return;
        const printWindow = window.open('', '_blank', 'width=950,height=800');
        printWindow.document.write('<!DOCTYPE html><html><head><title>Print Proforma Invoice</title>');
        printWindow.document.write('<style>');
        printWindow.document.write(`
            @page { size: A4 portrait; margin: 12mm 15mm; }
            body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px; color: #000; line-height: 1.35; margin: 0; padding: 0; background: #fff; }
            table { width: 100%; border-collapse: collapse; }
            .border { border: 1.5px solid #000 !important; }
            .border-black { border: 1.5px solid #000 !important; }
            .border-t-0 { border-top: none !important; }
            th, td { padding: 4px 5px; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-left { text-align: left; }
            .font-bold { font-weight: bold; }
            .font-semibold { font-weight: 600; }
            .font-mono { font-family: monospace; }
            .uppercase { text-transform: uppercase; }
            .underline { text-decoration: underline; }
            .italic { font-style: italic; }
        `);
        printWindow.document.write('</style></head><body>');
        printWindow.document.write(printEl.innerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 350);
    }
    </script>
</div>
