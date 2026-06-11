<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Piutang Dagang (Accounts Receivable)</h1>
            <p class="text-sm text-gray-500 mt-1">Manage outstanding client invoices, record payments, and track aging receivables.</p>
        </div>
        <div>
            <button wire:click="recordReceipt" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Record Payment Receipt
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
            <button wire:click="$set('activeTab', 'receivables')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'receivables' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Outstanding Invoices
            </button>
            <button wire:click="$set('activeTab', 'receipts')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'receipts' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Payment Receipts History
            </button>
            <button wire:click="$set('activeTab', 'aging')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'aging' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                AR Aging Report
            </button>
        </nav>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search customer or invoice...">
        </div>
    </div>

    <!-- Content Tabs -->
    @if($activeTab === 'receivables')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Invoice No</th>
                            <th class="py-3.5 px-6">Customer</th>
                            <th class="py-3.5 px-6">Invoice Date</th>
                            <th class="py-3.5 px-6">Due Date</th>
                            <th class="py-3.5 px-6 text-right">Total Amount</th>
                            <th class="py-3.5 px-6 text-right">Paid</th>
                            <th class="py-3.5 px-6 text-right">Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($openInvoices as $invoice)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $invoice->invoice_number }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $invoice->customer->name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $invoice->sale_date }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $invoice->due_date ?: '-' }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($invoice->grand_total, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-600">Rp {{ number_format($invoice->paid_amount, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-600 font-bold">Rp {{ number_format($invoice->remaining_balance, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No outstanding invoices. All client debts are paid!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'receipts')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Receipt No</th>
                            <th class="py-3.5 px-6">Customer</th>
                            <th class="py-3.5 px-6">Invoice Reference</th>
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Method</th>
                            <th class="py-3.5 px-6 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($receipts as $receipt)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-gray-900 font-semibold">{{ $receipt->receipt_number }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-700">{{ $receipt->customer->name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $receipt->sale ? $receipt->sale->invoice_number : 'General Downpayment' }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $receipt->payment_date }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $receipt->payment_method }}</td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-600 font-bold">Rp {{ number_format($receipt->amount, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No payment receipts logged yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($receipts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $receipts->links() }}
                </div>
            @endif
        </div>

    @elseif($activeTab === 'aging')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Customer Name</th>
                            <th class="py-3.5 px-6 text-right">Current</th>
                            <th class="py-3.5 px-6 text-right">1 - 30 Days</th>
                            <th class="py-3.5 px-6 text-right">31 - 60 Days</th>
                            <th class="py-3.5 px-6 text-right">61 - 90 Days</th>
                            <th class="py-3.5 px-6 text-right">Over 90 Days</th>
                            <th class="py-3.5 px-6 text-right font-bold">Total Receivables</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($agingSummary as $customer => $buckets)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $customer }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-600">Rp {{ number_format($buckets['current'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-amber-600">Rp {{ number_format($buckets['1_30'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-amber-700">Rp {{ number_format($buckets['31_60'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-500">Rp {{ number_format($buckets['61_90'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-700 font-semibold">Rp {{ number_format($buckets['over_90'], 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900 font-bold">Rp {{ number_format($buckets['total'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No aging receivables data.</td>
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

                    @if($modalType === 'record_receipt')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Record Client Payment Receipt</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Receipt Number</label>
                                    <input type="text" wire:model="receipt_number" readonly class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-gray-50 font-mono">
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
                                    <label class="block text-sm font-medium text-gray-700">Customer</label>
                                    <select wire:model.live="customer_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                @if($customer_id)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Select Outstanding Sale Invoice</label>
                                        <select wire:model="sale_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- General Payment / Deposit --</option>
                                            @foreach($customerSales as $sale)
                                                <option value="{{ $sale->id }}">{{ $sale->invoice_number }} (Total: Rp {{ number_format($sale->grand_total, 0) }})</option>
                                            @endforeach
                                        </select>
                                        @error('sale_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Debit Account (Cash/Bank)</label>
                                        <select wire:model="debit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="">-- Select Debit Account --</option>
                                            @foreach($accounts as $acc)
                                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('debit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Credit Account (Receivable)</label>
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
                            <button type="button" wire:click="saveReceipt" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Post Receipt</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
