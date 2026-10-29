<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">System Settings</h1>
            <p class="text-sm text-gray-500 mt-1">Configure company profiles, manage users, roles, and administrative tasks.</p>
        </div>
        @if($activeTab === 'users')
            <button wire:click="createUser" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add User
            </button>
        @endif
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'company')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'company' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Company Profile
            </button>
            <button wire:click="$set('activeTab', 'users')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                User Management
            </button>
        </nav>
    </div>

    <!-- Company Profile Tab -->
    @if($activeTab === 'company')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 font-sans">
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Edit Company Profile</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company Name</label>
                    <input type="text" wire:model="company_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('company_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tax Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tax Registration Number (NPWP)</label>
                    <input type="text" wire:model="company_tax_number" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('company_tax_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" wire:model="company_phone" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" wire:model="company_email" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea wire:model="company_address" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" wire:click="saveCompany" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                    Save Company Profile
                </button>
            </div>
        </div>

    <!-- User Management Tab -->
    @elseif($activeTab === 'users')
        <!-- Search bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search users by name or email...">
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Name</th>
                            <th class="py-3.5 px-6">Email</th>
                            <th class="py-3.5 px-6">Phone</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Hak Akses Menu</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $user->name }}</td>
                                <td class="py-4 px-6 text-gray-700 font-mono">{{ $user->email }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $user->phone ?: '-' }}</td>
                                <td class="py-4 px-6 text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Semua Menu (Admin)
                                        </span>
                                    @elseif($user->allowed_menus === null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            Semua Menu (Default)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            <span class="font-bold text-blue-600 mr-1">{{ count($user->allowed_menus) }}</span> / {{ $totalActiveMenus }} Menu
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    <button wire:click="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- User Modal (Create/Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isOpen', false)"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="$set('isOpen', false)" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 font-display" id="modal-title">
                                {{ $isEditMode ? 'Edit User & Hak Akses Menu' : 'Tambah User & Atur Hak Akses' }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Atur profil user, role kewenangan, serta checklist menu-menu yang diizinkan untuk diakses.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mt-4">
                            <!-- Kolom Kiri: Profil & Kredensial User (5 Kolom) -->
                            <div class="lg:col-span-5 space-y-4 font-sans">
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">1. Informasi Akun & Role</h4>

                                <!-- Name -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">Nama Lengkap</label>
                                    <input type="text" wire:model="user_name" placeholder="cth. Budi Santoso" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @error('user_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">Email Address</label>
                                    <input type="email" wire:model="user_email" placeholder="budi@radiamartha.com" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500 font-mono">
                                    @error('user_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">Nomor Telepon</label>
                                    <input type="text" wire:model="user_phone" placeholder="08123456789" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @error('user_phone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Role -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-semibold text-gray-700">Role / Kewenangan</label>
                                        <button type="button" wire:click="applyRolePreset" class="text-[11px] text-blue-600 hover:text-blue-800 font-medium">
                                            Reset Preset Role
                                        </button>
                                    </div>
                                    <select wire:model.live="user_role" class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="admin">Admin (Akses Penuh Seluruh Sistem)</option>
                                        <option value="manager">Manager (Operasional & Laporan)</option>
                                        <option value="finance">Finance / Accountant (Keuangan & Pajak)</option>
                                        <option value="warehouse">Warehouse Officer (Gudang & Logistik)</option>
                                        <option value="hr">HR Specialist (Kepegawaian & Rekrutmen)</option>
                                        <option value="user">User Standar (Penjualan / Terbatas)</option>
                                    </select>
                                    <p class="text-[11px] text-gray-500 mt-1">Mengubah role akan otomatis menyesuaikan checklist menu di sebelah kanan.</p>
                                </div>

                                <!-- Password -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700">Password {{ $isEditMode ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                                    <input type="password" wire:model="user_password" placeholder="••••••••" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                    @error('user_password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan: Checklist Menu Yang Bisa Diakses (7 Kolom) -->
                            <div class="lg:col-span-7 font-sans flex flex-col border-t lg:border-t-0 lg:border-l border-gray-200 pt-4 lg:pt-0 lg:pl-6">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-gray-150">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">2. Checklist Akses Menu</h4>
                                            @if($user_role === 'admin')
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800">
                                                    Semua Menu (Admin)
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ count($selected_menus) > 0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-700' }}">
                                                    {{ count($selected_menus) }} / {{ $totalActiveMenus }} Dipilih
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-0.5">Hanya menu yang dicentang yang akan tampil di sidebar user ini.</p>
                                    </div>

                                    <!-- Quick Select Buttons -->
                                    <div class="flex items-center gap-1.5 flex-shrink-0">
                                        <button type="button" wire:click="selectAllMenus" class="px-2 py-1 text-xs font-medium rounded bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200">
                                            Pilih Semua
                                        </button>
                                        <button type="button" wire:click="deselectAllMenus" class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700 hover:bg-gray-200 border border-gray-200">
                                            Batal Semua
                                        </button>
                                    </div>
                                </div>

                                @if($user_role === 'admin')
                                    <div class="my-3 p-3 bg-purple-50 border border-purple-200 rounded-lg flex items-start gap-2.5">
                                        <svg class="w-5 h-5 text-purple-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <div class="text-xs text-purple-900">
                                            <p class="font-bold">Akses Penuh Administrator</p>
                                            <p class="mt-0.5 text-purple-700">Role Admin memiliki izin membuka seluruh menu sistem tanpa batasan. Anda dapat memilih role lain jika ingin membatasi menu tertentu.</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Scrollable Grouped Checklist -->
                                <div class="mt-3 overflow-y-auto max-h-[380px] pr-2 space-y-4 divide-y divide-gray-100">
                                    @foreach($menuGroupsData as $group)
                                        <div class="pt-3 first:pt-0">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wide">
                                                        {{ $group['group_name'] ?: 'Lainnya' }}
                                                    </span>
                                                    <span class="text-[10px] text-gray-500 font-mono">
                                                        ({{ $group['selected_count'] }}/{{ $group['total_count'] }})
                                                    </span>
                                                </div>
                                                <button type="button"
                                                        wire:click="toggleGroupMenus('{{ $group['group_name'] }}')"
                                                        class="text-[11px] font-medium text-blue-600 hover:text-blue-800 cursor-pointer">
                                                    {{ $group['all_selected'] ? 'Batal Grup' : 'Pilih Grup' }}
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach($group['menus'] as $menu)
                                                    <label class="flex items-start p-2 rounded-lg border cursor-pointer transition text-xs select-none {{ $menu['is_checked'] ? 'bg-blue-50/70 border-blue-300 text-blue-900' : 'bg-gray-50/50 border-gray-200 text-gray-700 hover:bg-gray-100' }}">
                                                        <input type="checkbox"
                                                               wire:model.live="selected_menus"
                                                               value="{{ $menu['route_name'] }}"
                                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-0.5 mr-2 shrink-0">
                                                        <div class="min-w-0">
                                                            <span class="font-medium block truncate">{{ $menu['label'] }}</span>
                                                            <span class="text-[10px] text-gray-400 font-mono block truncate">{{ $menu['route_name'] }}</span>
                                                        </div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="$set('isOpen', false)" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none font-display">
                            Batal
                        </button>
                        <button type="button" wire:click="saveUser" class="inline-flex justify-center px-5 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none font-display font-semibold shadow-sm">
                            Simpan User & Hak Akses
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
