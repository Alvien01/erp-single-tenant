<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Kas & Bank (Cash & Bank Management)</h1>
            <p class="text-sm text-gray-500 mt-1">Manage cash books, petty cash, bank accounts, transactions and transfers.</p>
        </div>
        <div class="flex space-x-2">
            @if($activeTab === 'accounts')
                <button wire:click="createBankAccount" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Bank/Cash Account
                </button>
            @elseif($activeTab === 'transactions')
                <button wire:click="createTransaction" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Record Cash Transaction
                </button>
            @elseif($activeTab === 'transfers')
                <button wire:click="createTransfer" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    Transfer Funds
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'accounts')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'accounts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Bank & Petty Cash Accounts
            </button>
            <button wire:click="$set('activeTab', 'transactions')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'transactions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Cash Book / Transactions
            </button>
            <button wire:click="$set('activeTab', 'transfers')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'transfers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Inter-Account Transfers
            </button>
        </nav>
    </div>

    <!-- Search / Filter -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search...">
        </div>
    </div>

    <!-- Active Tab Content -->
    @if($activeTab === 'accounts')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($bankAccounts as $bank)
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md transition duration-150 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                            {{ $bank->code }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 font-display mt-2">{{ $bank->bank_name }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Acc Name: {{ $bank->name }}</p>
                    <p class="text-xs font-mono text-gray-700 mt-1">Acc No: {{ $bank->account_number }}</p>

                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Current Balance</p>
                        <p class="text-2xl font-bold text-gray-900 font-mono mt-1">Rp {{ number_format($bank->balance, 2, ',', '.') }}</p>
                    </div>
                </div>
            @empty
                <div class="col-span-3 bg-white rounded-lg border border-gray-200 py-12 text-center text-gray-500">
                    No bank or petty cash accounts configured yet.
                </div>
            @endforelse
        </div>

    @elseif($activeTab === 'transactions')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Ref</th>
                            <th class="py-3.5 px-6">Bank Account</th>
                            <th class="py-3.5 px-6">Matched CoA Account</th>
                            <th class="py-3.5 px-6">Description</th>
                            <th class="py-3.5 px-6 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 text-gray-900">{{ $tx->date }}</td>
                                <td class="py-4 px-6 text-gray-500 font-mono">{{ $tx->reference }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-700">{{ $tx->bankAccount->bank_name }} ({{ $tx->bankAccount->name }})</td>
                                <td class="py-4 px-6 text-gray-600">{{ $tx->account->code }} - {{ $tx->account->name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $tx->description }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold {{ $tx->type === 'in' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $tx->type === 'in' ? '+' : '-' }}Rp {{ number_format($tx->amount, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No cash transactions logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    @elseif($activeTab === 'transfers')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Ref</th>
                            <th class="py-3.5 px-6">From Account (CoA)</th>
                            <th class="py-3.5 px-6">To Account (CoA)</th>
                            <th class="py-3.5 px-6">Description</th>
                            <th class="py-3.5 px-6 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($transfers as $tf)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 text-gray-900">{{ $tf->date }}</td>
                                <td class="py-4 px-6 text-gray-500 font-mono">{{ $tf->reference }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $tf->fromAccount->code }} - {{ $tf->fromAccount->name }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $tf->toAccount->code }} - {{ $tf->toAccount->name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $tf->description }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-blue-600">
                                    Rp {{ number_format($tf->amount, 2, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No transfers logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transfers->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Modals -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if($modalType === 'bank_account')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Configure Bank/Cash Account</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bank Code</label>
                                        <input type="text" wire:model="bank_code" placeholder="e.g. BCA01" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('bank_code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bank Name</label>
                                        <input type="text" wire:model="bank_name" placeholder="e.g. Bank BCA" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('bank_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Account Display Name</label>
                                    <input type="text" wire:model="account_name" placeholder="e.g. Petty Cash / Operational BCA" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('account_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Account Number</label>
                                    <input type="text" wire:model="account_number" placeholder="e.g. 80011928" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('account_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Initial Balance (Rp)</label>
                                    <input type="number" wire:model="initial_balance" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('initial_balance') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveBankAccount" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Save Account</button>
                        </div>

                    @elseif($modalType === 'transaction')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Record Cash Book Transaction</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" wire:model="transaction_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('transaction_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Transaction Type</label>
                                        <select wire:model="transaction_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="in">Incoming / In (Debit Bank/Cash)</option>
                                            <option value="out">Outgoing / Out (Credit Bank/Cash)</option>
                                        </select>
                                        @error('transaction_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                        <input type="number" wire:model="transaction_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('transaction_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                                        <input type="text" wire:model="transaction_reference" placeholder="Auto-generated if blank" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Cash/Bank Account</label>
                                    <select wire:model="transaction_bank_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">-- Select Bank/Cash Account --</option>
                                        @foreach($bankAccounts as $bAcc)
                                            <option value="{{ $bAcc->id }}">{{ $bAcc->bank_name }} - {{ $bAcc->name }} (Rp {{ number_format($bAcc->balance, 0) }})</option>
                                        @endforeach
                                    </select>
                                    @error('transaction_bank_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Opposite Match Account (CoA)</label>
                                    <select wire:model="transaction_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">-- Select opposite double-entry ledger account --</option>
                                        @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                        @endforeach
                                    </select>
                                    @error('transaction_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <input type="text" wire:model="transaction_description" placeholder="e.g. Office supplies / Client invoice repayment" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('transaction_description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveTransaction" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Post & Record</button>
                        </div>

                    @elseif($modalType === 'transfer')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Inter-Account Fund Transfer</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" wire:model="transfer_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('transfer_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                        <input type="number" wire:model="transfer_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('transfer_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">From Bank Account</label>
                                        <select wire:model="transfer_from_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- Select Source --</option>
                                            @foreach($bankAccounts as $bAcc)
                                                <option value="{{ $bAcc->id }}">{{ $bAcc->bank_name }} - {{ $bAcc->name }} (Rp {{ number_format($bAcc->balance, 0) }})</option>
                                            @endforeach
                                        </select>
                                        @error('transfer_from_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">To Bank Account</label>
                                        <select wire:model="transfer_to_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- Select Destination --</option>
                                            @foreach($bankAccounts as $bAcc)
                                                <option value="{{ $bAcc->id }}">{{ $bAcc->bank_name }} - {{ $bAcc->name }} (Rp {{ number_format($bAcc->balance, 0) }})</option>
                                            @endforeach
                                        </select>
                                        @error('transfer_to_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reference Number</label>
                                        <input type="text" wire:model="transfer_reference" placeholder="Auto-generated if blank" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <input type="text" wire:model="transfer_description" placeholder="e.g. Transfer Petty Cash, Mutual BCA transfer" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('transfer_description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveTransfer" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Post Transfer</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
