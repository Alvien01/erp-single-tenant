<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-display text-[var(--color-text)]">🏪 Manajemen Store / Cabang</h1>
            <p class="text-sm text-gray-500">Kelola cabang toko dan pengaturannya</p>
        </div>
        <button wire:click="openCreate" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Store
        </button>
    </div>

    @if(session('success'))<div class="p-3 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 text-sm rounded">{{ session('success') }}</div>@endif

    <div class="bg-white rounded-lg shadow-sm border p-4">
        <input type="text" wire:model.live.debounce.300ms="search" class="w-full border-gray-300 rounded-lg text-sm" placeholder="Cari store...">
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($stores as $s)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded font-bold">{{ $s->code }}</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <h3 class="font-bold text-gray-900 mt-2 text-lg">{{ $s->name }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                </div>
                <div class="space-y-1 text-sm text-gray-500">
                    @if($s->address)<p class="truncate">📍 {{ $s->address }}</p>@endif
                    @if($s->phone)<p>📞 {{ $s->phone }}</p>@endif
                    <p>💰 PPN: {{ $s->tax_rate }}% | SC: {{ $s->service_charge_rate }}%</p>
                    @if($s->warehouse)<p>📦 Gudang: {{ $s->warehouse->warehouse_name }}</p>@endif
                </div>
            </div>
            <div class="px-5 py-3 border-t bg-gray-50 flex justify-end gap-2">
                <button wire:click="toggleActive({{ $s->id }})" class="px-3 py-1.5 text-xs rounded border {{ $s->is_active ? 'text-red-600 border-red-200 hover:bg-red-50' : 'text-emerald-600 border-emerald-200 hover:bg-emerald-50' }}">
                    {{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                <button wire:click="openEdit({{ $s->id }})" class="px-3 py-1.5 text-xs text-blue-600 border border-blue-200 rounded hover:bg-blue-50">Edit</button>
                <button wire:click="delete({{ $s->id }})" onclick="return confirm('Hapus store?')" class="px-3 py-1.5 text-xs text-red-600 border border-red-200 rounded hover:bg-red-50">Hapus</button>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-gray-400">
            <p class="text-lg font-medium">Belum ada store</p>
            <p class="text-sm">Buat store pertama untuk mulai menggunakan POS</p>
        </div>
        @endforelse
    </div>
    <div>{{ $stores->links() }}</div>

    <!-- Store Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b sticky top-0 bg-white z-10 flex justify-between items-center">
                <h3 class="font-bold text-lg">{{ $editingId ? 'Edit' : 'Tambah' }} Store</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>
            <div class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Kode Store *</label><input type="text" wire:model="code" class="w-full border-gray-300 rounded-lg text-sm uppercase" placeholder="mis: CBG01"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Store *</label><input type="text" wire:model="name" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label><textarea wire:model="address" rows="2" class="w-full border-gray-300 rounded-lg text-sm"></textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label><input type="text" wire:model="phone" class="w-full border-gray-300 rounded-lg text-sm"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" wire:model="email" class="w-full border-gray-300 rounded-lg text-sm"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Tarif Pajak (%)</label><input type="number" wire:model="tax_rate" class="w-full border-gray-300 rounded-lg text-sm" step="0.01"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Service Charge (%)</label><input type="number" wire:model="service_charge_rate" class="w-full border-gray-300 rounded-lg text-sm" step="0.01"></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Gudang Terkait</label><select wire:model="warehouse_id" class="w-full border-gray-300 rounded-lg text-sm"><option value="">-- Pilih Gudang --</option>@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->warehouse_name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Header Struk</label><input type="text" wire:model="receipt_header" class="w-full border-gray-300 rounded-lg text-sm" placeholder="mis: Nama Toko / Slogan"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Footer Struk</label><input type="text" wire:model="receipt_footer" class="w-full border-gray-300 rounded-lg text-sm"></div>
                <div class="flex items-center gap-2"><input type="checkbox" wire:model="is_active" id="storeActive" class="rounded text-blue-600"><label for="storeActive" class="text-sm text-gray-700">Store Aktif</label></div>
            </div>
            <div class="p-5 border-t flex justify-end gap-2 sticky bottom-0 bg-white">
                <button wire:click="$set('showModal', false)" class="px-4 py-2 text-gray-600 border rounded-lg text-sm">Batal</button>
                <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
