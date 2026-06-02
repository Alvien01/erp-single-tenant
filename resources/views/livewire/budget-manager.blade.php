<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Budget Management</h1>
            <p class="text-sm text-gray-500 mt-1">Plan department budgets, configure budget positions, and monitor actual vs planned spending.</p>
        </div>
        <button wire:click="createBudget" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Budget Plan
        </button>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Search / Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search budget plans by name...">
        </div>
    </div>

    <!-- Budgets List -->
    @forelse($budgets as $b)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans p-6 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between border-b border-gray-100 pb-4">
                <div>
                    <h3 class="text-lg font-bold font-display text-gray-900">{{ $b->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Department: <span class="font-semibold text-gray-700">{{ $b->department ? $b->department->name : 'Global / Company-wide' }}</span> | 
                        Period: <span class="font-semibold text-gray-700">{{ $b->start_date->format('Y-m-d') }} to {{ $b->end_date->format('Y-m-d') }}</span>
                    </p>
                </div>
                <div class="flex items-center space-x-2 mt-3 md:mt-0">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                        {{ $b->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $b->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                        {{ $b->status === 'closed' ? 'bg-red-100 text-red-800' : '' }}
                    ">
                        {{ ucfirst($b->status) }}
                    </span>
                    @if($b->status === 'draft')
                        <button wire:click="approveBudget({{ $b->id }})" class="text-xs font-semibold px-3 py-1.5 bg-emerald-600 text-white rounded hover:bg-emerald-700 cursor-pointer">Approve Plan</button>
                        <button wire:click="addBudgetLine({{ $b->id }})" class="text-xs font-semibold px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 cursor-pointer">+ Add Position</button>
                    @endif
                    <button wire:click="editBudget({{ $b->id }})" class="text-xs text-gray-500 hover:text-blue-600 font-semibold px-2 py-1 cursor-pointer">Edit</button>
                    <button wire:click="deleteBudget({{ $b->id }})" wire:confirm="Delete this budget plan?" class="text-xs text-red-500 hover:text-red-700 font-semibold px-2 py-1 cursor-pointer">Delete</button>
                </div>
            </div>

            <!-- Budget Lines Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-xs">
                    <thead>
                        <tr class="text-left font-semibold text-gray-500">
                            <th class="py-2 px-3">Account Ledger</th>
                            <th class="py-2 px-3 text-right">Planned Budget</th>
                            <th class="py-2 px-3 text-right">Actual Spent</th>
                            <th class="py-2 px-3 text-right">Remaining Balance</th>
                            <th class="py-2 px-3 text-center">Usage Rate</th>
                            @if($b->status === 'draft')
                                <th class="py-2 px-3 text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($b->lines as $line)
                            @php
                                $remaining = $line->planned_amount - $line->actual_amount;
                                $percentage = $line->planned_amount > 0 ? ($line->actual_amount / $line->planned_amount) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-3 font-semibold text-gray-700">{{ $line->account->code }} - {{ $line->account->name }}</td>
                                <td class="py-3 px-3 text-right font-mono font-medium text-gray-900">Rp {{ number_format($line->planned_amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-medium text-red-600">Rp {{ number_format($line->actual_amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-medium {{ $remaining >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format($remaining, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <div class="w-24 bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $percentage) }}%"></div>
                                        </div>
                                        <span class="font-mono font-bold text-gray-700">{{ number_format($percentage, 1) }}%</span>
                                    </div>
                                </td>
                                @if($b->status === 'draft')
                                    <td class="py-3 px-3 text-center">
                                        <button wire:click="deleteBudgetLine({{ $line->id }})" class="text-rose-600 hover:text-rose-900 cursor-pointer">Remove</button>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-400">No budget positions defined yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center text-gray-500 font-sans">
            No budget plans found. Create one to start tracking expenditures!
        </div>
    @endforelse

    <!-- Modals Section -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if($modalType === 'budget')
                        <!-- Budget Modal -->
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                                {{ $budget_id ? 'Edit Budget Plan' : 'Create New Budget Plan' }}
                            </h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                                    <input type="text" wire:model="name" placeholder="e.g. Q3 Sales & Marketing Budget" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Department</label>
                                    <select wire:model="department_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">Global / Company-wide</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" wire:model="start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                                        <input type="date" wire:model="end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveBudget" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Save Plan
                            </button>
                        </div>

                    @elseif($modalType === 'budget_line')
                        <!-- Budget Line Modal -->
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Add Budget Position</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Account Ledger (CoA)</label>
                                    <select wire:model="account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </select>
                                    @error('account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Planned Budget Amount (Rp)</label>
                                    <input type="number" wire:model="planned_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('planned_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveBudgetLine" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Save Position
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
