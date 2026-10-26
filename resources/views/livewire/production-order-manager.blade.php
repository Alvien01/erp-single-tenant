<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Production Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor manufacturing processes, track raw material consumption, and record finished goods.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer shadow-xs">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Production Order
        </button>
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between shadow-xs" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span><strong class="font-semibold">Sukses!</strong> {{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 font-bold">&times;</button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between shadow-xs" role="alert">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span><strong class="font-semibold">Perhatian!</strong> {{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-700 font-bold">&times;</button>
        </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by order number or product name...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Order Number</th>
                        <th class="py-3.5 px-6">Product To Produce</th>
                        <th class="py-3.5 px-6 text-right">Target Qty</th>
                        <th class="py-3.5 px-6 text-right">Est. Production Cost</th>
                        <th class="py-3.5 px-6">Timeline</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-semibold text-blue-600 whitespace-nowrap">{{ $order->order_number }}</td>
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $order->product->name ?? '-' }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900 font-medium">{{ number_format($order->quantity, 2) }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($order->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-gray-600 text-xs whitespace-nowrap">
                                {{ $order->start_date }} to {{ $order->end_date }}
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $order->status === 'planned' ? 'bg-gray-100 text-gray-800 border border-gray-300' : '' }}
                                    {{ $order->status === 'in_progress' ? 'bg-blue-100 text-blue-800 border border-blue-300' : '' }}
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800 border border-red-300' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $order->id }})" class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded px-2 py-1 cursor-pointer transition-colors">
                                        Edit / Status
                                    </button>
                                    @if($order->status !== 'completed')
                                        <button onclick="confirm('Hapus order produksi ini?') || event.stopImmediatePropagation()" wire:click="delete({{ $order->id }})" class="text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 border border-red-200 rounded px-2 py-1 cursor-pointer transition-colors">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                No production orders registered.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none text-xl font-bold cursor-pointer">
                            &times;
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Update Production Order' : 'Add New Production Order' }}
                        </h3>

                        <!-- In-Modal Alert Message -->
                        @if (session()->has('error'))
                            <div class="mt-3 p-3.5 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between shadow-xs">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                    <span><strong class="font-semibold">Gagal menyimpan:</strong> {{ session('error') }}</span>
                                </div>
                            </div>
                        @endif
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 font-sans">
                            <!-- Product Selection -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Produk yang Diproduksi <span class="text-red-500">*</span></label>
                                <select wire:model.live="product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500" {{ $isEditMode ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Kode: {{ $p->code ?? '-' }})</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Target Qty -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Target Qty <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0.01" wire:model.live.debounce.250ms="quantity" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('quantity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Warehouse Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Gudang / Warehouse</label>
                                <select wire:model="warehouse_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}">{{ $wh->warehouse_code }} - {{ $wh->warehouse_name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Start Date -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- End Date -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Target Tanggal Selesai <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div class="lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Status Produksi</label>
                                <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="planned">Planned (Rencana Produksi Baru)</option>
                                    <option value="in_progress">In Progress (Sedang Dikerjakan)</option>
                                    <option value="completed">Completed (Selesai: Potong stok bahan baku & Tambahkan stok barang jadi)</option>
                                    <option value="cancelled">Cancelled (Dibatalkan)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Required Materials List -->
                        @if(count($materials) > 0)
                            <div class="mt-6 border-t border-gray-200 pt-5">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-bold text-gray-900 font-display">Daftar Bahan Baku / Komponen (BOM Reference)</h4>
                                    <span class="text-xs text-gray-500">{{ count($materials) }} komponen</span>
                                </div>
                                <div class="overflow-hidden border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 text-left font-semibold text-gray-600 text-xs uppercase tracking-wider">
                                                <th class="py-2.5 px-4">Komponen / Material</th>
                                                <th class="py-2.5 px-4 text-right">Qty Dibutuhkan</th>
                                                <th class="py-2.5 px-4 text-right">Estimasi Biaya Satuan</th>
                                                <th class="py-2.5 px-4 text-right">Total Estimasi Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($materials as $mat)
                                                <tr>
                                                    <td class="py-2.5 px-4 text-gray-900 font-medium">{{ $mat['name'] ?? 'Bahan' }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-gray-800">{{ number_format($mat['qty_required'], 2) }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-gray-800">Rp {{ number_format($mat['unit_cost'], 0, ',', '.') }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-gray-900 font-semibold">Rp {{ number_format($mat['qty_required'] * $mat['unit_cost'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="mt-4 p-3 bg-amber-50/70 border border-amber-200 rounded-lg text-xs text-amber-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                <span>Tidak ada resep BOM untuk produk yang dipilih. Order produksi tetap dapat disimpan tanpa rincian konsumsi bahan baku.</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display cursor-pointer">
                            Batal
                        </button>
                        <button type="button" wire:click="store" wire:loading.attr="disabled" class="inline-flex items-center justify-center px-5 py-2 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display disabled:opacity-50 cursor-pointer shadow-xs transition-colors">
                            <span wire:loading.remove wire:target="store">{{ $isEditMode ? 'Update Order' : 'Simpan Order' }}</span>
                            <span wire:loading wire:target="store" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
