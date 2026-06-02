<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900 tracking-tight">CRM Pipeline</h1>
            <p class="text-sm text-gray-500 mt-1">Manage leads, track opportunities, and convert them to quotations.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="create" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Lead
            </button>
        </div>
    </div>

    <!-- Alert / Toast -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 text-sm rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md">
            <label for="search" class="sr-only">Search Leads</label>
            <div class="relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" id="search" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search by opportunity, contact, or company...">
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 items-start">
        @foreach($stages as $stage)
            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                <!-- Column Header -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full @if($stage === 'new') bg-blue-500 @elseif($stage === 'qualified') bg-purple-500 @elseif($stage === 'proposition') bg-amber-500 @elseif($stage === 'won') bg-emerald-500 @else bg-red-500 @endif"></span>
                        <h3 class="font-bold text-gray-800 uppercase tracking-wider text-xs">{{ $stage }}</h3>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                        {{ $leads->where('status', $stage)->count() }}
                    </span>
                </div>

                <!-- Total Revenue for stage -->
                <div class="text-xs text-gray-500 mb-4 border-b border-gray-200 pb-2">
                    Est: <span class="font-bold text-gray-700">Rp {{ number_format($leads->where('status', $stage)->sum('expected_revenue'), 0, ',', '.') }}</span>
                </div>

                <!-- Card List -->
                <div class="space-y-3 min-h-[300px]">
                    @forelse($leads->where('status', $stage) as $lead)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200 relative group">
                            <!-- Card Header -->
                            <div class="font-semibold text-gray-900 text-sm mb-1 leading-snug">
                                {{ $lead->title }}
                            </div>

                            <!-- Customer info -->
                            <div class="text-xs text-gray-500 mb-2">
                                {{ $lead->contact_name }} @if($lead->company_name) <span class="text-gray-400">|</span> {{ $lead->company_name }} @endif
                            </div>

                            <!-- Values & Probability -->
                            <div class="flex justify-between items-center mt-3 pt-2 border-t border-gray-100">
                                <span class="text-xs font-semibold text-blue-600 font-mono">
                                    Rp {{ number_format($lead->expected_revenue, 0, ',', '.') }}
                                </span>
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xxs font-medium bg-blue-50 text-blue-700">
                                    {{ $lead->probability }}%
                                </span>
                            </div>

                            <!-- Actions inside card -->
                            <div class="mt-4 flex items-center justify-between gap-2 border-t border-gray-50 pt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                <div class="flex gap-1">
                                    <button wire:click="edit({{ $lead->id }})" class="p-1 text-gray-400 hover:text-blue-600 rounded hover:bg-gray-50" title="Edit">
                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $lead->id }})" onclick="confirm('Are you sure you want to delete this lead?') || event.stopImmediatePropagation()" class="p-1 text-gray-400 hover:text-red-600 rounded hover:bg-gray-50" title="Delete">
                                        <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Move Stage buttons -->
                                <div class="flex gap-1">
                                    @if($stage !== 'new')
                                        <button wire:click="updateStage({{ $lead->id }}, '{{ $stages[array_search($stage, $stages) - 1] }}')" class="p-0.5 text-gray-400 hover:text-gray-700 bg-gray-50 rounded" title="Move Left">
                                            ◀
                                        </button>
                                    @endif
                                    @if($stage !== 'won' && $stage !== 'lost')
                                        <button wire:click="updateStage({{ $lead->id }}, '{{ $stages[array_search($stage, $stages) + 1] }}')" class="p-0.5 text-gray-400 hover:text-gray-700 bg-gray-50 rounded" title="Move Right">
                                            ▶
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <!-- Convert to SQ button for Proposition/Won stages -->
                            @if($stage === 'proposition')
                                <div class="mt-2">
                                    <button wire:click="convertToQuotation({{ $lead->id }})" class="w-full text-center px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold transition-colors duration-150">
                                        Convert to SQ
                                    </button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-gray-400 italic bg-white rounded border border-dashed border-gray-200">
                            Empty
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create/Edit Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                            {{ $isEditMode ? 'Edit Lead/Opportunity' : 'Add New Lead/Opportunity' }}
                        </h3>
                        <form wire:submit.prevent="store" class="mt-4 space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Opportunity Name</label>
                                <input wire:model="title" type="text" id="title" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="e.g. 50 Units Office Chairs Purchase">
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Person</label>
                                    <input wire:model="contact_name" type="text" id="contact_name" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('contact_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                                    <input wire:model="company_name" type="text" id="company_name" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('company_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input wire:model="email" type="email" id="email" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input wire:model="phone" type="text" id="phone" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expected_revenue" class="block text-sm font-medium text-gray-700">Expected Revenue (Rp)</label>
                                    <input wire:model="expected_revenue" type="number" id="expected_revenue" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('expected_revenue') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="probability" class="block text-sm font-medium text-gray-700">Probability (%)</label>
                                    <input wire:model="probability" type="number" id="probability" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    @error('probability') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Stage</label>
                                    <select wire:model="status" id="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="new">New</option>
                                        <option value="qualified">Qualified</option>
                                        <option value="proposition">Proposition</option>
                                        <option value="won">Won</option>
                                        <option value="lost">Lost</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="user_id" class="block text-sm font-medium text-gray-700">Salesperson</label>
                                    <select wire:model="user_id" id="user_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Select salesperson...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Internal Notes / Requirements</label>
                                <textarea wire:model="notes" id="notes" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border border-gray-300 rounded-md"></textarea>
                                @error('notes') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:flow-row-dense">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                                    Save
                                </button>
                                <button type="button" wire:click="$set('isOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
