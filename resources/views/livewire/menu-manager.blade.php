<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Manage Menu</h1>
            <p class="text-sm text-gray-500 mt-1">Atur visibilitas dan akses menu di sidebar. Menu yang dinonaktifkan tidak akan muncul dan tidak bisa diakses.</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="enableAll" wire:confirm="Aktifkan semua menu?" class="inline-flex items-center px-3 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-800 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Aktifkan Semua
            </button>
            <button wire:click="disableAll" wire:confirm="Nonaktifkan semua menu (kecuali Dashboard)?" class="inline-flex items-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Nonaktifkan Semua
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="p-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Berhasil!</span> {{ session('success') }}
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Menu</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalMenus }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-emerald-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Menu Aktif</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalActive }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Menu Nonaktif</p>
                    <p class="text-2xl font-bold text-red-600">{{ $totalInactive }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari menu berdasarkan nama atau route...">
            </div>
            <div class="sm:w-64">
                <select wire:model.live="filterGroup" class="w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Grup</option>
                    @foreach($groups as $group)
                        <option value="{{ $group }}">{{ $group }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Menu Groups --}}
    @forelse($grouped as $groupName => $items)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            {{-- Group Header --}}
            <div class="flex items-center justify-between px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-semibold text-gray-800 font-display">{{ $groupName }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                        {{ $items->where('is_active', true)->count() }}/{{ $items->count() }} aktif
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="enableGroup('{{ $groupName }}')" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium px-2 py-1 rounded hover:bg-emerald-50 transition">
                        Aktifkan Semua
                    </button>
                    <span class="text-gray-300">|</span>
                    <button wire:click="disableGroup('{{ $groupName }}')" class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 rounded hover:bg-red-50 transition">
                        Nonaktifkan Semua
                    </button>
                </div>
            </div>

            {{-- Menu Items --}}
            <div class="divide-y divide-gray-100">
                @foreach($items as $menu)
                    <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition duration-150 {{ !$menu->is_active ? 'opacity-60' : '' }}">
                        <div class="flex items-center gap-4">
                            {{-- Status Icon --}}
                            <div class="flex-shrink-0">
                                @if($menu->is_active)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100">
                                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </span>
                                @endif
                            </div>
                            {{-- Menu Info --}}
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $menu->label }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $menu->route_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            {{-- Status Badge --}}
                            @if($menu->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>
                            @endif
                            {{-- Toggle Button --}}
                            <button
                                wire:click="toggleMenu({{ $menu->id }})"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $menu->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"
                                role="switch"
                                aria-checked="{{ $menu->is_active ? 'true' : 'false' }}"
                            >
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $menu->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada menu ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci pencarian atau filter grup.</p>
        </div>
    @endforelse
</div>
