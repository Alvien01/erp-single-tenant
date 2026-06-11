<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Tax Management & Faktur Pajak</h1>
            <p class="text-sm text-gray-500 mt-1">Configure tax rates, manage Faktur Pajak PPN (Masukan / Keluaran), and track withholding tax liabilities (PPh).</p>
        </div>
        @if($activeTab === 'rates')
            <button wire:click="createTax" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Tax Rule
            </button>
        @elseif($activeTab === 'invoices')
            <button wire:click="createInvoice" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Record Faktur Pajak
            </button>
        @elseif($activeTab === 'withholding')
            <button wire:click="createWithholding" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Record Withholding Tax
            </button>
        @endif
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'rates')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'rates' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Tax Rates & Rules
            </button>
            <button wire:click="$set('activeTab', 'invoices')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'invoices' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Faktur Pajak PPN
            </button>
            <button wire:click="$set('activeTab', 'withholding')" class="border-b-2 py-4 px-1 text-sm font-medium {{ $activeTab === 'withholding' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Withholding Tax (PPh)
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

    <!-- Tab Contents -->
    @if($activeTab === 'rates')
        <!-- Taxes Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Tax Name</th>
                            <th class="py-3.5 px-6">Tax Rate (%)</th>
                            <th class="py-3.5 px-6">Scope Type</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($taxes as $tax)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 text-gray-900 font-semibold">{{ $tax->name }}</td>
                                <td class="py-4 px-6 font-mono font-bold text-gray-700">{{ number_format($tax->rate, 2) }}%</td>
                                <td class="py-4 px-6 text-gray-600 font-medium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $tax->type === 'sales' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                        {{ ucfirst($tax->type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <button wire:click="toggleTaxStatus({{ $tax->id }})" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer 
                                        {{ $tax->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}
                                    ">
                                        {{ $tax->is_active ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    <button wire:click="editTax({{ $tax->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="deleteTax({{ $tax->id }})" wire:confirm="Delete this tax rule?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">No tax rules configured.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($taxes->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $taxes->links() }}
                </div>
            @endif
        </div>

    @elseif($activeTab === 'invoices')
        <!-- PPN KPI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-sans">
            <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                <span class="text-sm font-semibold text-gray-400 uppercase">Total PPN Masukan (Approved)</span>
                <div class="text-2xl font-bold font-mono text-emerald-600 mt-2">Rp {{ number_format($ppnMasukanApproved, 2, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                <span class="text-sm font-semibold text-gray-400 uppercase">Total PPN Keluaran (Approved)</span>
                <div class="text-2xl font-bold font-mono text-red-600 mt-2">Rp {{ number_format($ppnKeluaranApproved, 2, ',', '.') }}</div>
            </div>
            <div class="bg-white rounded-lg p-5 border border-gray-200 shadow-sm">
                <span class="text-sm font-semibold text-gray-400 uppercase">Net PPN Payable / Receivable</span>
                <div class="text-2xl font-bold font-mono mt-2 {{ $netPpnPayable >= 0 ? 'text-red-700' : 'text-emerald-700' }}">
                    Rp {{ number_format(abs($netPpnPayable), 2, ',', '.') }}
                    <span class="text-xs font-semibold uppercase">({{ $netPpnPayable >= 0 ? 'Payable' : 'Overpaid / Receivable' }})</span>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Faktur Pajak No</th>
                            <th class="py-3.5 px-6">Type</th>
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6 text-right">DPP (Rp)</th>
                            <th class="py-3.5 px-6 text-right">PPN (Rp)</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($invoices as $inv)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-gray-900 font-semibold">{{ $inv->invoice_number }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-700">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $inv->type === 'masukan' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        PPN {{ ucfirst($inv->type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-500">{{ $inv->date }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($inv->dpp, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-blue-600 font-bold">Rp {{ number_format($inv->ppn, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $inv->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($inv->status === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}
                                    ">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <button wire:click="editInvoice({{ $inv->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No Faktur Pajak PPN recorded.</td>
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

    @elseif($activeTab === 'withholding')
        <!-- Withholding Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">ID</th>
                            <th class="py-3.5 px-6">Tax Code / Type</th>
                            <th class="py-3.5 px-6 text-right">Amount (Rp)</th>
                            <th class="py-3.5 px-6">Reference Document</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($withholding as $wht)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-gray-400">#{{ $wht->id }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900 font-mono">{{ strtoupper($wht->type) }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-red-600">Rp {{ number_format($wht->amount, 2, ',', '.') }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $wht->reference_type ? $wht->reference_type . ' (ID: ' . $wht->reference_id . ')' : '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $wht->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}
                                    ">
                                        {{ ucfirst($wht->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <button wire:click="editWithholding({{ $wht->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No withholding tax entries logged.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($withholding->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $withholding->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Modals Section -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if($modalType === 'tax')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                                {{ $tax_id ? 'Edit Tax Rule' : 'Add New Tax Rule' }}
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tax Name</label>
                                    <input type="text" wire:model="name" placeholder="e.g. PPN 11%" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Rate (%)</label>
                                        <input type="number" step="0.01" wire:model="rate" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('rate') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Scope Type</label>
                                        <select wire:model="type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="sales">Sales (Tax on Outgoings)</option>
                                            <option value="purchase">Purchase (Tax on Incomings)</option>
                                        </select>
                                        @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="inline-flex items-center mt-2">
                                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Is Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveTax" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Save Rule</button>
                        </div>

                    @elseif($modalType === 'invoice')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Record Faktur Pajak PPN</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Faktur Pajak Number</label>
                                    <input type="text" wire:model="invoice_number" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm font-mono">
                                    @error('invoice_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">PPN Type</label>
                                        <select wire:model="invoice_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="masukan">PPN Masukan (Input Tax)</option>
                                            <option value="keluaran">PPN Keluaran (Output Tax)</option>
                                        </select>
                                        @error('invoice_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Invoice Date</label>
                                        <input type="date" wire:model="invoice_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('invoice_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">DPP (Rp)</label>
                                        <input type="number" wire:model="invoice_dpp" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('invoice_dpp') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">PPN Amount (Rp)</label>
                                        <input type="number" wire:model="invoice_ppn" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('invoice_ppn') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="invoice_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="draft">Draft</option>
                                        <option value="submitted">Submitted / E-Faktur</option>
                                        <option value="approved">Approved & Journaled</option>
                                    </select>
                                    @error('invoice_status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveInvoice" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold font-display">Save Invoice</button>
                        </div>

                    @elseif($modalType === 'withholding')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Record Withholding Tax (PPh)</h3>
                            <div class="mt-4 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">PPh Article / Type</label>
                                        <select wire:model="wht_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="pph21">PPh Pasal 21 (Employee)</option>
                                            <option value="pph22">PPh Pasal 22 (Import/Goods)</option>
                                            <option value="pph23">PPh Pasal 23 (Services/Rent)</option>
                                            <option value="pph25">PPh Pasal 25 (Monthly Installment)</option>
                                            <option value="pph29">PPh Pasal 29 (Annual Tax Pay)</option>
                                        </select>
                                        @error('wht_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount (Rp)</label>
                                        <input type="number" wire:model="wht_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('wht_amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                                    <select wire:model="wht_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="unpaid">Unpaid / Outstanding Liability</option>
                                        <option value="paid">Paid & Settled</option>
                                    </select>
                                    @error('wht_status') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                            <button type="button" wire:click="saveWithholding" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold font-display">Save Entry</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
