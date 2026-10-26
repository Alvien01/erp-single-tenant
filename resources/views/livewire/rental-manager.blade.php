<div>
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Rental Manager</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola penyewaan barang, jadwal rental, deposit, dan status serah terima barang.</p>
        </div>
        <a href="{{ route('rentals.create') }}" 
           wire:navigate
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Order Rental Baru
        </a>
    </div>

    <!-- Success Message Alert -->
    @if(session()->has('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-sm shadow-xs animate-fadeIn">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Rental</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalRentals }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Semua transaksi sewa</p>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sedang Disewa</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $ongoingCount }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Barang berada di pelanggan</p>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sudah Kembali</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $returnedCount }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Selesai / dikembalikan</p>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Nilai Sewa</p>
                <p class="text-xl font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($totalRentalValue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Deposit aktif: Rp {{ number_format($activeDeposits, 0, ',', '.') }}</p>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
        <!-- Filters Bar -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" 
                       wire:model.live.debounce.250ms="search" 
                       placeholder="Cari no order, barang, atau customer..." 
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <select wire:model.live="statusFilter" class="border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status Sewa</option>
                    <option value="draft">Draft</option>
                    <option value="confirmed">Terkonfirmasi</option>
                    <option value="ongoing">Sedang Disewa</option>
                    <option value="returned">Dikembalikan</option>
                    <option value="completed">Selesai</option>
                    <option value="cancelled">Dibatalkan</option>
                </select>

                <select wire:model.live="paymentStatusFilter" class="border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Pembayaran</option>
                    <option value="unpaid">Belum Lunas</option>
                    <option value="partially_paid">Sebagian</option>
                    <option value="paid">Lunas</option>
                </select>
            </div>
        </div>

        <!-- Rentals Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-50/80 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">No. Rental</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Barang Sewa</th>
                        <th class="py-3.5 px-4">Periode & Durasi</th>
                        <th class="py-3.5 px-4 text-right">Total Sewa</th>
                        <th class="py-3.5 px-4 text-right">Deposit</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-center">Bayar</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($rentals as $rental)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono font-semibold text-blue-700 block">
                                    {{ $rental->rental_number ?: 'RNT-' . str_pad($rental->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $rental->created_at ? $rental->created_at->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-900">{{ $rental->customer->name ?? '-' }}</div>
                                @if(!empty($rental->customer->phone))
                                    <div class="text-xs text-gray-500">{{ $rental->customer->phone }}</div>
                                @elseif(!empty($rental->customer->company_name))
                                    <div class="text-xs text-gray-500">{{ $rental->customer->company_name }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-800">{{ $rental->item_name ?: ($rental->product->name ?? 'Barang Sewa') }}</div>
                                <div class="text-xs text-gray-500">
                                    Jumlah: <span class="font-semibold text-gray-700">{{ floatval($rental->quantity) }} unit</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-xs whitespace-nowrap">
                                <div class="text-gray-900 font-medium">
                                    {{ $rental->start_date ? $rental->start_date->format('d/m/Y') : '-' }} &rarr; 
                                    {{ $rental->end_date ? $rental->end_date->format('d/m/Y') : '-' }}
                                </div>
                                <div class="text-gray-500">
                                    {{ $rental->duration }} 
                                    {{ match($rental->rate_unit) { 'hourly' => 'Jam', 'weekly' => 'Minggu', 'monthly' => 'Bulan', default => 'Hari' } }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($rental->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono text-xs text-gray-600 whitespace-nowrap">
                                Rp {{ number_format($rental->deposit_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php $badge = $rental->status_badge; @endphp
                                <span class="px-2.5 py-1 text-xs font-medium rounded-full border {{ $badge['class'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php $payBadge = $rental->payment_status_badge; @endphp
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full border {{ $payBadge['class'] }}">
                                    {{ $payBadge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- View Detail Modal -->
                                    <button type="button" 
                                            wire:click="viewDetail({{ $rental->id }})" 
                                            class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors cursor-pointer" 
                                            title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </button>

                                    <!-- Quick Return (if ongoing) -->
                                    @if($rental->status === 'ongoing')
                                        <button type="button" 
                                                wire:click="openQuickReturn({{ $rental->id }})" 
                                                class="p-1.5 text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-md transition-colors cursor-pointer" 
                                                title="Proses Pengembalian">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                        </button>
                                    @elseif($rental->status === 'draft' || $rental->status === 'confirmed')
                                        <button type="button" 
                                                wire:click="quickStatusUpdate({{ $rental->id }}, 'ongoing')" 
                                                class="p-1.5 text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-md transition-colors cursor-pointer" 
                                                title="Mulai Sewa / Serahkan Barang">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </button>
                                    @endif

                                    <!-- Edit Link to Redirect View -->
                                    <a href="{{ route('rentals.edit', $rental->id) }}" 
                                       wire:navigate
                                       class="p-1.5 text-gray-500 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors inline-block" 
                                       title="Edit Order (Redirect View)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>

                                    <!-- Delete -->
                                    <button type="button" 
                                            wire:click="confirmDelete({{ $rental->id }})" 
                                            class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors cursor-pointer" 
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="text-base font-medium text-gray-900">Belum ada data rental</h3>
                                <p class="text-sm text-gray-500 mt-1">Mulai catat transaksi penyewaan barang atau alat baru.</p>
                                <div class="mt-4">
                                    <a href="{{ route('rentals.create') }}" 
                                       wire:navigate
                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                                        + Buat Order Rental
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($rentals->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL 1: Detail Rental Modal -->
    @if($isDetailOpen && $selectedRental)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-2xl overflow-hidden transform transition-all my-8 animate-scaleIn">
                <!-- Header -->
                <div class="px-6 py-4 bg-gray-50/90 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-900 font-mono">{{ $selectedRental->rental_number }}</h3>
                            @php $b = $selectedRental->status_badge; @endphp
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border {{ $b['class'] }}">{{ $b['label'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Dibuat pada {{ $selectedRental->created_at ? $selectedRental->created_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                    <button type="button" wire:click="closeDetail" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 space-y-5 text-sm">
                    <!-- Customer Information Section -->
                    <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Informasi Penyewa</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-xs text-gray-400 block">Nama Customer</span>
                                <span class="font-semibold text-gray-900">{{ $selectedRental->customer->name ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block">Perusahaan</span>
                                <span class="text-gray-800">{{ $selectedRental->customer->company_name ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block">Telepon / HP</span>
                                <span class="text-gray-800">{{ $selectedRental->customer->phone ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-400 block">Email</span>
                                <span class="text-gray-800">{{ $selectedRental->customer->email ?: '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Rental Item & Period -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="border border-gray-200 p-4 rounded-xl">
                            <span class="text-xs text-gray-400 block uppercase font-semibold">Barang / Alat Sewa</span>
                            <div class="font-bold text-gray-900 text-base mt-1">{{ $selectedRental->item_name }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Jumlah: <span class="font-semibold text-gray-800">{{ floatval($selectedRental->quantity) }} unit</span>
                            </div>
                            @if($selectedRental->product)
                                <div class="text-xs text-blue-600 mt-0.5">Produk Master: {{ $selectedRental->product->code }} - {{ $selectedRental->product->name }}</div>
                            @endif
                        </div>

                        <div class="border border-gray-200 p-4 rounded-xl">
                            <span class="text-xs text-gray-400 block uppercase font-semibold">Periode Rental</span>
                            <div class="font-medium text-gray-900 mt-1">
                                {{ $selectedRental->start_date ? $selectedRental->start_date->format('d M Y') : '-' }} s/d {{ $selectedRental->end_date ? $selectedRental->end_date->format('d M Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-600 mt-1">
                                Durasi: <span class="font-semibold text-gray-800">{{ $selectedRental->duration }} {{ match($selectedRental->rate_unit) { 'hourly' => 'Jam', 'weekly' => 'Minggu', 'monthly' => 'Bulan', default => 'Hari' } }}</span>
                            </div>
                            @if($selectedRental->return_date)
                                <div class="text-xs text-emerald-700 mt-0.5 font-medium">
                                    Aktual Kembali: {{ $selectedRental->return_date->format('d M Y') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-600 uppercase">
                            Rincian Biaya & Pembayaran
                        </div>
                        <div class="p-4 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Tarif Sewa (x {{ floatval($selectedRental->quantity) }} unit x {{ $selectedRental->duration }} {{ $selectedRental->rate_unit }})</span>
                                <span class="font-mono text-gray-900 font-medium">Rp {{ number_format(floatval($selectedRental->quantity) * floatval($selectedRental->rental_rate) * intval($selectedRental->duration), 0, ',', '.') }}</span>
                            </div>
                            @if($selectedRental->delivery_fee > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Biaya Kirim / Pick-up</span>
                                    <span class="font-mono text-gray-900 font-medium">Rp {{ number_format($selectedRental->delivery_fee, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between pt-2 border-t border-gray-100 text-sm font-bold">
                                <span class="text-gray-900">Total Biaya Sewa</span>
                                <span class="font-mono text-blue-700">Rp {{ number_format($selectedRental->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-xs pt-1 border-t border-dashed border-gray-200">
                                <span class="text-gray-500">Uang Jaminan / Deposit</span>
                                <span class="font-mono font-medium text-amber-700">Rp {{ number_format($selectedRental->deposit_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <span class="text-gray-500">Status Pembayaran</span>
                                @php $pb = $selectedRental->payment_status_badge; @endphp
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $pb['class'] }}">{{ $pb['label'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Conditions & Notes -->
                    @if($selectedRental->condition_out || $selectedRental->condition_in || $selectedRental->notes)
                        <div class="space-y-3 pt-1">
                            @if($selectedRental->condition_out)
                                <div class="p-3 bg-gray-50 rounded-lg text-xs">
                                    <span class="font-semibold text-gray-700 block mb-0.5">Kondisi Fisik Saat Serah Terima (Keluar):</span>
                                    <p class="text-gray-600">{{ $selectedRental->condition_out }}</p>
                                </div>
                            @endif
                            @if($selectedRental->condition_in)
                                <div class="p-3 bg-emerald-50 rounded-lg text-xs text-emerald-900">
                                    <span class="font-semibold block mb-0.5">Kondisi Fisik Saat Pengembalian (Masuk):</span>
                                    <p>{{ $selectedRental->condition_in }}</p>
                                </div>
                            @endif
                            @if($selectedRental->notes)
                                <div class="p-3 bg-blue-50/50 rounded-lg text-xs text-gray-700">
                                    <span class="font-semibold block mb-0.5">Catatan Tambahan:</span>
                                    <p>{{ $selectedRental->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        @if($selectedRental->status === 'ongoing')
                            <button type="button" 
                                    wire:click="openQuickReturn({{ $selectedRental->id }})" 
                                    class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-medium rounded-lg cursor-pointer">
                                Proses Pengembalian
                            </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('rentals.edit', $selectedRental->id) }}" 
                           wire:navigate
                           class="px-3 py-1.5 border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-medium rounded-lg inline-block">
                            Edit Order
                        </a>
                        <button type="button" 
                                wire:click="closeDetail" 
                                class="px-4 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-medium rounded-lg cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 2: Quick Return Modal -->
    @if($confirmingReturn)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden transform transition-all animate-scaleIn">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Konfirmasi Pengembalian Barang</h3>
                            <p class="text-xs text-gray-500">Tandai barang telah kembali dan diperiksa.</p>
                        </div>
                    </div>

                    <div class="space-y-4 text-sm">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Pengembalian</label>
                            <input type="date" 
                                   wire:model="quickReturnDate" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-amber-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Kondisi Barang Masuk</label>
                            <textarea wire:model="quickReturnCondition" 
                                      rows="3" 
                                      placeholder="Catat kondisi fisik dan kelengkapan unit saat dikembalikan..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-amber-500"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" 
                                wire:click="$set('confirmingReturn', false)" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="button" 
                                wire:click="processQuickReturn" 
                                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shadow-sm cursor-pointer">
                            Konfirmasi Pengembalian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL 3: Delete Confirmation Modal -->
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 w-full max-w-md overflow-hidden transform transition-all animate-scaleIn">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Hapus Order Rental</h3>
                            <p class="text-xs text-gray-500">Apakah Anda yakin ingin menghapus data sewa ini? Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" 
                                wire:click="$set('confirmingDeletion', false)" 
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="button" 
                                wire:click="delete" 
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-lg shadow-sm cursor-pointer">
                            Ya, Hapus Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
