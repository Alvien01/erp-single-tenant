<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Warehouses</h1>
            <p class="text-sm text-gray-500 mt-1">Manage warehouse facilities and inventory locations.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Warehouse
        </button>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by name or location...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Warehouse Name</th>
                        <th class="py-3.5 px-6">Location</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($warehouses as $wh)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-medium text-gray-900">{{ $wh->warehouse_name }}</td>
                            <td class="py-4 px-6 text-gray-700">{{ $wh->address ?? '-' }}</td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <button wire:click="manageLocations({{ $wh->id }})" class="text-emerald-600 hover:text-emerald-900 font-medium cursor-pointer font-sans">Locations</button>
                                <button wire:click="edit({{ $wh->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer font-sans">Edit</button>
                                <button wire:click="delete({{ $wh->id }})" wire:confirm="Are you sure you want to delete this warehouse?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer font-sans">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center text-gray-500">
                                No warehouses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($warehouses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $warehouses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Edit Warehouse' : 'Add New Warehouse' }}
                        </h3>
                        
                        <div class="mt-4 space-y-4 font-sans">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Warehouse Name</label>
                                <input type="text" wire:model="warehouse_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('warehouse_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Location/Address</label>
                                <input type="text" wire:model="location" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('location') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                            Save Warehouse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Manage Sub-Locations Modal -->
    @if($selectedWarehouseForLocations)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeLocations"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeLocations" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            Sub-Locations (Racks / Shelves) - {{ $selectedWarehouseForLocations->warehouse_name }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Manage physical sections or zones inside this warehouse facility.</p>
                        
                        <!-- Add New Location Form inline -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-150">
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Add Sub-Location</h4>
                            @if (session()->has('success_loc'))
                                <div class="p-2 mb-3 text-xs text-emerald-800 rounded bg-emerald-100 border border-emerald-200">
                                    {{ session('success_loc') }}
                                </div>
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Location Code</label>
                                    <input type="text" wire:model="new_loc_code" class="mt-1 block w-full border border-gray-300 rounded py-1.5 px-2 text-sm font-mono bg-gray-100" readonly>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Location Name / Rack Name</label>
                                    <input type="text" wire:model="new_loc_name" placeholder="e.g. Rack A-1, Shelf 3" class="mt-1 block w-full border border-gray-300 rounded py-1.5 px-2 text-sm">
                                    @error('new_loc_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <button type="button" wire:click="addLocation" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-1.5 px-4 rounded text-sm transition cursor-pointer">
                                        Add Location
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Current Locations List -->
                        <div class="mt-6">
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Current Sub-Locations</h4>
                            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                            <th class="py-2.5 px-4">Code</th>
                                            <th class="py-2.5 px-4">Name / Description</th>
                                            <th class="py-2.5 px-4 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($selectedWarehouseForLocations->locations as $loc)
                                            <tr>
                                                <td class="py-2.5 px-4 font-mono text-blue-600 font-semibold">{{ $loc->location_code }}</td>
                                                <td class="py-2.5 px-4 text-gray-800">{{ $loc->location_name }}</td>
                                                <td class="py-2.5 px-4 text-center">
                                                    <button type="button" wire:click="deleteLocation({{ $loc->id }})" wire:confirm="Delete this sub-location?" class="text-xs text-red-600 hover:text-red-900 font-medium font-sans cursor-pointer">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="py-6 text-center text-gray-500 italic">No sub-locations configured. Add one above.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" wire:click="closeLocations" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
