<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Hutang Dagang (Accounts Payable)</h1>
            <p class="text-sm text-gray-500 mt-1">Manage outstanding supplier bills, schedule payments, and record disbursements.</p>
        </div>
        <div class="flex space-x-2">
            <button wire:click="addSchedule" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Schedule Payment
            </button>
            <button wire:click="recordDisbursement" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Record Disbursement
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'payables')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'payables' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Outstanding Bills
            </button>
            <button wire:click="$set('activeTab', 'disbursements')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'disbursements' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Disbursements History
            </button>
            <button wire:click="$set('activeTab', 'schedules')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'schedules' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Payment Schedules
            </button>
            <button wire:click="$set('activeTab', 'aging')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'aging' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                AP Aging Report
            </button>
        </nav>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search supplier or bill number...">
        </div>
    </div>

    <!-- Active Tab Content -->
    @if($activeTab === 'payables')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Purchase Bill No</th>
                            <th class="py-3.5 px-6">Supplier</th>
                            <th class="py-3.5 px-6">Bill Date</th>
                            <th class="py-3.5 px-6">Due Date</th>
                            <th class="py-3.5 px-6 text-right">Total Amount</th>
                            <th class="py-3.5 px-6 text-right">Paid</th>
                            <th class="py-3.5 px-6 text-right">Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($openPayables as $bill)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $bill->purchase_number }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $bill->supplier->name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $bill->purchase_date }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $bill->due_date ?: '-' }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($bill->grand_total, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-600">Rp {{ number_format($bill->paid_amount, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-600 font-bold">Rp {{ number_format($bill->remaining_balance, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No outstanding bills. All accounts payable are cleared!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'disbursements')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Disbursement No</th>
                            <th class="py-3.5 px-6">Supplier</th>
                            <th class="py-3.5 px-6">Bill Reference</th>
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Method</th>
                            <th class="py-3.5 px-6 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($disbursements as $disb)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-gray-900 font-semibold">{{ $disb->disbursement_number }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-700">{{ $disb->supplier->name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $disb->purchase ? $disb->purchase->purchase_number : 'General Advance Payment' }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $disb->payment_date }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $disb->payment_method }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-600 font-bold">Rp {{ number_format($disb->amount, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No payment disbursements logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($disbursements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $disbursements->links() }}
                </div>
            @endif
        </div>

    @elseif($activeTab === 'schedules')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Supplier</th>
                            <th class="py-3.5 px-6">Bill Reference</th>
                            <th class="py-3.5 px-6">Scheduled Due Date</th>
                            <th class="py-3.5 px-6 text-right">Planned Amount</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($schedules as $sched)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $sched->supplier->name }}</td>
                                <td class="py-4 px-6 font-mono text-gray-500">{{ $sched->purchase->purchase_number }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $sched->due_date }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-gray-800">Rp {{ number_format($sched->planned_amount, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $sched->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}
                                    ">
                                        {{ ucfirst($sched->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">No payment schedules logged.</td>
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

    @elseif($activeTab === 'aging')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Supplier Name</th>
                            <th class="py-3.5 px-6 text-right">Current</th>
                            <th class="py-3.5 px-6 text-right">1 - 30 Days</th>
                            <th class="py-3.5 px-6 text-right">31 - 60 Days</th>
                            <th class="py-3.5 px-6 text-right">61 - 90 Days</th>
                            <th class="py-3.5 px-6 text-right">Over 90 Days</th>
                            <th class="py-3.5 px-6 text-right font-bold">Total Payables</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($agingSummary as $supplier => $buckets)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $supplier }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-600">Rp {{ number_format($buckets['current'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-amber-600">Rp {{ number_format($buckets['1_30'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-amber-700">Rp {{ number_format($buckets['31_60'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-500">Rp {{ number_format($buckets['61_90'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-700 font-semibold">Rp {{ number_format($buckets['over_90'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900 font-bold">Rp {{ number_format($buckets['total'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No aging payables data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if($modalType === 'record_disbursement')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Record Payment Disbursement to Supplier</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Disbursement Number</label>
                                    <input type="text" wire:model="disbursement_number" readonly class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-gray-50 font-mono">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" wire:model="payment_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('payment_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                                        <select wire:model="payment_method" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="Transfer">Bank Transfer</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Cheque">Cheque</option>
                                        </select>
                                        @error('payment_method') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                    <select wire:model.live="supplier_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                @if($supplier_id)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Select Outstanding Purchase Bill</label>
                                        <select wire:model="purchase_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- General Payment / Advance --</option>
                                            @foreach($supplierPurchases as $purchase)
                                                <option value="{{ $purchase->id }}">{{ $purchase->purchase_number }} (Total: Rp {{ number_format($purchase->grand_total, 0) }})</option>
                                            @endforeach
                                        </select>
                                        @error('purchase_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Debit Account (Payable)</label>
                                        <select wire:model="debit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- Select Debit Account --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('debit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Credit Account (Cash/Bank)</label>
                                        <select wire:model="credit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- Select Credit Account --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('credit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                    <input type="number" wire:model="amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes / Remarks</label>
                                    <input type="text" wire:model="notes" placeholder="e.g. Paid in full" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveDisbursement" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Post Disbursement</button>
                        </div>

                    @elseif($modalType === 'add_schedule')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Schedule a Supplier Payment</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Supplier</label>
                                    <select wire:model.live="schedule_supplier_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">-- Select Supplier --</option>
                                        @foreach($suppliers as $s)
                                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('schedule_supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                @if($schedule_supplier_id)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Purchase Bill</label>
                                        <select wire:model="schedule_purchase_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- Select Purchase --</option>
                                            @foreach($scheduleSupplierPurchases as $purchase)
                                                <option value="{{ $purchase->id }}">{{ $purchase->purchase_number }} (Total: Rp {{ number_format($purchase->grand_total, 0) }})</option>
                                            @endforeach
                                        </select>
                                        @error('schedule_purchase_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Scheduled Date</label>
                                        <input type="date" wire:model="schedule_due_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('schedule_due_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                        <input type="number" wire:model="schedule_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('schedule_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveSchedule" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Save Schedule</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
