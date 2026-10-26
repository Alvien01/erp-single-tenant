<div class="space-y-6 pb-12">
    <!-- Breadcrumb & Top Bar -->
    <div class="mb-4">
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('sales') }}" wire:navigate class="hover:text-blue-600 transition-colors">Sales</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ $isEditMode ? 'Edit Invoice (' . $invoice_number . ')' : 'Buat Invoice Baru' }}</span>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('sales') }}" 
                   wire:navigate
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 shadow-2xs transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                        {{ $isEditMode ? 'Edit Sales Invoice: ' . $invoice_number : 'Buat Sales Invoice Baru' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $isEditMode ? 'Perbarui informasi transaksi penjualan, produk, diskon, pajak, dan rincian pembayaran.' : 'Formulir pencatatan transaksi penjualan, pesanan barang, dan faktur komersial.' }}
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions & Tab Switcher -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Tab Switcher -->
                <div class="inline-flex p-1 bg-gray-100 rounded-lg text-xs font-medium border border-gray-200">
                    <button type="button" 
                            wire:click="closePreview" 
                            class="px-3 py-1.5 rounded-md transition-all cursor-pointer {{ !$isPreview ? 'bg-white shadow-xs text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                        Form Input
                    </button>
                    <button type="button" 
                            wire:click="openPreview" 
                            class="px-3 py-1.5 rounded-md transition-all cursor-pointer flex items-center gap-1.5 {{ $isPreview ? 'bg-white shadow-xs text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Preview Invoice
                    </button>
                </div>

                <button type="button" 
                        wire:click="cancel" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="button" 
                        wire:click="save" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-xs transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center gap-1.5 cursor-pointer">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Invoice' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(!$isPreview)
        <!-- ================= MAIN FORM INPUT VIEW ================= -->
        <div class="space-y-6">
            <!-- Card 1: Informasi Faktur & Customer -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">1</span>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Informasi Faktur & Customer</h3>
                    </div>
                    <span class="text-xs text-gray-400 font-mono">{{ $invoice_number }}</span>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Invoice # -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Invoice Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   wire:model="invoice_number" 
                                   class="block w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white font-mono" 
                                   placeholder="e.g. INVC/RAM/I/20261019">
                            @error('invoice_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Customer Selection / Manual Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                    Customer <span class="text-red-500">*</span>
                                </label>
                                <button type="button" 
                                        wire:click="toggleCustomerMode" 
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium inline-flex items-center gap-1 cursor-pointer">
                                    @if($customer_mode === 'custom')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                        Pilih dari Master Customer
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        Input Manual
                                    @endif
                                </button>
                            </div>

                            @if($customer_mode === 'custom')
                                <input type="text" 
                                       wire:model="customer_name" 
                                       class="block w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       placeholder="Ketik nama customer atau perusahaan...">
                                @error('customer_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @else
                                <select wire:model.live="customer_id" class="block w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">
                                            {{ $c->name }} {{ $c->company_name ? '('.$c->company_name.')' : '' }} {{ $c->phone ? ' - ' . $c->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            @endif
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Status Transaksi <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="sale_status" class="block w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="draft">Draft</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                            @error('sale_status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-1">
                        <!-- Sale Date -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Tanggal Penjualan (Sale Date) <span class="text-red-500">*</span>
                            </label>
                            <input type="date" wire:model="sale_date" class="block w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @error('sale_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">
                                Jatuh Tempo (Due Date)
                            </label>
                            <input type="date" wire:model="due_date" class="block w-full border border-gray-300 rounded-lg py-2.5 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            @error('due_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Line Items -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">2</span>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Item Barang & Jasa (Line Items)</h3>
                        <span class="ml-2 text-xs px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold">{{ count($items) }} item</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" 
                                wire:click="roundAllSubtotals" 
                                class="inline-flex items-center px-3 py-1.5 border border-emerald-300 shadow-2xs text-xs font-semibold rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:outline-none cursor-pointer transition-colors"
                                title="Bulatkan semua subtotal produk ke ribuan terdekat">
                            <svg class="w-3.5 h-3.5 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Bulatkan Semua
                        </button>
                        <button type="button" 
                                wire:click="autoAllSubtotals" 
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-2xs text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none cursor-pointer transition-colors"
                                title="Kembalikan semua subtotal ke kalkulasi otomatis (Qty × Unit Price)">
                            <svg class="w-3.5 h-3.5 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Reset Otomatis
                        </button>
                        <button type="button" 
                                wire:click="addItem" 
                                class="inline-flex items-center px-3 py-1.5 border border-blue-600 shadow-2xs text-xs font-semibold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none cursor-pointer transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Baris Item
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    <th class="pb-3 min-w-[240px]">Product / Deskripsi</th>
                                    <th class="pb-3 w-32">Model / Unit</th>
                                    <th class="pb-3 w-24">Jumlah (Qty)</th>
                                    <th class="pb-3 w-36 text-right">Harga Satuan</th>
                                    <th class="pb-3 w-56 text-right">Subtotal</th>
                                    <th class="pb-3 w-16 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($items as $index => $item)
                                    <tr class="py-2.5">
                                        <td class="py-3 pr-3">
                                            <div class="flex items-center gap-1.5">
                                                @if(!empty($item['is_custom']))
                                                    <input type="text" 
                                                           wire:model.live="items.{{ $index }}.product_name" 
                                                           placeholder="Ketik nama produk..." 
                                                           class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    <button type="button" 
                                                            wire:click="toggleProductMode({{ $index }})" 
                                                            class="px-2.5 py-2 text-xs font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg whitespace-nowrap inline-flex items-center gap-1 cursor-pointer transition-colors"
                                                            title="Pilih dari daftar produk katalog">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                                        Katalog
                                                    </button>
                                                @else
                                                    <select wire:model.live="items.{{ $index }}.product_id" 
                                                            class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="">-- Pilih Produk --</option>
                                                        @foreach($products as $p)
                                                            <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} (Stok: {{ $p->stock }})</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" 
                                                            wire:click="toggleProductMode({{ $index }})" 
                                                            class="px-2.5 py-2 text-xs font-medium text-purple-600 hover:text-purple-800 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg whitespace-nowrap inline-flex items-center gap-1 cursor-pointer transition-colors"
                                                            title="Ketik nama produk sendiri">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                        Manual
                                                    </button>
                                                @endif
                                            </div>
                                            @error('items.'.$index.'.product_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            @error('items.'.$index.'.product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="py-3 pr-3">
                                            <input type="text" 
                                                   wire:model.live.debounce.150ms="items.{{ $index }}.model" 
                                                   placeholder="Model / SKU" 
                                                   class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('items.'.$index.'.model') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="py-3 pr-3">
                                            <input type="number" 
                                                   step="any" 
                                                   min="0" 
                                                   wire:model.live.debounce.150ms="items.{{ $index }}.quantity" 
                                                   class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('items.'.$index.'.quantity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="py-3 pr-3 text-right">
                                            <input type="number" 
                                                   step="any" 
                                                   min="0" 
                                                   wire:model.live.debounce.150ms="items.{{ $index }}.unit_price" 
                                                   class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm text-right font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            @error('items.'.$index.'.unit_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </td>
                                        <td class="py-3 pr-3 text-right align-top">
                                            <div class="flex items-center gap-1 justify-end">
                                                <div class="relative w-36 rounded-lg shadow-2xs">
                                                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-xs font-semibold text-gray-400">Rp</span>
                                                    <input type="number" 
                                                           step="any" 
                                                           min="0" 
                                                           wire:model.live.debounce.250ms="items.{{ $index }}.total" 
                                                           placeholder="0" 
                                                           title="Edit subtotal secara manual" 
                                                           class="w-full border rounded-lg py-2 pl-8 pr-2.5 text-xs text-right font-mono font-semibold focus:outline-none focus:ring-2 transition-colors {{ !empty($item['is_rounded']) ? 'border-emerald-400 bg-emerald-50/50 text-emerald-900 focus:ring-emerald-500' : (!empty($item['is_custom_total']) ? 'border-amber-400 bg-amber-50/50 text-amber-900 focus:ring-amber-500' : 'border-gray-300 bg-white text-gray-900 focus:ring-blue-500') }}">
                                                </div>
                                                
                                                <!-- Button Bulatkan -->
                                                <button type="button" 
                                                        wire:click="roundItemSubtotal({{ $index }})" 
                                                        title="Bulatkan subtotal ke ribuan terdekat" 
                                                        class="px-2 py-2 text-[11px] font-semibold border rounded-lg transition-colors cursor-pointer {{ !empty($item['is_rounded']) ? 'bg-emerald-600 text-white border-emerald-600 shadow-2xs' : 'bg-white text-emerald-700 hover:bg-emerald-50 border-gray-300' }}">
                                                    .000
                                                </button>
                                                
                                                <!-- Button Auto -->
                                                <button type="button" 
                                                        wire:click="autoItemSubtotal({{ $index }})" 
                                                        title="Kembalikan ke kalkulasi otomatis (Qty × Unit Price)" 
                                                        class="p-2 text-xs border rounded-lg transition-colors cursor-pointer {{ (empty($item['is_custom_total']) && empty($item['is_rounded'])) ? 'bg-blue-600 text-white border-blue-600 shadow-2xs' : 'bg-white text-gray-500 hover:bg-gray-100 border-gray-300' }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                </button>
                                            </div>
                                            <div class="mt-1 text-right">
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
                                        <td class="py-3 text-center align-middle">
                                            <button type="button" 
                                                    wire:click="removeItem({{ $index }})" 
                                                    class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                                                    title="Hapus baris item">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Card 3 & 4: Catatan & Rincian Pembayaran -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column: Notes & Information -->
                <div class="lg:col-span-5 space-y-4">
                    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">3</span>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Catatan & Syarat Ketentuan</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                                    Catatan Faktur (Notes / Terms)
                                </label>
                                <textarea wire:model="notes" 
                                          rows="5" 
                                          class="block w-full border border-gray-300 rounded-lg p-3 text-sm bg-white text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" 
                                          placeholder="Contoh: Pembayaran tempo 14 hari, transfer bank ke rekening CV Radi Amartha Mahardika..."></textarea>
                            </div>

                            <div class="p-4 bg-blue-50/70 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-1.5">
                                <div class="font-bold flex items-center gap-1.5 text-blue-900">
                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                    Kalkulasi Otomatis Transaksi
                                </div>
                                <p class="text-[12px] leading-relaxed text-blue-800">
                                    <strong>Grand Total</strong> dihitung otomatis dari akumulasi subtotal produk di atas (dikurangi Diskon, ditambah Pajak & Biaya Pengiriman). Nilai <strong>Subtotal (DPP)</strong> dapat disesuaikan untuk keperluan dasar perpajakan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Rincian Pembayaran Card -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">4</span>
                                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Rincian Pembayaran</h3>
                            </div>
                            <span class="text-xs font-normal text-gray-500">Kalkulasi Otomatis / Manual</span>
                        </div>

                        <div class="p-6 space-y-4">
                            <!-- Subtotal Produk -->
                            <div class="bg-blue-50/60 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Subtotal Produk</span>
                                        <span class="text-xs text-gray-500 font-medium">({{ count($items) }} item)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="button" 
                                                wire:click="roundAllSubtotals" 
                                                class="px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-white hover:bg-emerald-50 border border-emerald-300 rounded-lg shadow-2xs transition-colors cursor-pointer"
                                                title="Bulatkan semua subtotal produk">
                                            Bulatkan Semua
                                        </button>
                                        <button type="button" 
                                                wire:click="autoAllSubtotals" 
                                                class="px-2.5 py-1 text-xs font-semibold text-blue-700 bg-white hover:bg-blue-50 border border-blue-300 rounded-lg shadow-2xs transition-colors cursor-pointer"
                                                title="Reset semua ke perhitungan Qty × Harga">
                                            Auto Semua
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-1">
                                    <span class="text-xs text-blue-800">Total nilai akumulasi produk</span>
                                    <span class="text-lg font-bold font-mono text-blue-950">
                                        Rp {{ number_format($this->getItemsSubtotal(), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Subtotal (DPP) -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Subtotal (DPP)</label>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" 
                                                wire:click="roundDppSubtotal" 
                                                class="text-xs font-medium text-emerald-700 hover:text-emerald-800 underline cursor-pointer"
                                                title="Bulatkan nominal DPP ke ribuan">
                                            Bulatkan
                                        </button>
                                        <span class="text-xs text-gray-400">|</span>
                                        <span class="text-xs text-gray-500">Dasar Pengenaan Pajak</span>
                                    </div>
                                </div>
                                <div class="relative rounded-lg shadow-2xs">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">Rp</span>
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           wire:model.live.debounce.150ms="total_amount" 
                                           placeholder="0" 
                                           class="w-full border border-gray-300 rounded-lg py-2.5 pl-9 pr-3 text-sm text-right font-mono font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>

                            <!-- Diskon -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Diskon / Potongan</label>
                                    <span class="text-xs text-gray-500">Pengurang nilai total invoice</span>
                                </div>
                                <div class="relative rounded-lg shadow-2xs">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-red-500">- Rp</span>
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           wire:model.live.debounce.150ms="discount_amount" 
                                           placeholder="0" 
                                           class="w-full border border-gray-300 rounded-lg py-2.5 pl-12 pr-3 text-sm text-right font-mono font-semibold text-red-600 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>

                            <!-- Pajak (Pilihan Tarif & Input Nominal) -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Pajak Pertambahan Nilai / PPh</label>
                                    <span class="text-xs text-gray-500">Pilih tarif & sesuaikan nominal</span>
                                </div>

                                <!-- Opsi Pilihan Tarif Pajak -->
                                <div class="grid grid-cols-3 gap-2 mb-2">
                                    <button type="button" 
                                            wire:click="setTaxType('pph_05')" 
                                            class="py-2 px-3 text-xs font-semibold rounded-lg border transition-all text-center cursor-pointer {{ $tax_type === 'pph_05' ? 'bg-blue-600 border-blue-600 text-white shadow-2xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                                        PPh (0,5%)
                                    </button>
                                    <button type="button" 
                                            wire:click="setTaxType('ppn_12')" 
                                            class="py-2 px-3 text-xs font-semibold rounded-lg border transition-all text-center cursor-pointer {{ ($tax_type === 'ppn_12' || $tax_type === 'ppn_11') ? 'bg-blue-600 border-blue-600 text-white shadow-2xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                                        PPN (12%)
                                    </button>
                                    <button type="button" 
                                            wire:click="setTaxType('none')" 
                                            class="py-2 px-3 text-xs font-semibold rounded-lg border transition-all text-center cursor-pointer {{ $tax_type === 'none' ? 'bg-blue-600 border-blue-600 text-white shadow-2xs' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-100' }}">
                                        Bebas Pajak (0%)
                                    </button>
                                </div>

                                <!-- Input Nominal Pajak -->
                                <div class="relative rounded-lg shadow-2xs">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">Rp</span>
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           wire:model.live.debounce.150ms="tax_amount" 
                                           placeholder="0" 
                                           class="w-full border border-gray-300 rounded-lg py-2.5 pl-9 pr-3 text-sm text-right font-mono font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>

                            <!-- Shipping Price -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Ongkos Kirim (Shipping Fee)</label>
                                    <span class="text-xs text-gray-500">Biaya pengiriman armada</span>
                                </div>
                                <div class="relative rounded-lg shadow-2xs">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">+ Rp</span>
                                    <input type="number" 
                                           step="any" 
                                           min="0" 
                                           wire:model.live.debounce.150ms="shipping_price" 
                                           placeholder="0" 
                                           class="w-full border border-gray-300 rounded-lg py-2.5 pl-12 pr-3 text-sm text-right font-mono font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                </div>
                            </div>

                            <!-- Grand Total Section -->
                            <div class="pt-3">
                                <div class="bg-gradient-to-r from-blue-700 via-blue-800 to-indigo-900 rounded-xl p-5 text-white shadow-md space-y-4">
                                    <!-- Header & Action Buttons -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-blue-500/40">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs uppercase tracking-wider text-blue-200 font-bold">Grand Total Faktur</span>
                                            @if($is_rounded_grand_total)
                                                <span class="inline-flex items-center gap-1 text-[11px] bg-emerald-500/25 text-emerald-200 font-semibold px-2.5 py-0.5 rounded-full border border-emerald-400/40">
                                                    <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                    Dibulatkan
                                                </span>
                                            @elseif($is_custom_grand_total)
                                                <span class="inline-flex items-center gap-1 text-[11px] bg-amber-500/25 text-amber-200 font-semibold px-2.5 py-0.5 rounded-full border border-amber-400/40">
                                                    <svg class="w-3 h-3 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    Diedit Manual
                                                </span>
                                            @else
                                                <span class="text-[11px] bg-blue-600/50 text-blue-200 font-medium px-2.5 py-0.5 rounded-full border border-blue-400/30">
                                                    Kalkulasi Otomatis
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Quick Rounding & Reset Buttons -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button type="button" 
                                                    wire:click="roundUpGrandTotal" 
                                                    class="px-2.5 py-1 text-xs font-semibold text-emerald-900 bg-emerald-300 hover:bg-emerald-200 rounded-lg shadow-sm transition-all cursor-pointer inline-flex items-center gap-1"
                                                    title="Bulatkan Grand Total ke atas ke ribuan terdekat (misal: 123.450 -> 124.000)">
                                                <svg class="w-3.5 h-3.5 text-emerald-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                                Bulatkan ke Atas
                                            </button>
                                            <button type="button" 
                                                    wire:click="roundGrandTotal" 
                                                    class="px-2.5 py-1 text-xs font-semibold text-blue-950 bg-blue-200 hover:bg-blue-100 rounded-lg shadow-sm transition-all cursor-pointer inline-flex items-center gap-1"
                                                    title="Bulatkan Grand Total ke ribuan terdekat">
                                                .000
                                            </button>
                                            <button type="button" 
                                                    wire:click="autoGrandTotal" 
                                                    class="px-2.5 py-1 text-xs font-semibold text-white bg-white/20 hover:bg-white/30 rounded-lg transition-all cursor-pointer inline-flex items-center gap-1"
                                                    title="Kembalikan ke kalkulasi rumus otomatis">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                Auto
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Editable Grand Total Input & Formatted Display -->
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                        <div class="sm:col-span-6">
                                            <label class="block text-[11px] text-blue-200 font-medium mb-1">
                                                Koreksi Nominal (Bisa diedit manual)
                                            </label>
                                            <div class="relative rounded-lg shadow-sm">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-sm font-bold text-gray-500">Rp</span>
                                                <input type="number" 
                                                       step="any" 
                                                       min="0" 
                                                       wire:model.live.debounce.250ms="grand_total" 
                                                       placeholder="0" 
                                                       class="w-full bg-white text-gray-900 border border-blue-300 rounded-lg py-2 pl-10 pr-3 text-right font-mono font-bold text-base focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all shadow-inner">
                                            </div>
                                        </div>

                                        <div class="sm:col-span-6 flex flex-col items-start sm:items-end justify-center">
                                            <span class="text-[11px] text-blue-200 uppercase tracking-wider font-medium">Total Akhir Terformat</span>
                                            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-white tracking-tight">
                                                Rp {{ number_format(floatval($grand_total ?: 0), 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Terbilang Banner -->
                                    @if(floatval($grand_total ?: 0) > 0)
                                        <div class="pt-2 border-t border-blue-500/30 text-[11px] italic text-blue-100 flex items-center gap-1.5 flex-wrap">
                                            <span class="font-semibold not-italic text-blue-200">Terbilang:</span>
                                            <span># {{ \App\Helpers\Terbilang::rupiah(floatval($grand_total)) }} #</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Bottom Actions Bar -->
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    @if($isEditMode && $sale_id)
                        <a href="{{ route('sales.pdf.stream', $sale_id) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-purple-300 shadow-2xs text-sm font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 focus:outline-none transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak PDF
                        </a>
                    @endif
                    <button type="button" wire:click="openPreview" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-2xs text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none cursor-pointer transition-colors">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Preview Invoice
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" wire:click="cancel" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="button" wire:click="save" class="px-6 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all cursor-pointer">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Invoice' }}
                    </button>
                </div>
            </div>
        </div>

    @else
        <!-- ================= LIVE INVOICE PREVIEW VIEW ================= -->
        <div class="space-y-6">
            <!-- Info Notice Banner -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs text-blue-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span>Live Preview ini menampilkan format proforma invoice secara akurat sesuai data yang Anda masukkan sebelum disimpan.</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="printInvoicePreview()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium inline-flex items-center gap-1.5 shadow-2xs transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak Preview
                    </button>
                    <button type="button" wire:click="closePreview" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg font-medium inline-flex items-center gap-1 shadow-2xs transition-colors cursor-pointer">
                        Kembali ke Form
                    </button>
                </div>
            </div>

            <!-- Paper Sheet Box -->
            <div class="bg-gray-100 p-4 sm:p-8 rounded-2xl overflow-x-auto border border-gray-200">
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
            <div class="bg-white rounded-xl shadow-xs border border-gray-200 p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <button type="button" wire:click="closePreview" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-2xs text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none cursor-pointer transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Form
                </button>

                <div class="flex items-center space-x-3">
                    <button type="button" onclick="printInvoicePreview()" class="inline-flex items-center px-4 py-2 border border-blue-600 shadow-2xs text-sm font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none cursor-pointer transition-colors">
                        <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Cetak / Print
                    </button>
                    <button type="button" wire:click="save" class="inline-flex justify-center px-6 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-xs focus:outline-none transition-all cursor-pointer">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Invoice' }}
                    </button>
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
