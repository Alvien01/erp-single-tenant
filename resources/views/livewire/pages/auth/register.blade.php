<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $company_name = '';
    public string $slug = '';

    public bool $hasInvitation = false;
    public string $invitationCompany = '';

    /**
     * Initialize invitation properties if invitation token is active in session.
     */
    public function mount(): void
    {
        $token = session('pending_invitation_token');
        if ($token) {
            $inv = \App\Models\TenantInvitation::where('token', $token)->first();
            if ($inv && $inv->isValid()) {
                $this->hasInvitation = true;
                $this->email = $inv->email;
                $this->invitationCompany = $inv->tenant->name;
            } else {
                session()->forget('pending_invitation_token');
            }
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];

        if (!$this->hasInvitation) {
            $rules['company_name'] = ['required', 'string', 'max:255'];
            $rules['slug'] = ['required', 'string', 'alpha_dash', 'max:50', 'unique:tenants,slug'];
        }

        $validated = $this->validate($rules);
        $validated['password'] = Hash::make($validated['password']);

        DB::transaction(function() use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            if ($this->hasInvitation) {
                $token = session('pending_invitation_token');
                $inv = \App\Models\TenantInvitation::where('token', $token)->first();

                if ($inv && $inv->isValid()) {
                    // Assign User to Tenant
                    \App\Models\TenantUser::create([
                        'tenant_id' => $inv->tenant_id,
                        'user_id' => $user->id,
                        'role' => $inv->role,
                        'is_active' => true,
                        'joined_at' => now(),
                    ]);

                    // Set user's current tenant_id
                    $user->update(['tenant_id' => $inv->tenant_id]);

                    // Mark invitation accepted
                    $inv->update(['accepted_at' => now()]);
                    session(['tenant_id' => $inv->tenant_id]);
                    session()->forget('pending_invitation_token');
                }
            } else {
                // Find starter plan
                $plan = \App\Models\Plan::where('name', 'Starter')->first() 
                    ?? \App\Models\Plan::first();

                // Create Tenant
                $tenant = \App\Models\Tenant::create([
                    'name' => $validated['company_name'],
                    'slug' => strtolower($validated['slug']),
                    'plan_id' => $plan?->id,
                    'is_active' => true,
                ]);

                // Assign User as Owner
                \App\Models\TenantUser::create([
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'role' => 'owner',
                    'is_active' => true,
                    'joined_at' => now(),
                ]);

                // Set user's current tenant_id
                $user->update(['tenant_id' => $tenant->id]);

                // Create Settings
                \App\Models\TenantSetting::create([
                    'tenant_id' => $tenant->id,
                    'company_name' => $validated['company_name'],
                    'currency_code' => 'IDR',
                    'timezone' => 'Asia/Jakarta',
                    'modules_enabled' => json_encode(['pos', 'hrm', 'crm', 'accounting', 'inventory', 'sales', 'purchasing']),
                ]);

                // Enable Modules
                $modules = ['pos', 'hrm', 'crm', 'accounting', 'inventory', 'sales', 'purchasing'];
                foreach ($modules as $mod) {
                    \App\Models\TenantModule::create([
                        'tenant_id' => $tenant->id,
                        'module' => $mod,
                        'is_enabled' => true,
                        'enabled_at' => now(),
                    ]);
                }

                session(['tenant_id' => $tenant->id]);
            }

            event(new Registered($user));
            Auth::login($user);
        });

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    @if($hasInvitation)
        <div class="mb-5 p-4 rounded-xl bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <div>
                <h4 class="text-sm font-bold text-indigo-900">Undangan Workspace Aktif</h4>
                <p class="text-xs text-indigo-700 leading-relaxed mt-1">Anda diundang bergabung dengan <strong>{{ $invitationCompany }}</strong>. Akun baru Anda akan otomatis dihubungkan ke workspace ini.</p>
            </div>
        </div>
    @endif

    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full {{ $hasInvitation ? 'bg-gray-100 cursor-not-allowed opacity-80' : '' }}" type="email" name="email" required autocomplete="username" :disabled="$hasInvitation" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        @if(!$hasInvitation)
            <!-- Company Name -->
            <div>
                <x-input-label for="company_name" :value="__('Company Name')" />
                <x-text-input wire:model="company_name" id="company_name" class="block mt-1 w-full" type="text" name="company_name" required placeholder="e.g. Wikasa Corp" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <!-- Subdomain Slug -->
            <div>
                <x-input-label for="slug" :value="__('Subdomain Slug')" />
                <div class="flex items-center mt-1">
                    <x-text-input wire:model="slug" id="slug" class="block w-full rounded-r-none" type="text" name="slug" required placeholder="wikasa" />
                    <span class="inline-flex items-center px-3 py-2.5 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                        .erp.com
                    </span>
                </div>
                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
            </div>
        @endif

        <!-- Password -->
        <div x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input wire:model="password" id="password" class="block w-full pr-10"
                                ::type="showPassword ? 'text' : 'password'"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                                
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="h-5 w-5" x-cloak fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div x-data="{ showPasswordConf: false }">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <div class="relative mt-1">
                <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block w-full pr-10"
                                ::type="showPasswordConf ? 'text' : 'password'"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

                <button type="button" @click="showPasswordConf = !showPasswordConf"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                    <svg x-show="!showPasswordConf" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPasswordConf" class="h-5 w-5" x-cloak fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ $hasInvitation ? __('Daftar & Gabung dengan Tim') : __('Register & Create Tenant') }}
            </x-primary-button>
        </div>
    </form>
</div>
