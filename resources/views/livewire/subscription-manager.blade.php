<div>
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Subscription Manager</h2>
            <p class="text-sm text-gray-500 mt-0.5">Kelola paket langganan berulang, tagihan periodik, dan status aktif customer.</p>
        </div>
        <button type="button" 
                wire:click="create" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 cursor-pointer">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Create New Subscription
        </button>
    </div>

    <!-- Success Message Alert -->
    @if(session()->has('success'))
        <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-emerald-800 text-sm shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Subscriptions</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $subscriptions->total() }}</p>
            </div>
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Subscriptions</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeCount }}</p>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Active Value</p>
                <p class="text-2xl font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($totalMonthly, 0, ',', '.') }}</p>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
        <!-- Filters Bar -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" 
                       wire:model.live.debounce.250ms="search" 
                       placeholder="Cari nomor, paket, atau customer..." 
                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select wire:model.live="statusFilter" class="w-full sm:w-auto border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active">Active</option>
                    <option value="paused">Paused</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <!-- Subscriptions Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-50/80 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="py-3 px-4">No. Langganan</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Paket / Layanan</th>
                        <th class="py-3 px-4">Siklus</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4 text-right">Harga</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($subscriptions as $sub)
                        <tr class="hover:bg-gray-50/70 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-semibold text-blue-700 whitespace-nowrap">
                                {{ $sub->subscription_number ?: 'SUB-'.str_pad($sub->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-gray-900">{{ $sub->customer->name ?? '-' }}</div>
                                @if(!empty($sub->customer->company_name))
                                    <div class="text-xs text-gray-500">{{ $sub->customer->company_name }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-gray-800">
                                {{ $sub->plan_name ?: 'Custom Plan' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full border {{ $sub->cycle_badge_class }}">
                                    {{ ucfirst($sub->billing_cycle) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-gray-600 whitespace-nowrap">
                                {{ $sub->start_date ? \Carbon\Carbon::parse($sub->start_date)->format('d M Y') : '-' }}
                                <span class="text-gray-400 mx-1">→</span>
                                {{ $sub->end_date ? \Carbon\Carbon::parse($sub->end_date)->format('d M Y') : 'Ongoing' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($sub->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $sub->status_badge_class }}">
                                    {{ ucfirst($sub->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button" 
                                            wire:click="edit({{ $sub->id }})" 
                                            class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors cursor-pointer"
                                            title="Edit Subscription">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button type="button" 
                                            wire:click="confirmDelete({{ $sub->id }})" 
                                            class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors cursor-pointer"
                                            title="Delete Subscription">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-500">
                                <div class="max-w-xs mx-auto">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum ada data langganan</h3>
                                    <p class="mt-1 text-xs text-gray-500">Mulai dengan menambahkan paket langganan customer baru.</p>
                                    <button type="button" wire:click="create" class="mt-3 inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-md text-xs font-medium hover:bg-blue-700 cursor-pointer">
                                        + Tambah Langganan
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($subscriptions->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="store">
                        <div class="bg-white px-5 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
                                <h3 class="text-base font-bold text-gray-900" id="modal-title">
                                    {{ $isEditMode ? 'Edit Subscription' : 'Create New Subscription' }}
                                </h3>
                                <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-500 cursor-pointer text-xl font-bold">&times;</button>
                            </div>

                            <div class="space-y-4">
                                <!-- Subscription Number -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nomor Langganan</label>
                                    <input type="text" wire:model="subscription_number" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-gray-50 font-mono text-gray-700 focus:outline-none" readonly>
                                </div>

                                <!-- Customer -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Customer <span class="text-red-500">*</span></label>
                                    <select wire:model="customer_id" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">-- Pilih Customer --</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }} {{ $c->company_name ? "({$c->company_name})" : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Plan Name -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Nama Paket / Layanan <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="plan_name" placeholder="Contoh: Maintenance Lab Rutin Bulanan" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('plan_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Price & Billing Cycle -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Harga Langganan <span class="text-red-500">*</span></label>
                                        <div class="relative rounded-lg shadow-xs">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-gray-400">Rp</span>
                                            <input type="number" step="any" min="0" wire:model="price" class="w-full border border-gray-300 rounded-lg py-2 pl-9 pr-3 text-sm text-right font-mono font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Siklus Penagihan</label>
                                        <select wire:model.live="billing_cycle" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="monthly">Bulanan (Monthly)</option>
                                            <option value="quarterly">3 Bulanan (Quarterly)</option>
                                            <option value="yearly">Tahunan (Yearly)</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Start & End Date -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                        <input type="date" wire:model="start_date" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Tanggal Berakhir / Jatuh Tempo</label>
                                        <input type="date" wire:model="end_date" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Status Langganan</label>
                                    <select wire:model="status" class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="active">Active</option>
                                        <option value="paused">Paused</option>
                                        <option value="cancelled">Cancelled</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>

                                <!-- Notes -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Catatan Tambahan</label>
                                    <textarea wire:model="notes" rows="2" placeholder="Ketentuan khusus, jadwal tagihan invoice..." class="w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="bg-gray-50 px-5 py-3.5 sm:px-6 flex justify-end gap-2 border-t border-gray-200">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-xs cursor-pointer">
                                {{ $isEditMode ? 'Update Langganan' : 'Simpan Langganan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($confirmingDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('confirmingDeletion', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-base font-bold text-gray-900">Hapus Langganan?</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus data langganan ini? Tindakan ini tidak dapat dibatalkan.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-2">
                        <button type="button" wire:click="delete" class="w-full sm:w-auto px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow-xs cursor-pointer">
                            Hapus
                        </button>
                        <button type="button" wire:click="$set('confirmingDeletion', false)" class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
