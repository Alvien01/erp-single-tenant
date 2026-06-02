<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Roster Shift Scheduling</h1>
            <p class="text-sm text-gray-500 mt-1">Manage corporate work shifts, construct employee roster calendars, and track shift duration metrics.</p>
        </div>
        @if($activeTab === 'schedules')
            <button wire:click="createRoster" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Assign Roster
            </button>
        @else
            <button wire:click="createShift" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Shift
            </button>
        @endif
    </div>

    <!-- Toast / Feedback Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'schedules')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'schedules' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Roster Planning Calendar
            </button>
            <button wire:click="$set('activeTab', 'shifts')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'shifts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Shift Types Definition
            </button>
        </nav>
    </div>

    @if($activeTab === 'schedules')
        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search employee name...">
            </div>
        </div>

        <!-- Roster Calendar Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Employee Name</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Assigned Shift</th>
                            <th class="py-3.5 px-6 text-center">Timings</th>
                            <th class="py-3.5 px-6">Roster Date</th>
                            <th class="py-3.5 px-6">Notes / Roster Instruction</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($schedules as $sc)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $sc->employee->name }}</td>
                                <td class="py-4 px-6 text-gray-500 capitalize">{{ $sc->employee->position ?? 'Staff' }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $sc->shift->name }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center font-mono text-gray-700">{{ $sc->shift->start_time }} - {{ $sc->shift->end_time }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $sc->date }}</td>
                                <td class="py-4 px-6 text-gray-500 italic max-w-xs truncate">{{ $sc->notes ?: '-' }}</td>
                                <td class="py-4 px-6 text-center space-x-2 font-display font-medium text-xs">
                                    <button wire:click="editRoster({{ $sc->id }})" class="text-blue-600 hover:text-blue-900 cursor-pointer">Edit</button>
                                    <button wire:click="deleteRoster({{ $sc->id }})" wire:confirm="Are you sure you want to delete this roster?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400 italic">No assigned employee rosters found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($schedules->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Shift types list -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Shift Label</th>
                            <th class="py-3.5 px-6">Start Time</th>
                            <th class="py-3.5 px-6">End Time</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($shifts as $sh)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $sh->name }}</td>
                                <td class="py-4 px-6 font-mono text-gray-700">{{ $sh->start_time }}</td>
                                <td class="py-4 px-6 font-mono text-gray-700">{{ $sh->end_time }}</td>
                                <td class="py-4 px-6 text-center space-x-2 font-display font-medium text-xs">
                                    <button wire:click="editShift({{ $sh->id }})" class="text-blue-600 hover:text-blue-900 cursor-pointer">Edit</button>
                                    <button wire:click="deleteShift({{ $sh->id }})" wire:confirm="Are you sure you want to delete this shift?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 italic">No shift types defined yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($shifts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Shift Modal -->
    @if($isOpenShiftModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeShiftModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeShiftModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $isEditShiftMode ? 'Edit Shift Type' : 'Create Shift Type' }}
                        </h3>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Shift Name / Label</label>
                                <input type="text" wire:model="name" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="e.g. Morning Shift, Night Duty">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Start Time</label>
                                    <input type="text" wire:model="start_time" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="08:00">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">End Time</label>
                                    <input type="text" wire:model="end_time" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="17:00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeShiftModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="storeShift" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Save Shift
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Roster Modal -->
    @if($isOpenRosterModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeRosterModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeRosterModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $isEditRosterMode ? 'Edit Assigned Roster' : 'Assign Employee Roster' }}
                        </h3>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Employee / Operator</label>
                                <select wire:model="employee_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e->id }}">{{ $e->name }} - {{ ucfirst($e->position ?? 'Staff') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Shift Schedule</label>
                                <select wire:model="shift_id_roster" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="">Select Shift</option>
                                    @foreach($allShifts as $s)
                                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->start_time }} - {{ $s->end_time }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Roster Date</label>
                                <input type="date" wire:model="date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Special Roster Instruction</label>
                                <textarea wire:model="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="Any temporary duty changes, extra time rules..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeRosterModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="storeRoster" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Assign Roster
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
