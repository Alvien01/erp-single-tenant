<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Sales Transactions</h1>
            <p class="text-sm text-gray-500 mt-1">Manage quotations, orders, and sales invoicing records.</p>
        </div>
        <a href="{{ route('sales.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 transition ease-in-out duration-150 shadow-2xs">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Invoice
        </a>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by Invoice # or Customer...">
        </div>
        
        <div class="w-full md:w-64">
            <select wire:model.live="status" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Invoice #</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Sale Date</th>
                        <th class="py-3.5 px-6 text-right">Subtotal (DPP)</th>
                        <th class="py-3.5 px-6 text-right">Tax (Pajak)</th>
                        <th class="py-3.5 px-6 text-right">Grand Total</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-medium text-blue-600">{{ $sale->invoice_number }}</td>
                            <td class="py-4 px-6 font-medium text-gray-900">{{ $sale->customer->name ?? ($sale->customer_name ?? '-') }}</td>
                            <td class="py-4 px-6">{{ $sale->sale_date }}</td>
                            <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($sale->total_amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($sale->tax_amount, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right font-mono font-semibold text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sale->status_badge_class }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <a href="{{ route('sales.pdf.stream', $sale->id) }}" target="_blank" class="text-purple-600 hover:text-purple-900 font-medium cursor-pointer inline-flex items-center" title="Cetak PDF">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    PDF
                                </a>
                                <a href="{{ route('sales.edit', $sale->id) }}" wire:navigate class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</a>
                                <button wire:click="delete({{ $sale->id }})" wire:confirm="Are you sure you want to delete this invoice?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500">
                                No sales invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sales->links() }}
            </div>
        @endif
    </div>
</div>
