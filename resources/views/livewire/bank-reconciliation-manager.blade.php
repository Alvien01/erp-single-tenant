<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Bank Reconciliation</h1>
            <p class="text-sm text-gray-500 mt-1">Match bank statement transactions with internal ledger accounts.</p>
        </div>
        <button wire:click="createStatement" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Bank Transaction
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
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search mutations by description or reference...">
        </div>
    </div>

    <!-- Mutations List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6">Description</th>
                        <th class="py-3.5 px-6">Reference</th>
                        <th class="py-3.5 px-6 text-right">Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($statements as $stmt)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $stmt->date->format('Y-m-d') }}</td>
                            <td class="py-4 px-6 font-semibold text-gray-700">{{ $stmt->description }}</td>
                            <td class="py-4 px-6 text-gray-500">{{ $stmt->reference ?: '-' }}</td>
                            <td class="py-4 px-6 text-right font-mono font-bold {{ $stmt->amount >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $stmt->amount >= 0 ? '+' : '' }}Rp {{ number_format($stmt->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $stmt->is_reconciled ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}
                                ">
                                    {{ $stmt->is_reconciled ? 'Reconciled' : 'Unreconciled' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                @if(!$stmt->is_reconciled)
                                    <button wire:click="startReconciliation({{ $stmt->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer font-bold">Reconcile</button>
                                @else
                                    <span class="text-gray-400">Matched</span>
                                @endif
                                <button wire:click="deleteStatement({{ $stmt->id }})" wire:confirm="Delete statement line?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer ml-2">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                No bank transactions logged.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($statements->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $statements->links() }}
            </div>
        @endif
    </div>

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

                    @if($modalType === 'statement')
                        <!-- Add Statement Line -->
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Add Bank Transaction Line</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" wire:model="date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <input type="text" wire:model="description" placeholder="e.g. Wire transfer from Client A" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                        <input type="number" wire:model="amount" placeholder="Use negative for outgoings" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reference</label>
                                        <input type="text" wire:model="reference" placeholder="e.g. TX-908123" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveStatement" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Save Transaction
                            </button>
                        </div>

                    @elseif($modalType === 'reconcile')
                        <!-- Reconcile Mutation -->
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Reconcile Bank Transaction</h3>
                            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-md text-xs space-y-1">
                                <p class="font-semibold text-gray-700">Mutation Details:</p>
                                <p><span class="text-gray-500">Date:</span> {{ $selectedStatement->date->format('Y-m-d') }}</p>
                                <p><span class="text-gray-500">Description:</span> {{ $selectedStatement->description }}</p>
                                <p><span class="text-gray-500">Amount:</span> <span class="font-semibold {{ $selectedStatement->amount >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($selectedStatement->amount, 0, ',', '.') }}</span></p>
                            </div>

                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Match against Account Ledger (CoA)</label>
                                    <select wire:model="match_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </select>
                                    @error('match_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="processReconciliation" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 focus:outline-none font-display font-semibold">
                                Reconcile & Post Journal
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
