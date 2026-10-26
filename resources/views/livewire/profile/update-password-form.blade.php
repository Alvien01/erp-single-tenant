<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header class="mb-6">
        <h3 class="text-lg font-bold font-display text-gray-900">
            Perbarui Kata Sandi (Password)
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            Pastikan akun Anda menggunakan kombinasi kata sandi yang aman dan tidak mudah ditebak.
        </p>
    </header>

    <form wire:submit="updatePassword" class="space-y-5">
        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-gray-700">Kata Sandi Saat Ini</label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="mt-1.5 block w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-xs" autocomplete="current-password" placeholder="••••••••">
            @error('current_password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-gray-700">Kata Sandi Baru</label>
            <input wire:model="password" id="update_password_password" name="password" type="password" class="mt-1.5 block w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-xs" autocomplete="new-password" placeholder="Minimal 8 karakter">
            @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Kata Sandi Baru</label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1.5 block w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-xs" autocomplete="new-password" placeholder="Ketik ulang kata sandi baru">
            @error('password_confirmation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-blue-700 active:bg-blue-800 shadow-sm transition ease-in-out duration-150 cursor-pointer">
                <svg wire:loading wire:target="updatePassword" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Perbarui Password
            </button>

            <x-action-message class="text-xs font-semibold text-emerald-600 flex items-center gap-1" on="password-updated">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                Password berhasil diperbarui.
            </x-action-message>
        </div>
    </form>
</section>
