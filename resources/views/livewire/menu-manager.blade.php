<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Manage Menu</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola menu navigasi sidebar ERP: tambah, ubah label, atur grup, urutan, dan visibilitas menu.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-blue-700 active:bg-blue-800 shadow-sm transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Menu
            </button>
            <button wire:click="syncFromRoutes" wire:loading.attr="disabled" class="inline-flex items-center px-3.5 py-2 bg-white border border-gray-300 rounded-lg font-medium text-xs text-gray-700 hover:bg-gray-50 active:bg-gray-100 shadow-sm transition ease-in-out duration-150" title="Pindai routes/web.php dan tambahkan route yang belum terdaftar">
                <svg wire:loading.remove wire:target="syncFromRoutes" class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <svg wire:loading wire:target="syncFromRoutes" class="animate-spin w-4 h-4 mr-1.5 text-blue-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Sinkronkan Route
            </button>
            <button wire:click="enableAll" wire:confirm="Aktifkan semua menu?" class="inline-flex items-center px-3 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-emerald-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Aktifkan Semua
            </button>
            <button wire:click="disableAll" wire:confirm="Nonaktifkan semua menu (kecuali Dashboard & Manage Menu)?" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-red-700 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Nonaktifkan Semua
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="p-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><strong>Berhasil!</strong> {{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><strong>Peringatan!</strong> {{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="p-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-between" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('info') }}</span>
            </div>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Total Menu</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalMenus }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Menu Aktif</p>
                    <p class="text-xl font-bold text-emerald-600">{{ $totalActive }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Menu Nonaktif</p>
                    <p class="text-xl font-bold text-red-600">{{ $totalInactive }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-2.5">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div class="ml-3">
                    <p class="text-xs font-medium text-gray-500">Total Grup</p>
                    <p class="text-xl font-bold text-purple-600">{{ count($groups) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter Controls --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari nama menu, route, atau grup...">
            </div>
            <div>
                <select wire:model.live="filterGroup" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Grup Menu</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp }}">{{ $grp }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterStatus" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active">Hanya Aktif</option>
                    <option value="inactive">Hanya Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Menu Groups & Items List --}}
    <div class="space-y-4">
        @forelse($grouped as $groupName => $items)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                {{-- Group Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-3.5 bg-gray-50 border-b border-gray-200 gap-2">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        <h3 class="text-base font-bold text-gray-800 font-display">{{ $groupName }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                            {{ $items->where('is_active', true)->count() }} / {{ $items->count() }} aktif
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="enableGroup('{{ $groupName }}')" class="text-xs text-emerald-700 hover:text-emerald-900 font-medium px-2 py-1 rounded hover:bg-emerald-50 transition">
                            Aktifkan Semua
                        </button>
                        <span class="text-gray-300">|</span>
                        <button wire:click="disableGroup('{{ $groupName }}')" class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded hover:bg-red-50 transition">
                            Nonaktifkan Semua
                        </button>
                    </div>
                </div>

                {{-- Table of Menu Items in this Group --}}
                <div class="divide-y divide-gray-100">
                    @foreach($items as $menu)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-3 hover:bg-gray-50 transition duration-150 gap-3 {{ !$menu->is_active ? 'bg-gray-50/50 opacity-60' : '' }}">
                            
                            {{-- Left: Order, Route & Label --}}
                            <div class="flex items-center gap-3">
                                {{-- Sort Order Controls --}}
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <button wire:click="moveOrder({{ $menu->id }}, 'up')" title="Pindah ke Atas" class="hover:text-blue-600 focus:outline-none p-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <span class="text-[10px] font-mono font-semibold text-gray-500">{{ $menu->sort_order }}</span>
                                    <button wire:click="moveOrder({{ $menu->id }}, 'down')" title="Pindah ke Bawah" class="hover:text-blue-600 focus:outline-none p-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                </div>

                                {{-- Label & Route Name --}}
                                <div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-900">{{ $menu->label }}</p>
                                        @if($menu->is_active)
                                            <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">Aktif</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.2 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-blue-600 font-mono mt-0.5 flex items-center gap-1">
                                        <span class="text-gray-400">Route:</span> {{ $menu->route_name }}
                                    </p>
                                </div>
                            </div>

                            {{-- Right: Status Switch & Actions (Edit / Delete) --}}
                            <div class="flex items-center gap-4 self-end sm:self-center">
                                {{-- Quick Toggle Switch --}}
                                <button
                                    type="button"
                                    wire:click="toggleMenu({{ $menu->id }})"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $menu->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"
                                    role="switch"
                                    aria-checked="{{ $menu->is_active ? 'true' : 'false' }}"
                                    title="{{ $menu->is_active ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}"
                                >
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $menu->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>

                                {{-- Action Buttons --}}
                                <div class="flex items-center space-x-2 border-l border-gray-200 pl-4">
                                    <button wire:click="edit({{ $menu->id }})" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md transition focus:outline-none" title="Ubah menu">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>

                                    @if($menu->route_name !== 'manage-menu')
                                        <button wire:click="delete({{ $menu->id }})" wire:confirm="Yakin ingin menghapus menu '{{ $menu->label }}'?" class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-md transition focus:outline-none" title="Hapus menu">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            Hapus
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mt-2 text-base font-semibold text-gray-900">Tidak ada menu yang sesuai</h3>
                <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau reset filter grup dan status.</p>
                <div class="mt-4">
                    <button wire:click="create" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Tambah Menu Baru
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modal Form (Tambah / Edit Menu) --}}
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- Backdrop --}}
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Box --}}
                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    {{-- Modal Header --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900" id="modal-title">
                                {{ $isEditMode ? 'Edit Menu' : 'Tambah Menu Baru' }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $isEditMode ? 'Perbarui informasi menu yang tampil di sidebar' : 'Tambahkan item menu baru ke dalam navigasi sidebar' }}
                            </p>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <form wire:submit.prevent="store" class="px-6 py-5 space-y-4">
                        
                        {{-- Route Selection --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-semibold text-gray-700">Nama Route <span class="text-red-500">*</span></label>
                                <label class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 cursor-pointer">
                                    <input type="checkbox" wire:model.live="is_custom_route" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mr-1.5">
                                    Ketik Manual
                                </label>
                            </div>

                            @if(!$is_custom_route)
                                <select wire:model.live="route_name" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Route dari Sistem --</option>
                                    @foreach($appRoutes as $r)
                                        <option value="{{ $r['name'] }}">
                                            {{ $r['name'] }} ({{ $r['uri'] }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="route_name" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono" placeholder="misal: products, sales, reports">
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Nama route Laravel yang dipanggil saat menu diklik.</p>
                            @error('route_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Label Menu --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Label Menu <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="label" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="misal: Data Produk, Invoices / Sales">
                            <p class="text-xs text-gray-500 mt-1">Teks judul yang ditampilkan kepada pengguna di sidebar navigasi.</p>
                            @error('label') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Group Menu --}}
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-sm font-semibold text-gray-700">Grup / Kategori Menu</label>
                                <label class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 cursor-pointer">
                                    <input type="checkbox" wire:model.live="is_custom_group" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500 mr-1.5">
                                    Grup Baru
                                </label>
                            </div>

                            @if(!$is_custom_group)
                                <select wire:model="group" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Tanpa Grup (Mandiri) --</option>
                                    @foreach($groups as $grp)
                                        <option value="{{ $grp }}">{{ $grp }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" wire:model="new_group_name" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nama grup baru, misal: Logistik & Distribusi">
                            @endif
                            <p class="text-xs text-gray-500 mt-1">Menu akan dikelompokkan ke dalam dropdown grup ini di sidebar.</p>
                            @error('new_group_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- Sort Order & Status in 2 columns --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            {{-- Sort Order --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampilan <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="sort_order" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <p class="text-[11px] text-gray-500 mt-1">Angka lebih kecil tampil lebih atas.</p>
                                @error('sort_order') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Status Aktif Switch --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Status Menu</label>
                                <div class="flex items-center h-10 gap-3">
                                    <button
                                        type="button"
                                        wire:click="$toggle('is_active')"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"
                                    >
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-sm font-medium {{ $is_active ? 'text-emerald-700' : 'text-gray-500' }}">
                                        {{ $is_active ? 'Aktif (Tampil)' : 'Nonaktif (Sembunyi)' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="pt-4 border-t border-gray-200 flex items-center justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none">
                                Batal
                            </button>
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none shadow-sm">
                                <svg wire:loading wire:target="store" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                {{ $isEditMode ? 'Simpan Perubahan' : 'Tambah Menu' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
