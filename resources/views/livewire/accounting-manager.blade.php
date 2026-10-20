<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Akuntansi & Sistem Informasi Akuntansi
                (SIA)</h1>
            <p class="text-sm text-gray-500 mt-1 font-sans">Enterprise-grade multi-entry journals, standard financial
                statements, real-time trial balance, audit controls & depreciation.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($activeTab === 'coa')
                <button wire:click="seedStandardCoa"
                    class="inline-flex items-center px-4 py-2 bg-indigo-50 border border-indigo-200 rounded-lg font-semibold text-xs text-indigo-700 hover:bg-indigo-100 transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    Inisialisasi COA Standar PSAK
                </button>
            @elseif($activeTab === 'assets')
                <button wire:click="runDepreciation"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Run Depreciation
                </button>
                <button wire:click="createAsset"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Register Asset
                </button>
            @elseif($activeTab === 'journals')
                <button wire:click="createJournal"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 shadow-sm transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    + Buat Jurnal Baru (Multi-Line)
                </button>
            @elseif($activeTab === 'financial_reports')
                <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-wider hover:bg-gray-900 transition cursor-pointer">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak / Simpan PDF
                </button>
            @endif
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div
            class="p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between font-sans print:hidden">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong class="font-semibold">Sukses!</strong> {{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif
    @if (session()->has('error'))
        <div
            class="p-4 mb-4 text-sm text-rose-800 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between font-sans print:hidden">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-rose-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong class="font-semibold">Perhatian!</strong> {{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">&times;</button>
        </div>
    @endif
    @if (session()->has('info'))
        <div
            class="p-4 mb-4 text-sm text-blue-800 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between font-sans print:hidden">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">&times;</button>
        </div>
    @endif

    <!-- Lock Date Notification Banner -->
    @if($accounting_lock_date)
        <div
            class="px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between text-amber-800 text-sm font-sans print:hidden">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
                <span><strong>Accounting Lock Date Aktif:</strong> Transaksi pada atau sebelum
                    <strong>{{ date('d F Y', strtotime($accounting_lock_date)) }}</strong> terkunci dari penambahan atau
                    perubahan bukti.</span>
            </div>
            <button wire:click="$set('activeTab', 'closing')"
                class="text-xs font-semibold text-amber-900 underline hover:no-underline">Kelola Kunci Buku</button>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display print:hidden">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'dashboard')"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'dashboard' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Analytics Dashboard
            </button>
            <button wire:click="$set('activeTab', 'coa')"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'coa' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Chart of Accounts
            </button>
            <button wire:click="$set('activeTab', 'journals')"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'journals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Journal Ledger
            </button>
            <button wire:click="$set('activeTab', 'ledger_detail')"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'ledger_detail' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Buku Besar Detail
            </button>
            <button wire:click="$set('activeTab', 'financial_reports')"
                class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'financial_reports' ? 'border-blue-500 text-blue-600 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Laporan Keuangan Resmi
            </button>
            <button wire:click="$set('activeTab', 'closing')" class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'closing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Tutup Buku & Lock Date
            </button>
            <button wire:click="$set('activeTab', 'assets')" class="py-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition-colors cursor-pointer {{ $activeTab === 'assets' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Fixed Assets
            </button>
        </nav>
    </div>

    <!-- ==================== TAB 1: DASHBOARD ==================== -->
    @if($activeTab === 'dashboard')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 font-sans">
            <!-- Total Assets -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Aset (Aktiva)</span>
                        <h3 class="text-2xl font-bold font-display text-gray-900 mt-2">Rp {{ number_format($stats['totalAssets'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-lg bg-blue-50 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="text-emerald-600 font-semibold inline-flex items-center mr-1">
                        Kas & Bank: Rp {{ number_format($stats['cashBankBalance'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Total Liabilities -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Kewajiban (Liabilitas)</span>
                        <h3 class="text-2xl font-bold font-display text-gray-900 mt-2">Rp {{ number_format($stats['totalLiabilities'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-lg bg-rose-50 text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="text-rose-600 font-semibold inline-flex items-center mr-1">
                        Utang Usaha: Rp {{ number_format($stats['payablesBalance'], 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Total Equity -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Ekuitas Modal</span>
                        <h3 class="text-2xl font-bold font-display text-gray-900 mt-2">Rp {{ number_format($stats['totalEquity'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="text-indigo-600 font-medium">Modal Disetor & Laba Ditahan</span>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Laba / Rugi Berjalan</span>
                        <h3 class="text-2xl font-bold font-display {{ $stats['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-2">
                            Rp {{ number_format($stats['netProfit'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-3 rounded-lg {{ $stats['netProfit'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span>Pendapatan: Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden font-sans mt-6">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-bold text-gray-900 text-sm">Transaksi Jurnal Terkini</h3>
                <button wire:click="$set('activeTab', 'journals')" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Lihat Semua Jurnal &rarr;</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3 px-6">No. Bukti (Voucher)</th>
                            <th class="py-3 px-6">Tanggal</th>
                            <th class="py-3 px-6">Akun COA</th>
                            <th class="py-3 px-6">Keterangan</th>
                            <th class="py-3 px-6 text-right">Debit</th>
                            <th class="py-3 px-6 text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($stats['recentTransactions'] as $tx)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-6">
                                    <button wire:click="viewVoucher('{{ $tx->reference_number }}')" class="font-mono text-blue-600 hover:underline font-semibold cursor-pointer">
                                        {{ $tx->reference_number }}
                                    </button>
                                </td>
                                <td class="py-3 px-6 text-gray-500">{{ $tx->transaction_date }}</td>
                                <td class="py-3 px-6 font-medium text-gray-900">{{ $tx->account->code }} - {{ $tx->account->name }}</td>
                                <td class="py-3 px-6 text-gray-600">{{ $tx->description }}</td>
                                <td class="py-3 px-6 text-right font-mono text-emerald-600 font-semibold">{{ $tx->type === 'debit' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}</td>
                                <td class="py-3 px-6 text-right font-mono text-rose-600 font-semibold">{{ $tx->type === 'credit' ? 'Rp ' . number_format($tx->amount, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">Belum ada transaksi jurnal tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @if($activeTab === 'coa')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 font-sans">
            <div class="flex-1 max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-1 focus:ring-blue-500" placeholder="Cari kode akun atau nama akun...">
            </div>
            <div class="text-xs text-gray-500">
                Total Akun Terdaftar: <strong class="text-gray-900 font-mono">{{ count($accounts) }}</strong>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Kode Akun</th>
                            <th class="py-3.5 px-6">Nama Akun</th>
                            <th class="py-3.5 px-6">Klasifikasi</th>
                            <th class="py-3.5 px-6 text-right">Saldo Berjalan</th>
                            <th class="py-3.5 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($accounts as $acc)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-3.5 px-6 font-mono text-blue-600 font-bold">{{ $acc->code }}</td>
                                <td class="py-3.5 px-6 font-medium text-gray-900">{{ $acc->name }}</td>
                                <td class="py-3.5 px-6 text-gray-500">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $acc->type === 'asset' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $acc->type === 'liability' ? 'bg-rose-100 text-rose-800' : '' }}
                                        {{ $acc->type === 'equity' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $acc->type === 'income' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $acc->type === 'expense' ? 'bg-amber-100 text-amber-800' : '' }}">
                                        {{ ucfirst($acc->type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right font-mono font-bold text-gray-900">
                                    Rp {{ number_format($acc->balance, 2, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    <button wire:click="drillDownToLedger({{ $acc->id }})" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg cursor-pointer">
                                        Buku Besar &rarr;
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">
                                    Tidak ada akun COA ditemukan. Klik "Inisialisasi COA Standar PSAK" untuk membuat akun otomatis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
    @if($activeTab === 'journals')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 font-sans flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <label class="text-xs font-semibold text-gray-500 uppercase">Tipe Jurnal:</label>
                <select wire:model.live="filter_journal_type" class="border border-gray-300 rounded-lg py-1.5 px-3 text-sm bg-white">
                    <option value="">Semua Tipe</option>
                    <option value="general">General Journal</option>
                    <option value="adjustment">Adjusting Entry</option>
                    <option value="closing">Closing Entry</option>
                </select>
            </div>
            <div class="text-xs text-gray-500">
                💡 Klik pada <strong>No. Bukti</strong> untuk melihat detail voucher atau melakukan <strong>Jurnal Pembalik (Void)</strong>.
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">No. Bukti (Voucher)</th>
                            <th class="py-3.5 px-6">Tanggal</th>
                            <th class="py-3.5 px-6">Akun COA</th>
                            <th class="py-3.5 px-6">Keterangan / Memo</th>
                            <th class="py-3.5 px-6 text-center">Tipe</th>
                            <th class="py-3.5 px-6 text-right">Debit</th>
                            <th class="py-3.5 px-6 text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($journals as $j)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-3.5 px-6 font-mono">
                                    <button wire:click="viewVoucher('{{ $j->reference_number }}')" class="text-blue-600 hover:text-blue-800 hover:underline font-bold cursor-pointer inline-flex items-center">
                                        {{ $j->reference_number }}
                                        <svg class="w-3 h-3 ml-1 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </button>
                                </td>
                                <td class="py-3.5 px-6 text-gray-500">{{ $j->transaction_date }}</td>
                                <td class="py-3.5 px-6 font-semibold text-gray-900">{{ $j->account->code }} - {{ $j->account->name }}</td>
                                <td class="py-3.5 px-6 text-gray-600">{{ $j->description }}</td>
                                <td class="py-3.5 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold 
                                        {{ $j->journal_type === 'closing' ? 'bg-red-100 text-red-800' : ($j->journal_type === 'adjustment' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($j->journal_type ?: 'general') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-right font-mono text-emerald-600 font-semibold">{{ $j->type === 'debit' ? 'Rp ' . number_format($j->amount, 0, ',', '.') : '-' }}</td>
                                <td class="py-3.5 px-6 text-right font-mono text-rose-600 font-semibold">{{ $j->type === 'credit' ? 'Rp ' . number_format($j->amount, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">Belum ada baris jurnal yang tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($journals->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $journals->links() }}
                </div>
            @endif
        </div>
    @endif
    @if($activeTab === 'ledger_detail')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 font-sans space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Pilih Akun Buku Besar</label>
                    <select wire:model.live="selected_account_id" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white">
                        <option value="">-- Pilih Akun --</option>
                        @foreach($allAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Dari Tanggal</label>
                    <input type="date" wire:model.live="ledger_start_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Sampai Tanggal</label>
                    <input type="date" wire:model.live="ledger_end_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm">
                </div>
            </div>
        </div>

        @if($selected_account_id)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden font-sans">
                <div class="p-6 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gray-50">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $allAccounts->find($selected_account_id)->name }}</h3>
                        <p class="text-xs text-gray-500 font-mono mt-0.5">Kode Akun: <span class="font-bold text-blue-600">{{ $allAccounts->find($selected_account_id)->code }}</span> | Klasifikasi: {{ ucfirst($allAccounts->find($selected_account_id)->type) }}</p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-right">
                            <span class="text-xs text-gray-500 block">Saldo Awal:</span>
                            <div class="text-base font-bold text-gray-900 font-mono">Rp {{ number_format($openingBalance, 2, ',', '.') }}</div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500 block">Saldo Akhir:</span>
                            <div class="text-base font-bold text-emerald-600 font-mono">Rp {{ number_format($closingBalance, 2, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3 px-6">Tanggal</th>
                                <th class="py-3 px-6">No. Bukti Jurnal</th>
                                <th class="py-3 px-6">Keterangan</th>
                                <th class="py-3 px-6 text-right">Debit</th>
                                <th class="py-3 px-6 text-right">Kredit</th>
                                <th class="py-3 px-6 text-right">Saldo Berjalan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="bg-blue-50/40">
                                <td class="py-3 px-6 text-gray-500 font-medium" colspan="3">Saldo Awal per {{ date('d/m/Y', strtotime($ledger_start_date)) }}</td>
                                <td class="py-3 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-3 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-3 px-6 text-right font-mono text-gray-900 font-bold">Rp {{ number_format($openingBalance, 2, ',', '.') }}</td>
                            </tr>
                            @forelse($ledgerEntries as $entry)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-3 px-6 text-gray-500">{{ $entry->transaction_date }}</td>
                                    <td class="py-3 px-6 font-mono">
                                        <button wire:click="viewVoucher('{{ $entry->reference_number }}')" class="text-blue-600 hover:underline font-bold cursor-pointer">
                                            {{ $entry->reference_number }}
                                        </button>
                                    </td>
                                    <td class="py-3 px-6 text-gray-700">{{ $entry->description }}</td>
                                    <td class="py-3 px-6 text-right font-mono text-emerald-600 font-semibold">
                                        {{ $entry->type === 'debit' ? 'Rp ' . number_format($entry->amount, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-6 text-right font-mono text-rose-600 font-semibold">
                                        {{ $entry->type === 'credit' ? 'Rp ' . number_format($entry->amount, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-6 text-right font-mono text-gray-900 font-bold">
                                        Rp {{ number_format($entry->running_balance, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">Tidak ada mutasi transaksi pada rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                            <tr class="bg-emerald-50/40 font-bold">
                                <td class="py-3.5 px-6 text-gray-900" colspan="3">Saldo Akhir per {{ date('d/m/Y', strtotime($ledger_end_date)) }}</td>
                                <td class="py-3.5 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-3.5 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-3.5 px-6 text-right font-mono text-emerald-700 font-bold text-base">Rp {{ number_format($closingBalance, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500 font-sans">
                Silakan pilih akun buku besar di atas untuk melihat mutasi detail kartu buku besar.
            </div>
        @endif
    @endif
    @if($activeTab === 'financial_reports')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 font-sans space-y-4 print:hidden">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="inline-flex p-1 bg-gray-100 rounded-xl">
                    <button wire:click="$set('report_type', 'income_statement')" class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer {{ $report_type === 'income_statement' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Laba Rugi (P&L)
                    </button>
                    <button wire:click="$set('report_type', 'balance_sheet')" class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer {{ $report_type === 'balance_sheet' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Neraca (Balance Sheet)
                    </button>
                    <button wire:click="$set('report_type', 'trial_balance')" class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer {{ $report_type === 'trial_balance' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Neraca Saldo (Trial Balance)
                    </button>
                    <button wire:click="$set('report_type', 'cash_flow')" class="px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer {{ $report_type === 'cash_flow' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                        Arus Kas (Cash Flow)
                    </button>
                </div>

                <!-- Date Filter Presets -->
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="setReportPeriod('this_month')" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $report_period === 'this_month' ? 'bg-blue-50 border-blue-300 text-blue-700' : 'bg-white border-gray-200 text-gray-700' }} cursor-pointer">Bulan Ini</button>
                    <button wire:click="setReportPeriod('last_month')" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $report_period === 'last_month' ? 'bg-blue-50 border-blue-300 text-blue-700' : 'bg-white border-gray-200 text-gray-700' }} cursor-pointer">Bulan Lalu</button>
                    <button wire:click="setReportPeriod('this_quarter')" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $report_period === 'this_quarter' ? 'bg-blue-50 border-blue-300 text-blue-700' : 'bg-white border-gray-200 text-gray-700' }} cursor-pointer">Kuartal Ini</button>
                    <button wire:click="setReportPeriod('this_year')" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $report_period === 'this_year' ? 'bg-blue-50 border-blue-300 text-blue-700' : 'bg-white border-gray-200 text-gray-700' }} cursor-pointer">Tahun Ini</button>

                    <div class="flex items-center gap-1.5 ml-2">
                        <input type="date" wire:model.live="report_start_date" class="border border-gray-300 rounded-lg py-1 px-2 text-xs">
                        <span class="text-gray-400 text-xs">s/d</span>
                        <input type="date" wire:model.live="report_end_date" class="border border-gray-300 rounded-lg py-1 px-2 text-xs">
                    </div>
                </div>
            </div>
        </div>

        <!-- Formal Report Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 font-sans print:shadow-none print:border-none print:p-0">
            <!-- Corporate Report Header -->
            <div class="text-center pb-6 border-b border-gray-200">
                <h2 class="text-xl font-bold font-display text-gray-900 tracking-wide uppercase">
                    @if($report_type === 'income_statement') LAPORAN LABA RUGI KOMPREHENSIF (INCOME STATEMENT)
                    @elseif($report_type === 'balance_sheet') LAPORAN POSISI KEUANGAN (NERACA / BALANCE SHEET)
                    @elseif($report_type === 'trial_balance') NERACA SALDO (TRIAL BALANCE - 6 KOLOM)
                    @elseif($report_type === 'cash_flow') LAPORAN ARUS KAS (CASH FLOW STATEMENT)
                    @endif
                </h2>
                <p class="text-sm text-gray-500 mt-1">Periode: {{ date('d F Y', strtotime($report_start_date)) }} s/d {{ date('d F Y', strtotime($report_end_date)) }}</p>
                <p class="text-xs text-gray-400 font-mono mt-0.5">(Dinyatakan dalam Rupiah Indonesia [IDR])</p>
            </div>

            <!-- 1. SUB-REPORT: LABA RUGI (INCOME STATEMENT) -->
            @if($report_type === 'income_statement' && !empty($reportData))
                    <div class="mt-6 space-y-6 max-w-4xl mx-auto">
                        <!-- Pendapatan Usaha -->
                        <div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-300 font-bold text-gray-900 uppercase text-xs tracking-wider">
                                <span>I. PENDAPATAN USAHA (REVENUE)</span>
                                <span>JUMLAH</span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($reportData['operatingRevenue'] as $r)
                                    <div class="flex justify-between items-center py-2 text-sm hover:bg-gray-50 px-2 rounded">
                                        <button wire:click="drillDownToLedger({{ $r['id'] }})" class="text-left font-mono text-blue-600 hover:underline">
                                            {{ $r['code'] }} - {{ $r['name'] }}
                                        </button>
                                        <span class="font-mono text-gray-900">Rp {{ number_format($r['amount'], 2, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="py-2 text-sm text-gray-400 italic px-2">Tidak ada pendapatan usaha pada periode ini.</div>
                                @endforelse
                            </div>
                            <div class="flex justify-between items-center py-2.5 font-bold text-gray-900 bg-gray-50 px-3 rounded-lg mt-1 text-sm">
                                <span>TOTAL PENDAPATAN USAHA</span>
                                <span class="font-mono">Rp {{ number_format($reportData['totalRevenue'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Harga Pokok Penjualan (HPP) -->
                        <div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-300 font-bold text-gray-900 uppercase text-xs tracking-wider">
                                <span>II. HARGA POKOK PENJUALAN (HPP / COGS)</span>
                                <span>JUMLAH</span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($reportData['cogsList'] as $c)
                                    <div class="flex justify-between items-center py-2 text-sm hover:bg-gray-50 px-2 rounded">
                                        <button wire:click="drillDownToLedger({{ $c['id'] }})" class="text-left font-mono text-blue-600 hover:underline">
                                            {{ $c['code'] }} - {{ $c['name'] }}
                                        </button>
                                        <span class="font-mono text-gray-900">Rp {{ number_format($c['amount'], 2, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="py-2 text-sm text-gray-400 italic px-2">Tidak ada beban HPP pada periode ini.</div>
                                @endforelse
                            </div>
                            <div class="flex justify-between items-center py-2.5 font-bold text-gray-900 bg-gray-50 px-3 rounded-lg mt-1 text-sm">
                                <span>TOTAL HARGA POKOK PENJUALAN</span>
                                <span class="font-mono">Rp {{ number_format($reportData['totalCogs'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- LABA KOTOR (GROSS PROFIT) -->
                        <div class="flex justify-between items-center py-3 px-4 rounded-xl bg-blue-50 border border-blue-200 font-bold text-blue-900 text-base">
                            <span>LABA KOTOR (GROSS PROFIT)</span>
                            <span class="font-mono">Rp {{ number_format($reportData['grossProfit'], 2, ',', '.') }}</span>
                        </div>

                        <!-- Beban Operasional -->
                        <div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-300 font-bold text-gray-900 uppercase text-xs tracking-wider">
                                <span>III. BEBAN OPERASIONAL & ADMINISTRASI</span>
                                <span>JUMLAH</span>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @forelse($reportData['operatingExpenses'] as $oe)
                                    <div class="flex justify-between items-center py-2 text-sm hover:bg-gray-50 px-2 rounded">
                                        <button wire:click="drillDownToLedger({{ $oe['id'] }})" class="text-left font-mono text-blue-600 hover:underline">
                                            {{ $oe['code'] }} - {{ $oe['name'] }}
                                        </button>
                                        <span class="font-mono text-gray-900">Rp {{ number_format($oe['amount'], 2, ',', '.') }}</span>
                                    </div>
                                @empty
                                    <div class="py-2 text-sm text-gray-400 italic px-2">Tidak ada beban operasional pada periode ini.</div>
                                @endforelse
                            </div>
                            <div class="flex justify-between items-center py-2.5 font-bold text-gray-900 bg-gray-50 px-3 rounded-lg mt-1 text-sm">
                                <span>TOTAL BEBAN OPERASIONAL</span>
                                <span class="font-mono">Rp {{ number_format($reportData['totalOperatingExpense'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- LABA OPERASI (EBITDA) -->
                        <div class="flex justify-between items-center py-3 px-4 rounded-xl bg-indigo-50 border border-indigo-200 font-bold text-indigo-900 text-base">
                            <span>LABA OPERASI (OPERATING INCOME / EBITDA)</span>
                            <span class="font-mono">Rp {{ number_format($reportData['operatingIncome'], 2, ',', '.') }}</span>
                        </div>

                        <!-- LABA BERSIH (NET INCOME) -->
                        <div class="flex justify-between items-center py-4 px-5 rounded-xl {{ $reportData['netProfit'] >= 0 ? 'bg-emerald-100 border-2 border-emerald-400 text-emerald-950' : 'bg-rose-100 border-2 border-rose-400 text-rose-950' }} font-bold text-lg">
                            <span>LABA (RUGI) BERSIH TAHUN BERJALAN</span>
                            <span class="font-mono underline decoration-double">Rp {{ number_format($reportData['netProfit'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                <!-- 2. SUB-REPORT: NERACA (BALANCE SHEET) -->
            @elseif($report_type === 'balance_sheet' && !empty($reportData))
                    <div class="mt-6 space-y-8 max-w-5xl mx-auto">
                        <!-- Balance Indicator Status -->
                        <div class="p-3 rounded-xl flex items-center justify-between text-xs font-semibold {{ $reportData['isBalanced'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-rose-50 text-rose-800 border border-rose-200' }}">
                            <span>Status Keseimbangan Neraca: <strong>{{ $reportData['isBalanced'] ? 'SEIMBANG (Aktiva = Pasiva)' : 'BELUM SEIMBANG' }}</strong></span>
                            <span class="font-mono">Total Aktiva: Rp {{ number_format($reportData['totalAssets'], 2, ',', '.') }} | Total Pasiva: Rp {{ number_format($reportData['totalLiabilitiesAndEquity'], 2, ',', '.') }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- KOLOM AKTIVA (ASSETS) -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 border-b-2 border-blue-600 pb-2">AKTIVA (ASET)</h3>

                                <!-- Aset Lancar -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Aset Lancar (Current Assets)</h4>
                                    <div class="divide-y divide-gray-100 mt-2">
                                        @forelse($reportData['currentAssets'] as $ca)
                                            <div class="flex justify-between py-1.5 text-xs">
                                                <button wire:click="drillDownToLedger({{ $ca['id'] }})" class="font-mono text-blue-600 hover:underline">{{ $ca['code'] }} - {{ $ca['name'] }}</button>
                                                <span class="font-mono text-gray-900">Rp {{ number_format($ca['amount'], 2, ',', '.') }}</span>
                                            </div>
                                        @empty
                                            <div class="py-1 text-xs text-gray-400">Tidak ada aset lancar.</div>
                                        @endforelse
                                    </div>
                                    <div class="flex justify-between py-2 font-bold text-xs bg-gray-50 px-2 rounded mt-1">
                                        <span>Subtotal Aset Lancar</span>
                                        <span class="font-mono">Rp {{ number_format($reportData['totalCurrentAssets'], 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Aset Tidak Lancar -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Aset Tidak Lancar (Fixed Assets)</h4>
                                    <div class="divide-y divide-gray-100 mt-2">
                                        @forelse($reportData['nonCurrentAssets'] as $nca)
                                            <div class="flex justify-between py-1.5 text-xs">
                                                <button wire:click="drillDownToLedger({{ $nca['id'] }})" class="font-mono text-blue-600 hover:underline">{{ $nca['code'] }} - {{ $nca['name'] }}</button>
                                                <span class="font-mono text-gray-900">Rp {{ number_format($nca['amount'], 2, ',', '.') }}</span>
                                            </div>
                                        @empty
                                            <div class="py-1 text-xs text-gray-400">Tidak ada aset tetap.</div>
                                        @endforelse
                                    </div>
                                    <div class="flex justify-between py-2 font-bold text-xs bg-gray-50 px-2 rounded mt-1">
                                        <span>Subtotal Aset Tidak Lancar</span>
                                        <span class="font-mono">Rp {{ number_format($reportData['totalNonCurrentAssets'], 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- TOTAL AKTIVA -->
                                <div class="flex justify-between py-3 px-4 bg-blue-600 text-white font-bold rounded-xl text-sm">
                                    <span>TOTAL AKTIVA</span>
                                    <span class="font-mono text-base">Rp {{ number_format($reportData['totalAssets'], 2, ',', '.') }}</span>
                                </div>
                            </div>

                            <!-- KOLOM PASIVA (LIABILITIES & EQUITY) -->
                            <div class="space-y-6">
                                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-900 border-b-2 border-rose-600 pb-2">PASIVA (KEWAJIBAN & EKUITAS)</h3>

                                <!-- Liabilitas Lancar -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Liabilitas Jangka Pendek</h4>
                                    <div class="divide-y divide-gray-100 mt-2">
                                        @forelse($reportData['currentLiabilities'] as $cl)
                                            <div class="flex justify-between py-1.5 text-xs">
                                                <button wire:click="drillDownToLedger({{ $cl['id'] }})" class="font-mono text-blue-600 hover:underline">{{ $cl['code'] }} - {{ $cl['name'] }}</button>
                                                <span class="font-mono text-gray-900">Rp {{ number_format($cl['amount'], 2, ',', '.') }}</span>
                                            </div>
                                        @empty
                                            <div class="py-1 text-xs text-gray-400">Tidak ada kewajiban lancar.</div>
                                        @endforelse
                                    </div>
                                    <div class="flex justify-between py-2 font-bold text-xs bg-gray-50 px-2 rounded mt-1">
                                        <span>Subtotal Liabilitas Lancar</span>
                                        <span class="font-mono">Rp {{ number_format($reportData['totalCurrentLiabilities'], 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- Ekuitas Modal -->
                                <div>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase">Ekuitas Modal Pemilik</h4>
                                    <div class="divide-y divide-gray-100 mt-2">
                                        @foreach($reportData['equityList'] as $eq)
                                            <div class="flex justify-between py-1.5 text-xs">
                                                <button wire:click="drillDownToLedger({{ $eq['id'] }})" class="font-mono text-blue-600 hover:underline">{{ $eq['code'] }} - {{ $eq['name'] }}</button>
                                                <span class="font-mono text-gray-900">Rp {{ number_format($eq['amount'], 2, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                        <div class="flex justify-between py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50/50 px-1 rounded">
                                            <span>Laba (Rugi) Periode Berjalan</span>
                                            <span class="font-mono">Rp {{ number_format($reportData['currentPeriodProfit'], 2, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex justify-between py-2 font-bold text-xs bg-gray-50 px-2 rounded mt-1">
                                        <span>Total Ekuitas</span>
                                        <span class="font-mono">Rp {{ number_format($reportData['totalEquity'], 2, ',', '.') }}</span>
                                    </div>
                                </div>

                                <!-- TOTAL PASIVA -->
                                <div class="flex justify-between py-3 px-4 bg-gray-900 text-white font-bold rounded-xl text-sm">
                                    <span>TOTAL KEWAJIBAN & EKUITAS</span>
                                    <span class="font-mono text-base">Rp {{ number_format($reportData['totalLiabilitiesAndEquity'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- 3. SUB-REPORT: NERACA SALDO (TRIAL BALANCE) -->
            @elseif($report_type === 'trial_balance' && !empty($reportData))
                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 font-bold uppercase">
                                    <th class="py-3 px-4 text-left">Kode Akun</th>
                                    <th class="py-3 px-4 text-left">Nama Akun</th>
                                    <th class="py-3 px-4 text-left">Tipe</th>
                                    <th class="py-3 px-4 text-right">Saldo Awal</th>
                                    <th class="py-3 px-4 text-right text-emerald-700">Mutasi Debit</th>
                                    <th class="py-3 px-4 text-right text-rose-700">Mutasi Kredit</th>
                                    <th class="py-3 px-4 text-right">Saldo Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($reportData['rows'] as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2.5 px-4 font-mono font-bold text-blue-600">
                                            <button wire:click="drillDownToLedger({{ $row['id'] }})" class="hover:underline">{{ $row['code'] }}</button>
                                        </td>
                                        <td class="py-2.5 px-4 font-medium text-gray-900">{{ $row['name'] }}</td>
                                        <td class="py-2.5 px-4 text-gray-500 uppercase">{{ $row['type'] }}</td>
                                        <td class="py-2.5 px-4 text-right font-mono text-gray-700">Rp {{ number_format($row['opening'], 2, ',', '.') }}</td>
                                        <td class="py-2.5 px-4 text-right font-mono text-emerald-600 font-semibold">{{ $row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 2, ',', '.') : '-' }}</td>
                                        <td class="py-2.5 px-4 text-right font-mono text-rose-600 font-semibold">{{ $row['credit'] > 0 ? 'Rp ' . number_format($row['credit'], 2, ',', '.') : '-' }}</td>
                                        <td class="py-2.5 px-4 text-right font-mono font-bold text-gray-900">Rp {{ number_format($row['ending'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <!-- TOTALS ROW -->
                                <tr class="bg-gray-200 text-gray-900 font-bold text-xs">
                                    <td colspan="4" class="py-3 px-4 text-right uppercase">TOTAL MUTASI PERIODE</td>
                                    <td class="py-3 px-4 text-right font-mono text-emerald-800">Rp {{ number_format($reportData['totalDebitMut'], 2, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono text-rose-800">Rp {{ number_format($reportData['totalCreditMut'], 2, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-mono">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ $reportData['isBalanced'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                            {{ $reportData['isBalanced'] ? 'BALANCED' : 'OUT OF BALANCE' }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <!-- 4. SUB-REPORT: ARUS KAS (CASH FLOW) -->
            @elseif($report_type === 'cash_flow' && !empty($reportData))
                <div class="mt-6 space-y-6 max-w-2xl mx-auto">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 space-y-4">
                        <div class="flex justify-between items-center py-2 text-sm">
                            <span class="text-gray-600">Saldo Awal Kas & Bank</span>
                            <span class="font-mono font-bold text-gray-900">Rp {{ number_format($reportData['openingCash'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 text-sm border-t border-gray-200">
                            <span class="text-emerald-700 font-semibold">+ Total Penerimaan Kas (Cash Inflow)</span>
                            <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($reportData['cashIn'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 text-sm border-t border-gray-200">
                            <span class="text-rose-700 font-semibold">- Total Pengeluaran Kas (Cash Outflow)</span>
                            <span class="font-mono font-bold text-rose-700">Rp {{ number_format($reportData['cashOut'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 text-base border-t-2 border-gray-300 font-bold">
                            <span>Perubahan Kas Bersih (Net Cash Flow)</span>
                            <span class="font-mono {{ $reportData['netChange'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                Rp {{ number_format($reportData['netChange'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-4 px-4 bg-blue-600 text-white rounded-xl text-base font-bold">
                            <span>SALDO AKHIR KAS & BANK</span>
                            <span class="font-mono text-lg">Rp {{ number_format($reportData['closingCash'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Sign-off Audit Section for Print -->
            <div class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-3 gap-6 text-center text-xs text-gray-600">
                <div>
                    <p class="font-semibold text-gray-900">Dibuat Oleh:</p>
                    <div class="h-16"></div>
                    <p class="border-t border-gray-400 mx-8 pt-1">Accounting Officer</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Diperiksa Oleh:</p>
                    <div class="h-16"></div>
                    <p class="border-t border-gray-400 mx-8 pt-1">Accounting Manager</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">Disetujui Oleh:</p>
                    <div class="h-16"></div>
                    <p class="border-t border-gray-400 mx-8 pt-1">Direktur Keuangan / CFO</p>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 6: TUTUP BUKU & LOCK DATE ==================== -->
    @if($activeTab === 'closing')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-sans">
            <!-- Panel 1: Lock Date Settings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center space-x-2 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <h3 class="text-base font-bold text-gray-900">Accounting Lock Date</h3>
                </div>
                <p class="text-xs text-gray-500">Mencegah staf memasukkan atau mengubah jurnal transaksi pada periode lampau yang sudah diaudit.</p>

                <div class="p-3 bg-gray-50 rounded-lg text-xs font-mono">
                    Status Saat Ini: 
                    @if($accounting_lock_date)
                        <strong class="text-rose-600">Terkunci s/d {{ date('d/m/Y', strtotime($accounting_lock_date)) }}</strong>
                    @else
                        <span class="text-gray-500">Tidak ada kunci aktif (Unlocked)</span>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase">Kunci Hingga Tanggal:</label>
                    <input type="date" wire:model="new_lock_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm">
                    @error('new_lock_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase">Catatan Kunci / Alasan:</label>
                    <input type="text" wire:model="lock_note" placeholder="e.g. Audit Interim Q2 2026 Selesai" class="mt-1 block w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm">
                </div>

                <div class="flex gap-2 pt-2">
                    <button wire:click="setAccountingLockDate" class="flex-1 px-4 py-2 bg-amber-600 text-white rounded-lg text-xs font-semibold hover:bg-amber-700 transition cursor-pointer">
                        Aktifkan Kunci
                    </button>
                    @if($accounting_lock_date)
                        <button wire:click="unlockAccountingDate" class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold hover:bg-gray-300 transition cursor-pointer">
                            Lepas Kunci
                        </button>
                    @endif
                </div>
            </div>

            <!-- Panel 2: Execute Fiscal Period Closing -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <div class="flex items-center space-x-2 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 class="text-base font-bold text-gray-900">Tutup Buku Akhir Periode</h3>
                </div>
                <p class="text-xs text-gray-500">Mereset seluruh saldo akun nominal (Pendapatan & Beban) ke nol dan memindahkan saldo laba/rugi bersih ke <strong>Laba Ditahan (Retained Earnings)</strong>.</p>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Tutup Buku:</label>
                    <input type="date" wire:model="closing_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm">
                    @error('closing_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 uppercase">Catatan / Keterangan:</label>
                    <textarea wire:model="closing_note" rows="2" placeholder="e.g. Tutup buku tahunan 2026" class="mt-1 block w-full border border-gray-300 rounded-lg py-1.5 px-3 text-sm"></textarea>
                </div>

                <div class="pt-2">
                    <button wire:click="executeClosing" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-xs font-bold rounded-lg text-white bg-rose-600 hover:bg-rose-700 transition cursor-pointer uppercase tracking-wider">
                        Eksekusi Tutup Buku
                    </button>
                </div>
            </div>

            <!-- Panel 3: Period Closing History -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-base font-bold text-gray-900">Riwayat Penutupan Buku</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Status</th>
                                <th class="py-2.5 px-3">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($closings as $c)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2.5 px-3 font-mono font-bold text-gray-900">{{ $c->closing_date }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-800">
                                            {{ $c->status }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-600 truncate max-w-[150px]">{{ $c->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-gray-400">Belum ada riwayat tutup buku.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== TAB 7: FIXED ASSETS ==================== -->
    @if($activeTab === 'assets')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Kode Aset</th>
                            <th class="py-3.5 px-6">Nama Aset</th>
                            <th class="py-3.5 px-6">Kategori</th>
                            <th class="py-3.5 px-6">Tgl Perolehan</th>
                            <th class="py-3.5 px-6 text-right">Harga Perolehan</th>
                            <th class="py-3.5 px-6 text-right">Masa Manfaat</th>
                            <th class="py-3.5 px-6 text-right">Nilai Buku</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($assets as $asset)
                            @php
                                $totalDep = $asset->depreciations->sum('amount');
                                $bookVal = $asset->purchase_price - $totalDep;
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-bold">{{ $asset->asset_code }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $asset->asset_name }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $asset->category }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $asset->purchase_date }}</td>
                                <td class="py-4 px-6 text-right font-mono font-medium text-gray-900">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-500">{{ $asset->useful_life_months }} bln</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-emerald-600">Rp {{ number_format($bookVal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">Belum ada aset tetap tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($assets->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ==================== MODAL 1: MULTI-LINE COMPOUND JOURNAL ENTRY ==================== -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 font-display">Input Bukti Jurnal Akuntansi (Multi-Line)</h3>
                            <p class="text-xs text-gray-500">Mendukung multi-baris debit dan kredit dengan validasi seimbang otomatis (Double-Entry Engine).</p>
                        </div>
                        <button type="button" wire:click="closeModal" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Journal Form Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Tanggal Transaksi</label>
                            <input type="date" wire:model="journal_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:ring-blue-500">
                            @error('journal_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Tipe Jurnal</label>
                            <select wire:model="journal_type" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white">
                                <option value="general">General Journal (Umum)</option>
                                <option value="adjustment">Adjusting Journal (Penyesuaian)</option>
                                <option value="closing">Closing Journal (Penutup)</option>
                            </select>
                            @error('journal_type') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Deskripsi / Memo Umum</label>
                            <input type="text" wire:model="description" placeholder="e.g. Pembayaran Gaji & PPh 21, Setoran Modal" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm">
                            @error('description') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Dynamic Spreadsheet Grid Table -->
                    <div class="mt-6 border border-gray-200 rounded-xl overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 font-semibold uppercase">
                                    <th class="py-2.5 px-3 text-center w-10">#</th>
                                    <th class="py-2.5 px-3 text-left w-64">Akun COA</th>
                                    <th class="py-2.5 px-3 text-left">Memo / Rincian Baris</th>
                                    <th class="py-2.5 px-3 text-right w-36">Debit (Rp)</th>
                                    <th class="py-2.5 px-3 text-right w-36">Kredit (Rp)</th>
                                    <th class="py-2.5 px-3 text-center w-12">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($journal_items as $index => $item)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="py-2 px-3 text-center text-gray-400 font-mono">{{ $index + 1 }}</td>
                                        <td class="py-2 px-3">
                                            <select wire:model="journal_items.{{ $index }}.account_id" class="w-full border border-gray-300 rounded-lg py-1.5 px-2 text-xs bg-white focus:ring-1 focus:ring-blue-500">
                                                <option value="">-- Pilih Akun --</option>
                                                @foreach($allAccounts as $acc)
                                                    <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                                @endforeach
                                            </select>
                                            @error("journal_items.{$index}.account_id") <span class="text-rose-500 text-[10px] block mt-0.5">Wajib dipilih</span> @enderror
                                        </td>
                                        <td class="py-2 px-3">
                                            <input type="text" wire:model="journal_items.{{ $index }}.memo" placeholder="Memo spesifik baris ini..." class="w-full border border-gray-300 rounded-lg py-1.5 px-2 text-xs">
                                        </td>
                                        <td class="py-2 px-3">
                                            <input type="number" step="any" wire:model.live.debounce.300ms="journal_items.{{ $index }}.debit" placeholder="0" class="w-full border border-gray-300 rounded-lg py-1.5 px-2 text-xs text-right font-mono focus:border-emerald-500">
                                        </td>
                                        <td class="py-2 px-3">
                                            <input type="number" step="any" wire:model.live.debounce.300ms="journal_items.{{ $index }}.credit" placeholder="0" class="w-full border border-gray-300 rounded-lg py-1.5 px-2 text-xs text-right font-mono focus:border-rose-500">
                                        </td>
                                        <td class="py-2 px-3 text-center">
                                            <button type="button" wire:click="removeJournalRow({{ $index }})" class="text-gray-400 hover:text-rose-600 p-1 cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Add Row Button -->
                    <div class="mt-3">
                        <button type="button" wire:click="addJournalRow" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition cursor-pointer">
                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            + Tambah Baris Transaksi
                        </button>
                    </div>

                    <!-- Sticky Real-Time Balance Status Panel -->
                    <div class="mt-6 p-4 rounded-xl border flex flex-col sm:flex-row items-center justify-between gap-4 {{ $this->isBalanced ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                        <div class="flex items-center gap-6 text-xs font-mono">
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-sans">Total Debit:</span>
                                <strong class="text-sm font-bold text-emerald-700">Rp {{ number_format($this->totalDebit, 2, ',', '.') }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-sans">Total Kredit:</span>
                                <strong class="text-sm font-bold text-rose-700">Rp {{ number_format($this->totalCredit, 2, ',', '.') }}</strong>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-sans">Selisih (Out of Balance):</span>
                                <strong class="text-sm font-bold {{ $this->balanceDiff < 0.01 ? 'text-gray-500' : 'text-rose-600' }}">
                                    Rp {{ number_format($this->balanceDiff, 2, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                        <div>
                            @if($this->isBalanced)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    BALANCED (SEIMBANG)
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white">
                                    BELUM SEIMBANG (Δ ≠ 0)
                                </span>
                            @endif
                        </div>
                    </div>

                    @error('journal_balance')
                        <p class="text-rose-600 text-xs mt-2 font-semibold">{{ $message }}</p>
                    @enderror

                    <!-- Modal Actions -->
                    <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">Batal</button>
                        <button type="button" wire:click="storeJournal" {{ !$this->isBalanced ? 'disabled' : '' }} class="px-5 py-2 text-sm font-bold text-white rounded-lg transition {{ $this->isBalanced ? 'bg-blue-600 hover:bg-blue-700 shadow-md cursor-pointer' : 'bg-gray-300 cursor-not-allowed opacity-60' }}">
                            Posting Bukti Jurnal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== MODAL 2: VOUCHER VIEW & VOID / REVERSING ENTRY ==================== -->
    @if($isOpenVoucherModal)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" aria-hidden="true" wire:click="closeVoucherModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                        <div>
                            <span class="text-xs uppercase tracking-wider font-bold text-gray-400">Bukti Jurnal Transaksi</span>
                            <h3 class="text-xl font-bold font-mono text-blue-600">{{ $voucher_header['reference_number'] ?? '' }}</h3>
                        </div>
                        <button type="button" wire:click="closeVoucherModal" class="text-gray-400 hover:text-gray-600 p-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Voucher Meta -->
                    <div class="grid grid-cols-2 gap-4 py-4 text-xs">
                        <div>
                            <span class="text-gray-500 block">Tanggal:</span>
                            <span class="font-bold text-gray-900">{{ $voucher_header['transaction_date'] ?? '' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Tipe Jurnal:</span>
                            <span class="font-bold uppercase text-blue-700">{{ $voucher_header['journal_type'] ?? '' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 block">Keterangan:</span>
                            <span class="text-gray-800">{{ $voucher_header['description'] ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Voucher Line Items Table -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden mt-2">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50 text-gray-600 font-bold uppercase">
                                <tr>
                                    <th class="py-2 px-3 text-left">Akun COA</th>
                                    <th class="py-2 px-3 text-left">Keterangan Baris</th>
                                    <th class="py-2 px-3 text-right">Debit (Rp)</th>
                                    <th class="py-2 px-3 text-right">Kredit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($voucher_items as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2.5 px-3 font-semibold text-gray-900">
                                            {{ $item->account->code }} - {{ $item->account->name }}
                                        </td>
                                        <td class="py-2.5 px-3 text-gray-600">{{ $item->description }}</td>
                                        <td class="py-2.5 px-3 text-right font-mono text-emerald-600 font-semibold">
                                            {{ $item->type === 'debit' ? number_format($item->amount, 2, ',', '.') : '-' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-mono text-rose-600 font-semibold">
                                            {{ $item->type === 'credit' ? number_format($item->amount, 2, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-100 font-bold">
                                    <td colspan="2" class="py-2.5 px-3 text-right uppercase">TOTAL</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-emerald-700">Rp {{ number_format($voucher_header['total_debit'] ?? 0, 2, ',', '.') }}</td>
                                    <td class="py-2.5 px-3 text-right font-mono text-rose-700">Rp {{ number_format($voucher_header['total_credit'] ?? 0, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Void / Reversing Entry Action Panel -->
                    @if(!($voucher_header['is_reversing'] ?? false))
                        @if($show_void_confirmation)
                            <div class="mt-6 p-4 rounded-xl bg-rose-50 border border-rose-200 space-y-3">
                                <h4 class="text-sm font-bold text-rose-900">Konfirmasi Pembalikan Jurnal (Void Entry)</h4>
                                <p class="text-xs text-rose-700">Sistem akan secara otomatis membuat Jurnal Pembalik (Reversing Entry) dengan posisi debit dan kredit dibalik untuk membatalkan dampak saldo akun ini tanpa menghapus jejak audit.</p>

                                <div>
                                    <label class="block text-xs font-semibold text-rose-900 uppercase">Alasan Pembatalan / Void:</label>
                                    <input type="text" wire:model="void_reason" placeholder="e.g. Salah input nominal akun, faktur dibatalkan" class="mt-1 block w-full border border-rose-300 rounded-lg py-1.5 px-3 text-xs bg-white">
                                </div>

                                <div class="flex justify-end gap-2 pt-2">
                                    <button type="button" wire:click="cancelVoidVoucher" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">Batal</button>
                                    <button type="button" wire:click="executeVoidVoucher" class="px-4 py-1.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg cursor-pointer">Eksekusi Jurnal Pembalik</button>
                                </div>
                            </div>
                        @else
                            <div class="mt-6 flex justify-between items-center pt-4 border-t border-gray-200">
                                <button type="button" wire:click="confirmVoidVoucher" class="inline-flex items-center px-3 py-1.5 bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 rounded-lg text-xs font-semibold cursor-pointer">
                                    <svg class="w-4 h-4 mr-1 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Void / Buat Jurnal Pembalik
                                </button>
                                <button type="button" wire:click="closeVoucherModal" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer">Tutup</button>
                            </div>
                        @endif
                    @else
                        <div class="mt-6 flex justify-between items-center pt-4 border-t border-gray-200">
                            <span class="text-xs text-gray-400 italic">Ini adalah bukti jurnal pembalik otomatis (Reversing Entry).</span>
                            <button type="button" wire:click="closeVoucherModal" class="px-4 py-2 text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer">Tutup</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- ==================== MODAL 3: ASSET REGISTER MODAL ==================== -->
    @if($isOpenAssetModal)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 transition-opacity" aria-hidden="true" wire:click="$set('isOpenAssetModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl px-6 pt-5 pb-6 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 font-display">Registrasi Aset Tetap Baru</h3>
                        <button type="button" wire:click="$set('isOpenAssetModal', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Kode Aset</label>
                            <input type="text" wire:model="asset_code" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-gray-50 font-mono" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Nama Aset</label>
                            <input type="text" wire:model="asset_name" placeholder="e.g. MacBook Pro M3, Mesin Cetak Offset" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm">
                            @error('asset_name') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase">Kategori</label>
                            <select wire:model="asset_category" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm bg-white">
                                <option value="Electronics">Electronics & IT</option>
                                <option value="Vehicles">Vehicles & Transport</option>
                                <option value="Machinery">Machinery & Factory Equipment</option>
                                <option value="Buildings">Buildings & Real Estate</option>
                                <option value="Equipment">Office Equipment</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Tgl Perolehan</label>
                                <input type="date" wire:model="asset_purchase_date" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm">
                                @error('asset_purchase_date') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Harga Perolehan (Rp)</label>
                                <input type="number" wire:model="asset_purchase_price" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm">
                                @error('asset_purchase_price') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Masa Manfaat (Bulan)</label>
                                <input type="number" wire:model="asset_useful_life" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase">Nilai Residu (Rp)</label>
                                <input type="number" wire:model="asset_residual_value" class="mt-1 block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3 pt-3 border-t border-gray-200">
                        <button type="button" wire:click="$set('isOpenAssetModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">Batal</button>
                        <button type="button" wire:click="storeAsset" class="px-5 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 cursor-pointer">Simpan Aset</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
