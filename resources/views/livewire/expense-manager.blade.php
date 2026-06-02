<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Employee Expenses Claim</h1>
            <p class="text-sm text-gray-500 mt-1">Manage, approve, and reimburse employee business expenses claims.</p>
        </div>
        <button wire:click="createExpense" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Claim
        </button>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Search / Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by employee name or category...">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Employee</th>
                        <th class="py-3.5 px-6">Category</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $exp->date->format('Y-m-d') }}</td>
                            <td class="py-4 px-6 font-semibold text-gray-700">{{ $exp->employee->name }}</td>
                            <td class="py-4 px-6 font-medium text-blue-600">{{ ucfirst($exp->category) }}</td>
                            <td class="py-4 px-6 text-gray-500">{{ $exp->description ?: '-' }}</td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">Rp {{ number_format($exp->amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $exp->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $exp->status === 'submitted' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $exp->status === 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $exp->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $exp->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                ">
                                    {{ ucfirst($exp->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                @if($exp->status === 'draft')
                                    <button wire:click="submitExpense({{ $exp->id }})" class="text-amber-600 hover:text-amber-900 font-medium cursor-pointer">Submit</button>
                                    <button wire:click="editExpense({{ $exp->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="deleteExpense({{ $exp->id }})" wire:confirm="Delete this expense claim?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                @elseif($exp->status === 'submitted')
                                    <button wire:click="approveExpense({{ $exp->id }})" class="text-emerald-600 hover:text-emerald-900 font-medium cursor-pointer">Approve</button>
                                    <button wire:click="rejectExpense({{ $exp->id }})" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Reject</button>
                                @elseif($exp->status === 'approved')
                                    <button wire:click="payExpense({{ $exp->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer font-bold">Reimburse / Pay</button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                No expense claims found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($expenses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
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

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $expense_id ? 'Edit Claim' : 'Create New Expense Claim' }}
                        </h3>
                        <div class="mt-4 space-y-4 font-sans">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Karyawan (Employee)</label>
                                <select wire:model="employee_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $e)
                                        <option value="{{ $e->id }}">{{ $e->name }}</option>
                                    @endforeach
                                </select>
                                @error('employee_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" wire:model="date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <select wire:model="category" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="travel">Travel</option>
                                        <option value="meals">Meals</option>
                                        <option value="accommodation">Accommodation</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('category') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                <input type="number" wire:model="amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea wire:model="description" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" rows="3" placeholder="Explain expense details..."></textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="saveExpense" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                            Save Claim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
