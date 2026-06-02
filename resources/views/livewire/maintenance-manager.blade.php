<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Equipment & Asset Maintenance</h1>
            <p class="text-sm text-gray-500 mt-1">Track maintenance orders, log repair activities, and scrap equipment.</p>
        </div>
        <div>
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                New Maintenance Request
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Requests</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">{{ $stats['active_repairs'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Repaired & Returned</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">{{ $stats['repaired'] }}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Scrapped Items</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">{{ $stats['scrap'] }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Maintenance Cost</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">Rp {{ number_format($stats['total_cost'], 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="relative w-full md:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search maintenance logs..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6 font-semibold">Asset Name</th>
                        <th class="py-3.5 px-6 font-semibold">Request Date</th>
                        <th class="py-3.5 px-6 font-semibold">Repair Date</th>
                        <th class="py-3.5 px-6 font-semibold">Priority</th>
                        <th class="py-3.5 px-6 font-semibold">Cost</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900">{{ $req->asset_name }}</div>
                                <div class="text-xs text-gray-500 max-w-xs truncate">{{ $req->description }}</div>
                            </td>
                            <td class="py-4 px-6 text-gray-600">{{ $req->request_date->format('d M Y') }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $req->repair_date ? $req->repair_date->format('d M Y') : '-' }}</td>
                            <td class="py-4 px-6">
                                @php
                                    $prioColor = match($req->priority) {
                                        'low' => 'bg-gray-50 text-gray-700 border-gray-200',
                                        'medium' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'high' => 'bg-red-50 text-red-700 border-red-100',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full border {{ $prioColor }}">
                                    {{ ucfirst($req->priority) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-mono font-bold text-gray-900">
                                Rp {{ number_format($req->cost, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $badgeColor = match($req->status) {
                                        'requested' => 'bg-gray-50 text-gray-600 border-gray-100',
                                        'in_progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'repaired' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                        'scrap' => 'bg-red-50 text-red-700 border-red-100',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 inline-flex items-center text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                <button wire:click="openModal({{ $req->id }})" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                <button onclick="confirm('Are you sure you want to delete this maintenance request?') || event.stopImmediatePropagation()" wire:click="delete({{ $req->id }})" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">No maintenance requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form wire:submit.prevent="save" class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $isEdit ? 'Edit Maintenance Request' : 'New Maintenance Request' }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Link to Asset (Optional)</label>
                                <select wire:model="asset_id" wire:change="onAssetChange($event.target.value)" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    <option value="">No linked asset</option>
                                    @foreach($assets as $ast)
                                        <option value="{{ $ast->id }}">{{ $ast->asset_code }} - {{ $ast->asset_name }}</option>
                                    @endforeach
                                </select>
                                @error('asset_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Asset Display Name</label>
                                <input wire:model="asset_name" type="text" placeholder="e.g. Forklift A, Server Rack 2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                @error('asset_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Problem Description</label>
                                <textarea wire:model="description" rows="3" placeholder="Describe the failure or request..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required></textarea>
                                @error('description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Request Date</label>
                                <input wire:model="request_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                @error('request_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Repair Date (Finished)</label>
                                <input wire:model="repair_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                @error('repair_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Maintenance Cost (Rp)</label>
                                <input wire:model="cost" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                @error('cost') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Priority</label>
                                <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                                @error('priority') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                                <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    <option value="requested">Requested</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="repaired">Repaired</option>
                                    <option value="scrap">Scrapped / Salvaged</option>
                                </select>
                                @error('status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
