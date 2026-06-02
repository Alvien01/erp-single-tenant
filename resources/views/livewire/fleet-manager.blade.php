<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Fleet & Vehicle Management</h1>
            <p class="text-sm text-gray-500 mt-1">Track vehicles, fuel usage, and maintenance logs dynamically.</p>
        </div>
        <div class="flex space-x-2">
            @if($activeTab === 'vehicles')
                <button wire:click="openModal('vehicle')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                    Add Vehicle
                </button>
            @elseif($activeTab === 'services')
                <button wire:click="openModal('service')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                    Add Service Record
                </button>
            @elseif($activeTab === 'fuel_logs')
                <button wire:click="openModal('fuel')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                    Log Fuel Purchase
                </button>
            @endif
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

    <!-- Tabs & Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Tabs -->
            <div class="flex space-x-2 border-b border-gray-100">
                <button wire:click="$set('activeTab', 'vehicles')" class="pb-3 px-4 font-semibold text-sm transition {{ $activeTab === 'vehicles' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Vehicles
                </button>
                <button wire:click="$set('activeTab', 'services')" class="pb-3 px-4 font-semibold text-sm transition {{ $activeTab === 'services' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Services
                </button>
                <button wire:click="$set('activeTab', 'fuel_logs')" class="pb-3 px-4 font-semibold text-sm transition {{ $activeTab === 'fuel_logs' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Fuel Logs
                </button>
            </div>

            <!-- Search input -->
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search logs..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>
        </div>
    </div>

    <!-- Active Tab Display -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($activeTab === 'vehicles')
            <!-- Vehicles Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 font-semibold">License Plate</th>
                            <th class="py-3.5 px-6 font-semibold">Model</th>
                            <th class="py-3.5 px-6 font-semibold">Assigned Driver</th>
                            <th class="py-3.5 px-6 font-semibold">Odometer</th>
                            <th class="py-3.5 px-6 font-semibold">Acquisition Date</th>
                            <th class="py-3.5 px-6 font-semibold text-center">Status</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($vehiclesList as $vehicle)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ $vehicle->license_plate }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $vehicle->model }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $vehicle->driver ? $vehicle->driver->name : 'Unassigned' }}</td>
                                <td class="py-4 px-6 text-gray-600 font-mono">{{ number_format($vehicle->odometer, 0) }} km</td>
                                <td class="py-4 px-6 text-gray-600">{{ $vehicle->acquisition_date ? $vehicle->acquisition_date->format('d M Y') : '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $badgeColor = match($vehicle->status) {
                                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'in_service' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'out_of_service' => 'bg-red-50 text-red-700 border-red-100',
                                            'sold' => 'bg-gray-50 text-gray-600 border-gray-100',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 inline-flex items-center text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                        {{ str_replace('_', ' ', ucfirst($vehicle->status)) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button wire:click="openModal('vehicle', {{ $vehicle->id }})" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                    <button onclick="confirm('Are you sure you want to delete this vehicle?') || event.stopImmediatePropagation()" wire:click="deleteVehicle({{ $vehicle->id }})" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">No vehicles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $vehiclesList->links() }}
            </div>

        @elseif($activeTab === 'services')
            <!-- Services Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 font-semibold">Vehicle</th>
                            <th class="py-3.5 px-6 font-semibold">Service Date</th>
                            <th class="py-3.5 px-6 font-semibold">Description</th>
                            <th class="py-3.5 px-6 font-semibold">Provider</th>
                            <th class="py-3.5 px-6 font-semibold">Cost</th>
                            <th class="py-3.5 px-6 font-semibold text-center">Status</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($servicesList as $service)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ $service->fleet->license_plate }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $service->service_date->format('d M Y') }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $service->description }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $service->provider ?: '-' }}</td>
                                <td class="py-4 px-6 text-gray-900 font-mono font-bold">Rp {{ number_format($service->cost, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $badgeColor = match($service->status) {
                                            'planned' => 'bg-gray-50 text-gray-600 border-gray-100',
                                            'in_progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'canceled' => 'bg-red-50 text-red-700 border-red-100',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 inline-flex items-center text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                        {{ ucfirst($service->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button wire:click="openModal('service', {{ $service->id }})" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                    <button onclick="confirm('Are you sure you want to delete this service record?') || event.stopImmediatePropagation()" wire:click="deleteService({{ $service->id }})" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">No service logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $servicesList->links() }}
            </div>

        @elseif($activeTab === 'fuel_logs')
            <!-- Fuel Logs Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 font-semibold">Vehicle</th>
                            <th class="py-3.5 px-6 font-semibold">Date</th>
                            <th class="py-3.5 px-6 font-semibold">Volume (Liters)</th>
                            <th class="py-3.5 px-6 font-semibold">Cost per Liter</th>
                            <th class="py-3.5 px-6 font-semibold">Total Cost</th>
                            <th class="py-3.5 px-6 font-semibold font-mono">Odometer</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($fuelLogsList as $fuel)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ $fuel->fleet->license_plate }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $fuel->date->format('d M Y') }}</td>
                                <td class="py-4 px-6 text-gray-700 font-mono">{{ number_format($fuel->liters, 2) }} L</td>
                                <td class="py-4 px-6 text-gray-600 font-mono">Rp {{ number_format($fuel->cost_per_liter, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-gray-900 font-mono font-bold">Rp {{ number_format($fuel->total_cost, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-gray-600 font-mono">{{ $fuel->odometer ? number_format($fuel->odometer, 0).' km' : '-' }}</td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button wire:click="openModal('fuel', {{ $fuel->id }})" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                    <button onclick="confirm('Are you sure you want to delete this fuel record?') || event.stopImmediatePropagation()" wire:click="deleteFuel({{ $fuel->id }})" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">No fuel logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $fuelLogsList->links() }}
            </div>
        @endif
    </div>

    <!-- Modals -->
    @if($modalType)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    @if($modalType === 'vehicle')
                        <!-- Vehicle Form Modal -->
                        <form wire:submit.prevent="saveVehicle" class="space-y-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">{{ $isEdit ? 'Edit Vehicle Details' : 'Add New Vehicle' }}</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">License Plate</label>
                                    <input wire:model="license_plate" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm font-mono uppercase" required>
                                    @error('license_plate') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Model / Description</label>
                                    <input wire:model="model" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" placeholder="e.g. Toyota Innova 2023" required>
                                    @error('model') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Driver Assignment</label>
                                    <select wire:model="driver_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                        <option value="">Unassigned</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('driver_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Acquisition Date</label>
                                    <input wire:model="acquisition_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    @error('acquisition_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Odometer Initial (km)</label>
                                    <input wire:model="odometer" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    @error('odometer') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                        <option value="active">Active</option>
                                        <option value="in_service">In Service</option>
                                        <option value="out_of_service">Out of Service</option>
                                        <option value="sold">Sold</option>
                                    </select>
                                    @error('status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Save</button>
                            </div>
                        </form>

                    @elseif($modalType === 'service')
                        <!-- Service Form Modal -->
                        <form wire:submit.prevent="saveService" class="space-y-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Service Record</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Vehicle</label>
                                    <select wire:model="service_fleet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($fleets as $fl)
                                            <option value="{{ $fl->id }}">{{ $fl->license_plate }} - {{ $fl->model }}</option>
                                        @endforeach
                                    </select>
                                    @error('service_fleet_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Service Date</label>
                                    <input wire:model="service_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('service_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Service Description</label>
                                    <input wire:model="service_description" type="text" placeholder="e.g. Engine oil replace, brake pads change" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('service_description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Service Provider / Workshop</label>
                                    <input wire:model="service_provider" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    @error('service_provider') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Total Cost (Rp)</label>
                                    <input wire:model="service_cost" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('service_cost') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                                    <select wire:model="service_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                        <option value="planned">Planned</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="completed">Completed</option>
                                        <option value="canceled">Canceled</option>
                                    </select>
                                    @error('service_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Save</button>
                            </div>
                        </form>

                    @elseif($modalType === 'fuel')
                        <!-- Fuel Log Form Modal -->
                        <form wire:submit.prevent="saveFuel" class="space-y-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Fuel Purchase Log</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Vehicle</label>
                                    <select wire:model="fuel_fleet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                        <option value="">Select Vehicle</option>
                                        @foreach($fleets as $fl)
                                            <option value="{{ $fl->id }}">{{ $fl->license_plate }} - {{ $fl->model }}</option>
                                        @endforeach
                                    </select>
                                    @error('fuel_fleet_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Purchase Date</label>
                                    <input wire:model="fuel_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('fuel_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Volume (Liters)</label>
                                    <input wire:model.live="fuel_liters" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('fuel_liters') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Cost per Liter (Rp)</label>
                                    <input wire:model.live="fuel_cost_per_liter" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('fuel_cost_per_liter') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Odometer Reading (km)</label>
                                    <input wire:model="fuel_odometer" type="number" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    @error('fuel_odometer') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Calculated Total Cost</label>
                                    <div class="mt-2 text-sm font-bold text-gray-900 font-mono">
                                        Rp {{ number_format(floatval($fuel_liters) * floatval($fuel_cost_per_liter), 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
