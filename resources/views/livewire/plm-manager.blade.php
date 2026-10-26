<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Product Lifecycle Management (PLM)</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola perubahan teknik produk (Engineering Change Orders), revisi versi BOM, dan stasiun kerja produksi.</p>
        </div>
        <button type="button" 
                wire:click="create" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ $activeTab === 'eco' ? '+ Create New ECO' : '+ Create New Work Center' }}
        </button>
    </div>

    <!-- Success & Error Alerts -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-sm shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl flex items-center justify-between text-red-800 text-sm shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Metric Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total ECO Requests</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalEco }}</p>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending Review</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingEco }}</p>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Applied (Live BOM)</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $appliedEco }}</p>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Work Centers</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalWc }}</p>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-6">
            <button type="button" 
                    wire:click="setTab('eco')" 
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'eco' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Engineering Change Orders (ECO)
            </button>
            <button type="button" 
                    wire:click="setTab('work_centers')" 
                    class="py-3 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'work_centers' ? 'border-blue-600 text-blue-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Stasiun Kerja (Work Centers)
            </button>
        </nav>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden font-sans">
        <!-- Search and Filters Bar -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" 
                       wire:model.live.debounce.250ms="search" 
                       placeholder="{{ $activeTab === 'eco' ? 'Cari nomor ECO, produk, judul...' : 'Cari kode atau nama stasiun kerja...' }}" 
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>

            @if($activeTab === 'eco')
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select wire:model.live="statusFilter" class="w-full sm:w-auto border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="draft">Draft</option>
                        <option value="pending_review">Pending Review</option>
                        <option value="approved">Approved</option>
                        <option value="applied">Applied (Live BOM)</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            @endif
        </div>

        @if($activeTab === 'eco')
            <!-- ECO Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50/80 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">No. ECO</th>
                            <th class="py-3 px-4">Produk & Judul Perubahan</th>
                            <th class="py-3 px-4">Alasan</th>
                            <th class="py-3 px-4 text-center">Versi BOM</th>
                            <th class="py-3 px-4">Pemohon</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($ecos as $eco)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-semibold text-blue-700 whitespace-nowrap">
                                    {{ $eco->eco_number }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-gray-900">{{ $eco->title }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        Produk: <span class="font-medium text-gray-700">{{ $eco->product->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-gray-700 text-xs">
                                    <span class="inline-block px-2 py-0.5 rounded bg-gray-100 font-medium text-gray-800">
                                        {{ $eco->reason ?: 'General Revision' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 text-xs font-mono font-semibold">
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200">v{{ $eco->old_bom_version ?: '1.0' }}</span>
                                        <span class="text-blue-500">→</span>
                                        <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">v{{ $eco->new_bom_version }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-gray-600 whitespace-nowrap">
                                    <div class="font-medium text-gray-800">{{ $eco->requester->name ?? 'User' }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $eco->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @php
                                        $badge = match($eco->status) {
                                            'draft' => 'bg-gray-100 text-gray-700 border-gray-200',
                                            'pending_review' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'approved' => 'bg-blue-50 text-blue-700 border-blue-200',
                                            'applied' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                            default => 'bg-gray-50 text-gray-600 border-gray-200'
                                        };
                                        $label = match($eco->status) {
                                            'draft' => 'Draft',
                                            'pending_review' => 'Pending Review',
                                            'approved' => 'Approved',
                                            'applied' => 'Applied (Live)',
                                            'rejected' => 'Rejected',
                                            default => ucfirst($eco->status)
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badge }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if($eco->status === 'draft')
                                            <button type="button" 
                                                    wire:click="changeEcoStatus({{ $eco->id }}, 'pending_review')" 
                                                    class="px-2 py-1 text-xs font-semibold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded cursor-pointer transition-colors"
                                                    title="Ajukan untuk review">
                                                Ajukan
                                            </button>
                                        @elseif($eco->status === 'pending_review')
                                            <button type="button" 
                                                    wire:click="changeEcoStatus({{ $eco->id }}, 'approved')" 
                                                    class="px-2 py-1 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded cursor-pointer transition-colors"
                                                    title="Setujui perubahan">
                                                Approve
                                            </button>
                                        @elseif($eco->status === 'approved')
                                            <button type="button" 
                                                    wire:click="changeEcoStatus({{ $eco->id }}, 'applied')" 
                                                    class="px-2 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded cursor-pointer transition-colors"
                                                    title="Terapkan ke Resep BOM Aktif">
                                                Apply
                                            </button>
                                        @endif

                                        <button type="button" 
                                                wire:click="editEco({{ $eco->id }})" 
                                                class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                                                title="Edit ECO">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <button type="button" 
                                                wire:click="confirmDelete('eco', {{ $eco->id }})" 
                                                class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                                                title="Hapus ECO">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-gray-500">
                                    <div class="max-w-xs mx-auto">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada Engineering Change Order (ECO)</h3>
                                        <p class="mt-1 text-xs text-gray-500">Mulai buat pengajuan revisi spesifikasi produk atau BOM.</p>
                                        <button type="button" wire:click="create" class="mt-3 inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700 cursor-pointer">
                                            + Tambah ECO Baru
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($ecos->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $ecos->links() }}
                </div>
            @endif

        @else
            <!-- Work Centers Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50/80 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-4">Kode Stasiun</th>
                            <th class="py-3 px-4">Nama Stasiun Kerja (Work Center)</th>
                            <th class="py-3 px-4 text-right">Biaya per Jam</th>
                            <th class="py-3 px-4 text-right">Kapasitas (Unit/Jam)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($workCenters as $wc)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-semibold text-blue-700 whitespace-nowrap">
                                    {{ $wc->code ?: 'WC-'.str_pad($wc->id, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $wc->name }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-semibold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($wc->cost_per_hour, 0, ',', '.') }} / jam
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-gray-800 whitespace-nowrap">
                                    {{ number_format($wc->capacity, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ ($wc->status ?? 'active') === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-600 border-gray-200' }}">
                                        {{ ucfirst($wc->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button type="button" 
                                                wire:click="editWorkCenter({{ $wc->id }})" 
                                                class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                                                title="Edit Work Center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button type="button" 
                                                wire:click="confirmDelete('wc', {{ $wc->id }})" 
                                                class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                                                title="Hapus Work Center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-gray-500">
                                    <div class="max-w-xs mx-auto">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada Stasiun Kerja</h3>
                                        <p class="mt-1 text-xs text-gray-500">Daftarkan mesin dan stasiun kerja produksi di sini.</p>
                                        <button type="button" wire:click="create" class="mt-3 inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-semibold hover:bg-blue-700 cursor-pointer">
                                            + Tambah Stasiun Kerja
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($workCenters->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $workCenters->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <form wire:submit.prevent="store">
                        <div class="bg-white px-5 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
                                <h3 class="text-base font-bold text-gray-900" id="modal-title">
                                    @if($activeTab === 'eco')
                                        {{ $isEditMode ? 'Edit Engineering Change Order (ECO)' : 'Create New Engineering Change Order (ECO)' }}
                                    @else
                                        {{ $isEditMode ? 'Edit Stasiun Kerja (Work Center)' : 'Create New Stasiun Kerja (Work Center)' }}
                                    @endif
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 cursor-pointer text-xl font-bold">&times;</button>
                            </div>

                            @if($activeTab === 'eco')
                                <!-- ECO Form Fields -->
                                <div class="space-y-4">
                                    <!-- ECO Number -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nomor ECO</label>
                                        <input type="text" wire:model="eco_number" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-gray-50 font-mono text-gray-700 focus:outline-none" readonly>
                                    </div>

                                    <!-- Product & BOM -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Produk Target <span class="text-red-500">*</span></label>
                                            <select wire:model.live="product_id" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Pilih Produk --</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code ?? '-' }})</option>
                                                @endforeach
                                            </select>
                                            @error('product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Resep BOM Terkait <span class="text-red-500">*</span></label>
                                            <select wire:model.live="bom_id" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">-- Pilih BOM --</option>
                                                @foreach($boms as $b)
                                                    <option value="{{ $b->id }}">{{ $b->product->name ?? 'BOM' }} (Versi: {{ $b->bom_version ?: '1.0' }})</option>
                                                @endforeach
                                            </select>
                                            @error('bom_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Title -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Judul / Pokok Perubahan <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="title" placeholder="Contoh: Modifikasi formula dan pengurangan bahan pengawet" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('title') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Reason & Versioning -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Alasan Perubahan</label>
                                            <select wire:model="reason" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="Cost Optimization">Cost Optimization</option>
                                                <option value="Quality Improvement">Quality Improvement</option>
                                                <option value="Component Replacement">Component Replacement</option>
                                                <option value="Regulatory Compliance">Regulatory Compliance</option>
                                                <option value="Design Enhancement">Design Enhancement</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Versi BOM Lama</label>
                                            <input type="text" wire:model="old_bom_version" placeholder="1.0" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-gray-50 text-gray-700 font-mono" readonly>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Versi BOM Baru <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="new_bom_version" placeholder="1.1" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @error('new_bom_version') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Status Pengajuan</label>
                                        <select wire:model="status" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="draft">Draft (Konsep Perubahan)</option>
                                            <option value="pending_review">Pending Review (Menunggu Persetujuan Tim Teknik)</option>
                                            <option value="approved">Approved (Disetujui)</option>
                                            <option value="applied">Applied (Langsung update versi resep BOM)</option>
                                        </select>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Deskripsi / Detail Teknis Perubahan</label>
                                        <textarea wire:model="description" rows="3" placeholder="Jelaskan komponen apa yang diganti, alasan spesifikasi, dan estimasi dampak biaya produksi..." class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                    </div>
                                </div>
                            @else
                                <!-- Work Center Form Fields -->
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Kode Stasiun Kerja <span class="text-red-500">*</span></label>
                                            <input type="text" wire:model="wc_code" placeholder="WC-01" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @error('wc_code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Status</label>
                                            <select wire:model="wc_status" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Stasiun Kerja / Mesin <span class="text-red-500">*</span></label>
                                        <input type="text" wire:model="wc_name" placeholder="Contoh: Mesin Pencetak & Autoclave Sterilisasi" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('wc_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Biaya Operasional per Jam (Rp) <span class="text-red-500">*</span></label>
                                            <div class="relative rounded-lg shadow-xs">
                                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">Rp</span>
                                                <input type="number" step="any" min="0" wire:model="wc_cost_per_hour" class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-3 text-sm text-right font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            @error('wc_cost_per_hour') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Kapasitas (Unit / Jam) <span class="text-red-500">*</span></label>
                                            <input type="number" step="0.01" min="0.01" wire:model="wc_capacity" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm font-mono text-right focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            @error('wc_capacity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Footer Actions -->
                        <div class="bg-gray-50 px-5 py-3.5 sm:px-6 flex justify-end gap-2 border-t border-gray-200">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-xs cursor-pointer disabled:opacity-50 transition-colors">
                                <span wire:loading.remove wire:target="store">{{ $isEditMode ? 'Update Data' : 'Simpan Data' }}</span>
                                <span wire:loading wire:target="store" class="inline-flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('confirmingDeletion', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-base font-bold text-gray-900">Hapus Data?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus data {{ $deleteType === 'eco' ? 'ECO' : 'Stasiun Kerja' }} ini? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                        <button type="button" wire:click="delete" class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-xs cursor-pointer">
                            Hapus
                        </button>
                        <button type="button" wire:click="$set('confirmingDeletion', false)" class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
