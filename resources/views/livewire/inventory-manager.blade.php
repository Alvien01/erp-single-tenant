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
            <button wire:click="$set('activeTab', 'dashboard')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'dashboard' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Dashboard
            </button>
            <button wire:click="$set('activeTab', 'stock')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'stock' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Stock Overview
            </button>
            <button wire:click="$set('activeTab', 'adjustments')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'adjustments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Adjustment Logs
            </button>
        </nav>
    </div>

    @if($activeTab === 'dashboard')
        <!-- Dashboard Analitik Warehouse & Stock -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Total Stok Fisik</p>
                        <p class="mt-2 text-3xl font-extrabold text-gray-900 font-mono">{{ number_format($stats['totalStockQty']) }}</p>
                        <span class="text-xs text-gray-400">Total unit on-hand</span>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Nilai Aset Persediaan</p>
                        <p class="mt-2 text-2xl font-extrabold text-emerald-650 font-mono">Rp {{ number_format($stats['totalValuation'], 0, ',', '.') }}</p>
                        <span class="text-xs text-gray-400">Valuasi harga modal</span>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-550">Receipts Pending</p>
                        <p class="mt-2 text-3xl font-extrabold text-amber-600 font-mono">{{ $stats['pendingReceipts'] }}</p>
                        <span class="text-xs text-gray-400">Penerimaan barang pending</span>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-lg">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500">Transfer Aktif</p>
                        <p class="mt-2 text-3xl font-extrabold text-blue-600 font-mono">{{ $stats['pendingTransfers'] }}</p>
                        <span class="text-xs text-gray-400">Transfer antargudang jalan</span>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Low stock warning --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 font-display mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Peringatan Stok Menipis (< 15 pcs)
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="text-left font-semibold text-gray-500">
                                <th class="py-3 px-4">Nama Produk</th>
                                <th class="py-3 px-4">Gudang</th>
                                <th class="py-3 px-4 text-center">Stok Sisa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($stats['lowStock'] as $item)
                                <tr>
                                    <td class="py-3 px-4 font-semibold text-gray-900">{{ $item->product->name ?? 'Produk Tidak Dikenal' }}</td>
                                    <td class="py-3 px-4 text-gray-550">{{ $item->warehouse->warehouse_name ?? 'Gudang Utama' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 font-mono">
                                            {{ $item->qty_on_hand }} pcs
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-400">Seluruh stok produk berada dalam batas aman (> 15 pcs).</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Warehouse capacity/quantities --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 font-display mb-4">Kapasitas Kuantitas per Lokasi Gudang</h3>
                <div class="space-y-4 font-sans text-sm">
                    @php
                        $maxWH = $stats['valuationByWarehouse']->max('total_qty') ?: 1;
                    @endphp
                    @forelse($stats['valuationByWarehouse'] as $wh)
                        @php
                            $percentage = ($wh->total_qty / $maxWH) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-semibold text-gray-650 mb-1">
                                <span>{{ $wh->warehouse->warehouse_name ?? 'Gudang Default' }}</span>
                                <span class="font-mono text-gray-900 font-bold">{{ number_format($wh->total_qty) }} pcs</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-450 text-center py-6">Stok belum dicatat di lokasi manapun.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent adjustments logs --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 font-sans">
            <h3 class="text-lg font-bold text-gray-900 font-display mb-4">Log Penyesuaian Stok (Adjustments) Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left font-semibold text-gray-500">
                            <th class="py-3 px-4">Nomor Penyesuaian</th>
                            <th class="py-3 px-4">Gudang</th>
                            <th class="py-3 px-4">Tipe Penyesuaian</th>
                            <th class="py-3 px-4">Disesuaikan Oleh</th>
                            <th class="py-3 px-4">Tanggal Input</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stats['recentAdjustments'] as $adj)
                            <tr>
                                <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $adj->adj_number }}</td>
                                <td class="py-3 px-4 text-gray-800">{{ $adj->warehouse->warehouse_name }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase
                                        {{ $adj->type === 'opname' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $adj->type }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">{{ $adj->adjustedBy->name ?? 'System' }}</td>
                                <td class="py-3 px-4 text-gray-500 font-mono">{{ $adj->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-400">Belum ada penyesuaian stok dicatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'stock')
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
