<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header class="mb-6">
        <h3 class="text-lg font-bold font-display text-gray-900">
            Informasi Profil Pengguna
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            Perbarui nama lengkap akun dan alamat email terdaftar Anda di sistem ERP.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="space-y-5">
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
            <input wire:model="name" id="name" name="name" type="text" class="mt-1.5 block w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-xs" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap">
            @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">Alamat Email</label>
            <input wire:model="email" id="email" name="email" type="email" class="mt-1.5 block w-full px-3.5 py-2.5 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-xs" required autocomplete="username" placeholder="nama@perusahaan.com">
            @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-800">
                    <p class="font-medium">
                        Alamat email Anda belum terverifikasi.
                        <button wire:click.prevent="sendVerification" class="underline font-bold text-amber-900 hover:text-amber-700 ml-1">
                            Kirim ulang email verifikasi
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1.5 text-xs font-semibold text-emerald-700">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="inline-flex items-center px-4 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-blue-700 active:bg-blue-800 shadow-sm transition ease-in-out duration-150 cursor-pointer">
                <svg wire:loading wire:target="updateProfileInformation" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Simpan Perubahan
            </button>

            <x-action-message class="text-xs font-semibold text-emerald-600 flex items-center gap-1" on="profile-updated">
                <svg class="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                Perubahan berhasil disimpan.
            </x-action-message>
        </div>
    </form>
</section>
