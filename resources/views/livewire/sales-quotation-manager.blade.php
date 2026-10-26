<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Sales Quotations</h1>
            <p class="text-sm text-gray-500 mt-1">Manage price offers and quotations sent to customers.</p>
        </div>
        <a href="{{ route('sales-quotations.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 shadow-2xs">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Quotation
        </a>
    </div>

    <!-- Alerts -->
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

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by quotation number or customer...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">SQ Number</th>
                        <th class="py-3.5 px-6">Customer</th>
                        <th class="py-3.5 px-6">Valid Until</th>
                        <th class="py-3.5 px-6 text-right">Estimated Amount</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($quotations as $sq)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-semibold text-blue-600">{{ $sq->sq_number }}</td>
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $sq->customer->name ?? ($sq->customer_name ?? '-') }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $sq->valid_until ? \Carbon\Carbon::parse($sq->valid_until)->format('Y-m-d') : '-' }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900 font-semibold">
                                Rp {{ number_format($sq->grand_total > 0 ? $sq->grand_total : $sq->items->sum('subtotal'), 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $sq->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $sq->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $sq->status === 'converted' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $sq->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($sq->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <a href="{{ route('sales-quotations.pdf.stream', $sq->id) }}" target="_blank" class="text-purple-600 hover:text-purple-900 font-medium cursor-pointer inline-flex items-center" title="View PDF">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    PDF
                                </a>
                                @if($sq->status === 'draft' || $sq->status === 'sent')
                                    <a href="{{ route('sales-quotations.edit', $sq->id) }}" wire:navigate class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</a>
                                    <button wire:click="convertToOrder({{ $sq->id }})" wire:confirm="Konversi penawaran ini menjadi Sales Order?" class="text-emerald-600 hover:text-emerald-900 font-medium cursor-pointer">Convert</button>
                                    <button wire:click="cancelQuotation({{ $sq->id }})" wire:confirm="Batalkan penawaran ini?" class="text-amber-600 hover:text-amber-900 font-medium cursor-pointer">Cancel</button>
                                @endif
                                <button wire:click="delete({{ $sq->id }})" wire:confirm="Yakin ingin menghapus penawaran harga ini?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-500">
                                No quotations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($quotations->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</div>
