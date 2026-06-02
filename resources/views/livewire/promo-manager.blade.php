<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-display text-[var(--color-text)]">🎯 Promo & Diskon</h1>
            <p class="text-sm text-gray-500">Kelola promosi, voucher, dan diskon</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="openCreatePromo" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Promo Baru
            </button>
            <button wire:click="openCreateVoucher" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-medium text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                Voucher Baru
            </button>
        </div>
    </div>

    @if(session('success'))<div class="p-3 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 text-sm rounded">{{ session('success') }}</div>@endif

    <!-- Tabs -->
    <div class="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit">
        <button wire:click="$set('activeTab', 'promos')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'promos' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Promosi</button>
        <button wire:click="$set('activeTab', 'vouchers')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'vouchers' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">Voucher</button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border p-4">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Cari...">
    </div>

    @if($activeTab === 'promos')
    <!-- Promos Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Nama Promo</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Tipe</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Nilai</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Periode</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Status</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($promos as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="font-medium text-gray-900">{{ $p->name }}</div>
                            <div class="text-xs text-gray-400">{{ $p->description }}</div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            @php $typeLabels = ['percentage'=>'Diskon %','fixed'=>'Diskon Rp','bogo'=>'BOGO','bundle'=>'Bundle','tiered'=>'Bertingkat','member_only'=>'Member']; @endphp
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs font-medium">{{ $typeLabels[$p->type] ?? $p->type }}</span>
                        </td>
                        <td class="py-3 px-4 text-right font-mono">
                            @if($p->type === 'percentage') {{ $p->value }}% @elseif($p->type === 'bogo') Beli {{ $p->buy_qty }} Gratis {{ $p->free_qty }} @else Rp {{ number_format($p->value, 0, ',', '.') }} @endif
                        </td>
                        <td class="py-3 px-4 text-center text-xs text-gray-500">
                            @if($p->start_date) {{ $p->start_date->format('d/m') }} - {{ $p->end_date?->format('d/m') ?? '∞' }} @else Tanpa batas @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button wire:click="togglePromo({{ $p->id }})" class="px-2 py-0.5 rounded-full text-xs font-bold {{ $p->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $p->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <button wire:click="openEditPromo({{ $p->id }})" class="p-1 text-gray-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                            <button wire:click="deletePromo({{ $p->id }})" onclick="return confirm('Hapus promo?')" class="p-1 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada promosi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $promos->links() }}</div>
    </div>
    @else
    <!-- Vouchers Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Kode Voucher</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Tipe</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Nilai</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Terpakai</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Berlaku</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Status</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vouchers as $v)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ $v->code }}</td>
                        <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded text-xs font-medium">{{ $v->type === 'percentage' ? '%' : 'Rp' }}</span></td>
                        <td class="py-3 px-4 text-right font-mono">{{ $v->type === 'percentage' ? $v->value.'%' : 'Rp '.number_format($v->value,0,',','.') }}</td>
                        <td class="py-3 px-4 text-center text-xs">{{ $v->used_count }}/{{ $v->max_uses }}</td>
                        <td class="py-3 px-4 text-center text-xs text-gray-500">{{ $v->valid_until ? $v->valid_until->format('d/m/Y') : '∞' }}</td>
                        <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $v->isValid() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $v->isValid() ? 'Valid' : 'Invalid' }}</span></td>
                        <td class="py-3 px-4 text-center">
                            <button wire:click="openEditVoucher({{ $v->id }})" class="p-1 text-gray-400 hover:text-blue-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                            <button wire:click="deleteVoucher({{ $v->id }})" onclick="return confirm('Hapus?')" class="p-1 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-8 text-center text-gray-400">Belum ada voucher.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $vouchers->links() }}</div>
    </div>
    @endif

    <!-- Promo Modal -->
    @if($showPromoModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                <h3 class="font-bold text-lg">{{ $editingPromoId ? 'Edit' : 'Buat' }} Promosi</h3>
                <button wire:click="$set('showPromoModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-5 space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Promo *</label><input type="text" wire:model="promoName" class="w-full border-gray-300 rounded-lg text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label><textarea wire:model="promoDescription" rows="2" class="w-full border-gray-300 rounded-lg text-sm"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Promo *</label>
                        <select wire:model.live="promoType" class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="percentage">Diskon Persentase (%)</option>
                            <option value="fixed">Diskon Nominal (Rp)</option>
                            <option value="bogo">Beli X Gratis Y (BOGO)</option>
                            <option value="bundle">Bundle Produk</option>
                            <option value="tiered">Diskon Bertingkat</option>
                            <option value="member_only">Khusus Member</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nilai Diskon</label><input type="number" wire:model="promoValue" class="w-full border-gray-300 rounded-lg text-sm" step="0.01"></div>
                </div>
                @if($promoType === 'bogo')
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Beli (qty)</label><input type="number" wire:model="promoBuyQty" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Gratis (qty)</label><input type="number" wire:model="promoFreeQty" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Min. Pembelian</label><input type="number" wire:model="promoMinPurchase" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Max. Diskon</label><input type="number" wire:model="promoMaxDiscount" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Kosongkan = unlimited"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tgl Mulai</label><input type="date" wire:model="promoStartDate" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tgl Berakhir</label><input type="date" wire:model="promoEndDate" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label><input type="time" wire:model="promoStartTime" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Jam Berakhir</label><input type="time" wire:model="promoEndTime" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Kategori Produk</label><select wire:model="promoCategoryId" class="w-full border-gray-300 rounded-lg text-sm"><option value="">Semua Kategori</option>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tier Member</label><select wire:model="promoMemberTier" class="w-full border-gray-300 rounded-lg text-sm"><option value="">Semua Tier</option><option value="bronze">Bronze</option><option value="silver">Silver</option><option value="gold">Gold</option></select></div>
                </div>
                <div class="flex items-center gap-2"><input type="checkbox" wire:model="promoIsActive" id="promoActive" class="rounded text-blue-600"><label for="promoActive" class="text-sm text-gray-700">Aktif</label></div>
            </div>
            <div class="p-5 border-t flex justify-end gap-2 sticky bottom-0 bg-white">
                <button wire:click="$set('showPromoModal', false)" class="px-4 py-2 text-gray-600 border rounded-lg text-sm">Batal</button>
                <button wire:click="savePromo" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Voucher Modal -->
    @if($showVoucherModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg">
            <div class="p-5 border-b flex justify-between items-center">
                <h3 class="font-bold text-lg">{{ $editingVoucherId ? 'Edit' : 'Buat' }} Voucher</h3>
                <button wire:click="$set('showVoucherModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-5 space-y-4">
                @if(!$editingVoucherId)
                <div class="flex items-center gap-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <input type="checkbox" wire:model.live="generateBatch" id="batchMode" class="rounded text-blue-600">
                    <label for="batchMode" class="text-sm font-medium text-blue-700">Generate batch voucher otomatis</label>
                </div>
                @if($generateBatch)
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Voucher</label><input type="number" wire:model="batchCount" class="w-full border-gray-300 rounded-lg text-sm" min="1" max="100"></div>
                @endif
                @endif
                @if(!$generateBatch)
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Kode Voucher</label><input type="text" wire:model="voucherCode" class="w-full border-gray-300 rounded-lg text-sm uppercase" placeholder="Kosongkan untuk auto-generate"></div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label><select wire:model="voucherType" class="w-full border-gray-300 rounded-lg text-sm"><option value="percentage">Persentase (%)</option><option value="fixed">Nominal (Rp)</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nilai</label><input type="number" wire:model="voucherValue" class="w-full border-gray-300 rounded-lg text-sm" step="0.01"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Min. Belanja</label><input type="number" wire:model="voucherMinPurchase" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Maks Pemakaian</label><input type="number" wire:model="voucherMaxUses" class="w-full border-gray-300 rounded-lg text-sm" min="1"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Dari</label><input type="date" wire:model="voucherValidFrom" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Berlaku Sampai</label><input type="date" wire:model="voucherValidUntil" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
            </div>
            <div class="p-5 border-t flex justify-end gap-2">
                <button wire:click="$set('showVoucherModal', false)" class="px-4 py-2 text-gray-600 border rounded-lg text-sm">Batal</button>
                <button wire:click="saveVoucher" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium">{{ $generateBatch ? 'Generate Voucher' : 'Simpan' }}</button>
            </div>
        </div>
    </div>
    @endif
</div>
