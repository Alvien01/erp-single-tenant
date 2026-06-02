<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Credit & Debit Notes</h1>
            <p class="text-sm text-gray-500 mt-1">Issue Customer Credit Notes (refunds & adjustments) and Vendor Debit Notes (purchase returns & allowances).</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Note
        </button>
    </div>

    <!-- Toast / Feedback Messages -->
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
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search Note number...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Note Number</th>
                        <th class="py-3.5 px-6">Type</th>
                        <th class="py-3.5 px-6">Partner</th>
                        <th class="py-3.5 px-6">Date</th>
                        <th class="py-3.5 px-6 text-right">Grand Total</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($invoices as $inv)
                        @php
                            $statusColors = match($inv->status) {
                                'draft' => 'bg-gray-100 text-gray-700',
                                'posted' => 'bg-blue-100 text-blue-800',
                                'paid' => 'bg-emerald-100 text-emerald-800',
                                'void' => 'bg-red-100 text-red-800',
                            };
                            $typeColors = match($inv->type) {
                                'credit_note' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'debit_note' => 'bg-rose-50 text-rose-700 border-rose-200',
                            };
                            $partnerName = $inv->type === 'credit_note' ? ($inv->customer->name ?? '-') : ($inv->supplier->name ?? '-');
                        @endphp
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-bold text-gray-900">{{ $inv->invoice_number }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $typeColors }}">
                                    {{ $inv->type === 'credit_note' ? 'Credit Note' : 'Debit Note' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-gray-900">{{ $partnerName }}</td>
                            <td class="py-4 px-6 text-gray-500">{{ $inv->date }}</td>
                            <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors }}">
                                    {{ ucfirst($inv->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2 font-display font-medium text-xs">
                                @if($inv->status === 'draft')
                                    <button wire:click="postInvoice({{ $inv->id }})" class="text-blue-600 hover:text-blue-900 cursor-pointer">Post Note</button>
                                @endif
                                <button wire:click="edit({{ $inv->id }})" class="text-gray-600 hover:text-gray-900 cursor-pointer">View / Edit</button>
                                <button wire:click="delete({{ $inv->id }})" wire:confirm="Are you sure you want to delete this Note?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                No notes found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Edit Adjustment Note' : 'Create Adjustment Note' }}
                        </h3>
                        
                        <div class="mt-4 space-y-4 text-sm">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Type Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Adjustment Type</label>
                                    <select wire:model.live="type" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" {{ $isEditMode ? 'disabled' : '' }}>
                                        <option value="credit_note">Credit Note (Cust)</option>
                                        <option value="debit_note">Debit Note (Vend)</option>
                                    </select>
                                    @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Reference Invoice/PO Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Original Reference</label>
                                    <select wire:model.live="original_invoice_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" {{ $isEditMode ? 'disabled' : '' }}>
                                        <option value="">Select Reference</option>
                                        @if($type === 'credit_note')
                                            @foreach($sales as $s)
                                                <option value="{{ $s->id }}">{{ $s->invoice_number }} - {{ $s->customer->name }}</option>
                                            @endforeach
                                        @else
                                            @foreach($purchases as $p)
                                                <option value="{{ $p->id }}">{{ $p->purchase_number }} - {{ $p->supplier->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('original_invoice_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Note Number -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Note ID</label>
                                    <input type="text" wire:model="invoice_number" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 bg-gray-50 font-mono" readonly>
                                    @error('invoice_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Status Selection -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Status</label>
                                    <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                        <option value="draft">Draft</option>
                                        <option value="posted">Posted</option>
                                        <option value="paid">Paid</option>
                                        <option value="void">Void</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Partner selection if no reference selected -->
                                @if($type === 'credit_note')
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase">Customer Reference</label>
                                        <select wire:model="customer_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" {{ $original_invoice_id ? 'disabled' : '' }}>
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $c)
                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('customer_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @else
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase">Supplier Reference</label>
                                        <select wire:model="supplier_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" {{ $original_invoice_id ? 'disabled' : '' }}>
                                            <option value="">Select Supplier</option>
                                            @foreach($suppliers as $v)
                                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                <!-- Date -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Date Issued</label>
                                    <input type="date" wire:model="date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    @error('date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Maturity Due Date</label>
                                    <input type="date" wire:model="due_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    @error('due_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Internal Notes / Adjustment Reason</label>
                                <textarea wire:model="notes" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="Describe the reason for adjustment/rebate..."></textarea>
                            </div>

                            <!-- Invoice Items List -->
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Adjustment Lines Ledger</h4>
                                    <button type="button" wire:click="addItem" class="text-xs font-bold text-blue-600 hover:text-blue-800 cursor-pointer">+ Add Line Item</button>
                                </div>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                        <thead class="bg-gray-50 font-semibold text-gray-500">
                                            <tr>
                                                <th class="py-2 px-4 text-left">Product Selection</th>
                                                <th class="py-2 px-4 text-right">Quantity</th>
                                                <th class="py-2 px-4 text-right">Unit Rate</th>
                                                <th class="py-2 px-4 text-right">Total Price</th>
                                                <th class="py-2 px-4 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @forelse($items as $index => $item)
                                                <tr>
                                                    <td class="py-2 px-4">
                                                        <select wire:model="items.{{ $index }}.product_id" class="w-full border border-gray-300 rounded px-2 py-1">
                                                            <option value="">Select Product</option>
                                                            @foreach($products as $p)
                                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="py-2 px-4 text-right font-mono">
                                                        <input type="number" step="0.01" min="0" wire:model.live="items.{{ $index }}.quantity" class="w-20 text-right border border-gray-300 rounded px-1.5 py-0.5">
                                                    </td>
                                                    <td class="py-2 px-4 text-right font-mono">
                                                        <input type="number" step="0.01" min="0" wire:model.live="items.{{ $index }}.unit_price" class="w-24 text-right border border-gray-300 rounded px-1.5 py-0.5">
                                                    </td>
                                                    <td class="py-2 px-4 text-right font-mono font-bold text-gray-800">
                                                        Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                                    </td>
                                                    <td class="py-2 px-4 text-center">
                                                        <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 cursor-pointer">Remove</button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="py-6 text-center text-gray-400 italic">No lines added yet. Click "+ Add Line Item" above.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Totals Panel -->
                            <div class="pt-4 border-t border-gray-200 flex justify-end">
                                <div class="w-64 space-y-2 text-xs font-sans">
                                    <div class="flex justify-between text-gray-500">
                                        <span>Total Amount:</span>
                                        <span class="font-mono">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-500">
                                        <span>Tax (11%):</span>
                                        <span class="font-mono">Rp {{ number_format($tax_amount, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm font-bold text-gray-900 border-t border-gray-150 pt-2">
                                        <span>Grand Total:</span>
                                        <span class="font-mono text-blue-700">Rp {{ number_format($grand_total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Save Invoice Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
