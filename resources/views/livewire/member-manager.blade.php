<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-display text-[var(--color-text)]">👥 Member & Loyalty</h1>
            <p class="text-sm text-gray-500">Kelola member, tier, dan poin loyalitas</p>
        </div>
        <button wire:click="openCreate" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Member
        </button>
    </div>

    @if(session('success'))
    <div class="p-3 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 text-sm rounded">{{ session('success') }}</div>
    @endif

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Cari member: nama, no hp, kode member...">
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Kode</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Nama</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">No. HP</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Tier</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Poin</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Total Belanja</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($members as $m)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono text-xs text-blue-600">{{ $m->member_code }}</td>
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $m->name }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ $m->phone ?? '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            @php $tierColors = ['gold' => 'bg-amber-100 text-amber-800', 'silver' => 'bg-gray-100 text-gray-800', 'bronze' => 'bg-orange-100 text-orange-800']; @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $tierColors[$m->tier] ?? 'bg-gray-100 text-gray-600' }}">{{ ucfirst($m->tier) }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono font-bold">{{ number_format($m->total_points) }}</td>
                        <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($m->total_spending, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <button wire:click="openEdit({{ $m->id }})" class="p-1.5 text-gray-400 hover:text-blue-600 rounded" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="openPointAdjust({{ $m->id }})" class="p-1.5 text-gray-400 hover:text-emerald-600 rounded" title="Atur Poin">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                </button>
                                <button wire:click="showHistory({{ $m->id }})" class="p-1.5 text-gray-400 hover:text-purple-600 rounded" title="Riwayat">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </button>
                                <button wire:click="delete({{ $m->id }})" onclick="return confirm('Hapus member ini?')" class="p-1.5 text-gray-400 hover:text-red-600 rounded" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">Belum ada data member.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $members->links() }}</div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div class="p-5 border-b flex justify-between items-center">
                <h3 class="font-bold text-lg">{{ $editingId ? 'Edit' : 'Tambah' }} Member</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-5 space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label><input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg text-sm"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label><input type="text" wire:model="phone" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" wire:model="email" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tgl Lahir</label><input type="date" wire:model="birth_date" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                        <select wire:model="tier" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="bronze">Bronze (0%)</option>
                            <option value="silver">Silver (2%)</option>
                            <option value="gold">Gold (5%)</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Store/Cabang</label>
                    <select wire:model="store_id" class="w-full border-gray-300 rounded-lg text-sm">
                        <option value="">Semua Cabang</option>
                        @foreach($stores as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="p-5 border-t flex justify-end gap-2">
                <button wire:click="$set('showModal', false)" class="px-4 py-2 text-gray-600 border rounded-lg text-sm">Batal</button>
                <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Point Adjustment Modal -->
    @if($showPointModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
            <div class="p-5 border-b"><h3 class="font-bold text-lg">Atur Poin Member</h3></div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select wire:model="pointType" class="w-full border-gray-300 rounded-lg text-sm">
                        <option value="adjust">Tambah Poin</option>
                        <option value="redeem">Tukar Poin</option>
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Poin</label><input type="number" wire:model="pointAmount" class="w-full border-gray-300 rounded-lg text-sm" min="0"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label><input type="text" wire:model="pointDescription" class="w-full border-gray-300 rounded-lg text-sm"></div>
            </div>
            <div class="p-5 border-t flex justify-end gap-2">
                <button wire:click="$set('showPointModal', false)" class="px-4 py-2 text-gray-600 border rounded-lg text-sm">Batal</button>
                <button wire:click="adjustPoints" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium">Proses</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction History Modal -->
    @if($showHistoryModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[80vh] flex flex-col">
            <div class="p-5 border-b flex justify-between items-center">
                <h3 class="font-bold text-lg">Riwayat Transaksi Member</h3>
                <button wire:click="$set('showHistoryModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-5 overflow-y-auto">
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead><tr class="text-left text-gray-500"><th class="py-2 px-3">No. Transaksi</th><th class="py-2 px-3">Tanggal</th><th class="py-2 px-3 text-right">Total</th><th class="py-2 px-3">Metode</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactionHistory as $trx)
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600 text-xs">{{ $trx->transaction_number }}</td>
                            <td class="py-2 px-3">{{ $trx->created_at->format('d M Y H:i') }}</td>
                            <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($trx->grand_total, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 uppercase text-xs font-bold">{{ $trx->payment_method }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
