<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header class="mb-4">
        <div class="flex items-center gap-2">
            <span class="p-2 bg-red-100 rounded-lg text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </span>
            <div>
                <h3 class="text-lg font-bold font-display text-red-700">
                    Zona Bahaya: Hapus Akun
                </h3>
                <p class="text-xs text-gray-500">
                    Tindakan ini permanen dan tidak dapat dibatalkan.
                </p>
            </div>
        </div>

        <p class="mt-3 text-sm text-gray-600 bg-red-50/70 p-3.5 rounded-xl border border-red-200">
            Setelah akun Anda dihapus, semua data dan hak akses Anda di sistem ERP ini akan dihapus secara permanen. Pastikan Anda telah menyimpan atau mendokumentasikan data yang diperlukan sebelum melanjutkan.
        </p>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center px-4 py-2.5 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-red-700 active:bg-red-800 shadow-sm transition ease-in-out duration-150 cursor-pointer"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus Akun Saya
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 font-display">
                        Konfirmasi Penghapusan Akun
                    </h3>
                    <p class="text-xs text-gray-500">
                        Apakah Anda yakin ingin menghapus akun ini?
                    </p>
                </div>
            </div>

            <p class="text-sm text-gray-600 mb-5">
                Silakan masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda benar-benar ingin menghapus akun ini secara permanen.
            </p>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi Anda</label>
                <input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1.5 block w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all shadow-xs"
                    placeholder="Masukkan kata sandi untuk konfirmasi"
                    autofocus
                />
                @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </button>

                <button type="submit" class="px-4 py-2 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 active:bg-red-800 transition-colors shadow-sm cursor-pointer">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>
