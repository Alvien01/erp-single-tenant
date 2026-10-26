<x-app-layout>
    <div class="space-y-6" x-data="{ activeTab: 'profile' }">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">User Profile</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola data identitas pengguna, keamanan akun, dan preferensi profil Anda.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-2 h-2 rounded-full bg-blue-600 mr-2"></span>
                    Role: {{ ucfirst(auth()->user()->role ?? 'Administrator') }}
                </span>
            </div>
        </div>

        {{-- Profile Summary Header Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-bold font-display text-2xl flex items-center justify-center shadow-md shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 font-display flex items-center gap-2">
                            {{ auth()->user()->name }}
                            @if(auth()->user()->email_verified_at)
                                <span title="Email Terverifikasi" class="text-blue-500 inline-flex items-center">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                </span>
                            @endif
                        </h2>
                        <p class="text-sm text-gray-500 font-medium">{{ auth()->user()->email }}</p>
                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-400">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Bergabung sejak {{ auth()->user()->created_at ? auth()->user()->created_at->format('d M Y') : '-' }}
                            </span>
                            @if(auth()->user()->phone)
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    {{ auth()->user()->phone }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-stretch sm:self-auto justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-gray-100">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                        Status: Akun Aktif
                    </span>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200 font-display overflow-x-auto whitespace-nowrap">
            <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                <button @click="activeTab = 'profile'" 
                        :class="activeTab === 'profile' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informasi Profil
                </button>
                <button @click="activeTab = 'password'" 
                        :class="activeTab === 'password' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Keamanan & Password
                </button>
                <button @click="activeTab = 'danger'" 
                        :class="activeTab === 'danger' ? 'border-red-600 text-red-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm transition-colors cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Akun
                </button>
            </nav>
        </div>

        {{-- Tab 1: Profile Information Form --}}
        <div x-show="activeTab === 'profile'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="max-w-2xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        {{-- Tab 2: Update Password Form --}}
        <div x-show="activeTab === 'password'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="max-w-2xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        {{-- Tab 3: Danger Zone / Delete Account Form --}}
        <div x-show="activeTab === 'danger'" x-cloak class="bg-white rounded-xl shadow-sm border border-red-200 p-6">
            <div class="max-w-2xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
