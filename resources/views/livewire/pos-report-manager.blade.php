<div class="space-y-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-display text-[var(--color-text)]">📊 Laporan POS</h1>
            <p class="text-sm text-gray-500">Dashboard laporan penjualan, produk, dan EOD</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 flex flex-wrap gap-4 items-end">
        <div><label class="block text-xs font-semibold text-gray-500 mb-1">Dari</label><input type="date" wire:model.live="dateFrom" class="border-gray-300 rounded-lg text-sm"></div>
        <div><label class="block text-xs font-semibold text-gray-500 mb-1">Sampai</label><input type="date" wire:model.live="dateTo" class="border-gray-300 rounded-lg text-sm"></div>
        <div><label class="block text-xs font-semibold text-gray-500 mb-1">Store</label><select wire:model.live="storeFilter" class="border-gray-300 rounded-lg text-sm"><option value="">Semua Store</option>@foreach($stores as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach</select></div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit">
        <button wire:click="$set('activeTab', 'overview')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'overview' ? 'bg-white shadow text-gray-900' : 'text-gray-500' }}">Overview</button>
        <button wire:click="$set('activeTab', 'transactions')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'transactions' ? 'bg-white shadow text-gray-900' : 'text-gray-500' }}">Transaksi</button>
        <button wire:click="$set('activeTab', 'products')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'products' ? 'bg-white shadow text-gray-900' : 'text-gray-500' }}">Produk</button>
        <button wire:click="$set('activeTab', 'pnl')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'pnl' ? 'bg-white shadow text-gray-900' : 'text-gray-500' }}">Laba Rugi</button>
        <button wire:click="$set('activeTab', 'eod')" class="px-4 py-2 rounded-md text-sm font-medium {{ $activeTab === 'eod' ? 'bg-white shadow text-gray-900' : 'text-gray-500' }}">EOD / Shift</button>
    </div>

    @if($activeTab === 'overview')
    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 font-mono mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <p class="text-sm text-gray-500">Total Transaksi</p>
            <p class="text-2xl font-bold text-gray-900 font-mono mt-1">{{ number_format($totalTransactions) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <p class="text-sm text-gray-500">Rata-rata / Transaksi</p>
            <p class="text-2xl font-bold text-gray-900 font-mono mt-1">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-5">
            <p class="text-sm text-gray-500">Total Diskon</p>
            <p class="text-2xl font-bold text-red-600 font-mono mt-1">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Revenue Chart & Payment Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-bold text-gray-900 mb-4">Revenue Harian</h3>
            <div class="flex items-end justify-between h-48 px-2 border-b border-gray-150 pb-2">
                @php $maxDaily = $dailyRevenue->max('total') ?: 1; @endphp
                @foreach($dailyRevenue as $d)
                @php $hPct = max(($d->total / $maxDaily) * 100, 5); @endphp
                <div class="flex flex-col items-center flex-1 group">
                    <span class="text-[9px] font-mono text-blue-600 mb-1 opacity-0 group-hover:opacity-100 transition">{{ number_format($d->total/1000000, 1) }}M</span>
                    <div class="w-full max-w-8 bg-blue-100 rounded-t hover:bg-blue-600 transition cursor-pointer mx-0.5" style="height: {{ $hPct }}%"></div>
                    <span class="text-[9px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($d->date)->format('d/m') }}</span>
                </div>
                @endforeach
                @if($dailyRevenue->isEmpty())
                <div class="flex-1 text-center text-gray-400 text-sm py-16">Belum ada data</div>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-bold text-gray-900 mb-4">Metode Pembayaran</h3>
            <div class="space-y-4">
                @foreach($paymentBreakdown as $pm)
                @php $pPct = $totalRevenue > 0 ? ($pm->total / $totalRevenue) * 100 : 0; $colors = ['cash'=>'bg-emerald-500','qris'=>'bg-blue-500','bank_transfer'=>'bg-purple-500','multi'=>'bg-amber-500']; @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700 uppercase">{{ $pm->payment_method }}</span>
                        <span class="font-mono text-gray-500">{{ $pm->count }} trx • Rp {{ number_format($pm->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2"><div class="{{ $colors[$pm->payment_method] ?? 'bg-gray-400' }} h-2 rounded-full" style="width: {{ $pPct }}%"></div></div>
                </div>
                @endforeach
                @if($paymentBreakdown->isEmpty())<p class="text-sm text-gray-400 text-center py-4">Belum ada data</p>@endif
            </div>
        </div>
    </div>
    @endif

    @if($activeTab === 'transactions')
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">No. Transaksi</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Waktu</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Kasir</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Member</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Metode</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-mono text-xs text-blue-600">{{ $t->transaction_number }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ $t->created_at->format('d M Y H:i') }}</td>
                        <td class="py-3 px-4">{{ $t->user->name ?? '-' }}</td>
                        <td class="py-3 px-4">{{ $t->member->name ?? 'Walk-in' }}</td>
                        <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-bold uppercase">{{ $t->payment_method }}</span></td>
                        <td class="py-3 px-4 text-right font-mono font-bold">Rp {{ number_format($t->grand_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Tidak ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $transactions->links() }}</div>
    </div>
    @endif

    @if($activeTab === 'products')
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-5 border-b"><h3 class="font-bold text-gray-900">Top 10 Produk Terlaris</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="py-3 px-4 text-left font-semibold text-gray-600">#</th><th class="py-3 px-4 text-left font-semibold text-gray-600">Produk</th><th class="py-3 px-4 text-right font-semibold text-gray-600">Qty Terjual</th><th class="py-3 px-4 text-right font-semibold text-gray-600">Total Revenue</th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($topProducts as $i => $tp)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4"><span class="w-6 h-6 rounded-full {{ $i < 3 ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span></td>
                        <td class="py-3 px-4 font-medium text-gray-900">{{ $tp->product_name }}</td>
                        <td class="py-3 px-4 text-right font-mono">{{ number_format($tp->total_qty + 0) }}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-blue-600">Rp {{ number_format($tp->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @if($topProducts->isEmpty())<tr><td colspan="4" class="py-8 text-center text-gray-400">Belum ada data.</td></tr>@endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($activeTab === 'pnl')
    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="p-5 border-b bg-gray-50"><h3 class="font-bold text-gray-900 text-lg">Laporan Laba Rugi (Simplified)</h3><p class="text-xs text-gray-400">Periode: {{ $dateFrom }} s/d {{ $dateTo }}</p></div>
            <div class="divide-y">
                <div class="p-4 flex justify-between"><span class="font-medium text-gray-700">Pendapatan Kotor (Revenue)</span><span class="font-mono font-bold text-gray-900">Rp {{ number_format($grossRevenue, 0, ',', '.') }}</span></div>
                <div class="p-4 flex justify-between"><span class="text-gray-500 pl-4">(-) Harga Pokok Penjualan (HPP)</span><span class="font-mono text-red-600">Rp {{ number_format($cogs, 0, ',', '.') }}</span></div>
                <div class="p-4 flex justify-between bg-blue-50"><span class="font-bold text-blue-700">Laba Kotor (Gross Profit)</span><span class="font-mono font-bold text-blue-700">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span></div>
                <div class="p-4 flex justify-between"><span class="text-gray-500 pl-4">(-) Total Diskon Diberikan</span><span class="font-mono text-red-600">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span></div>
                <div class="p-4 flex justify-between"><span class="text-gray-500 pl-4">(+) Pajak Terkumpul</span><span class="font-mono text-emerald-600">Rp {{ number_format($totalTax, 0, ',', '.') }}</span></div>
                <div class="p-4 flex justify-between bg-emerald-50 border-t-2 border-emerald-200"><span class="font-bold text-emerald-800 text-lg">Estimasi Laba Bersih</span><span class="font-mono font-bold text-emerald-800 text-lg">Rp {{ number_format($grossProfit - $totalDiscount + $totalTax, 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>
    @endif

    @if($activeTab === 'eod')
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-5 border-b"><h3 class="font-bold text-gray-900">Riwayat Shift / End of Day (EOD)</h3></div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Kasir</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Store</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Buka</th>
                        <th class="py-3 px-4 text-left font-semibold text-gray-600">Tutup</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Kas Awal</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Kas Akhir</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Selisih</th>
                        <th class="py-3 px-4 text-right font-semibold text-gray-600">Revenue</th>
                        <th class="py-3 px-4 text-center font-semibold text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sessions as $ss)
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-medium">{{ $ss->user->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-gray-500">{{ $ss->store->name ?? '-' }}</td>
                        <td class="py-3 px-4 text-xs">{{ $ss->opened_at?->format('d/m H:i') }}</td>
                        <td class="py-3 px-4 text-xs">{{ $ss->closed_at?->format('d/m H:i') ?? '-' }}</td>
                        <td class="py-3 px-4 text-right font-mono">{{ number_format($ss->opening_cash, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-right font-mono">{{ $ss->closing_cash !== null ? number_format($ss->closing_cash, 0, ',', '.') : '-' }}</td>
                        <td class="py-3 px-4 text-right font-mono {{ ($ss->difference ?? 0) < 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $ss->difference !== null ? number_format($ss->difference, 0, ',', '.') : '-' }}</td>
                        <td class="py-3 px-4 text-right font-mono font-bold">Rp {{ number_format($ss->total_revenue, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 text-center"><span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $ss->status === 'open' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">{{ ucfirst($ss->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="py-8 text-center text-gray-400">Belum ada data sesi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t">{{ $sessions->links() }}</div>
    </div>
    @endif
</div>
