<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Akuntansi & Sistem Informasi Akuntansi (SIA)</h1>
            <p class="text-sm text-gray-500 mt-1 font-sans">Manage accounts, double-entry journals, detailed ledgers, period closing, and fixed assets depreciation.</p>
        </div>
        @if($activeTab === 'assets')
            <div class="flex space-x-2">
                <button wire:click="runDepreciation" class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 active:bg-emerald-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Run Depreciation
                </button>
                <button wire:click="createAsset" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Register Asset
                </button>
            </div>
        @elseif($activeTab === 'journals')
            <button wire:click="createJournal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Post Journal Entry
            </button>
        @endif
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 font-sans" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 font-sans" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'dashboard')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'dashboard' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Analytics Dashboard
            </button>
            <button wire:click="$set('activeTab', 'coa')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'coa' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Chart of Accounts
            </button>
            <button wire:click="$set('activeTab', 'journals')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'journals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Journal Ledger
            </button>
            <button wire:click="$set('activeTab', 'ledger_detail')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'ledger_detail' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Buku Besar Detail
            </button>
            <button wire:click="$set('activeTab', 'closing')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'closing' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Tutup Buku
            </button>
            <button wire:click="$set('activeTab', 'assets')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'assets' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Fixed Assets
            </button>
        </nav>
    </div>

    @if($activeTab === 'dashboard')
        <!-- Summary KPI Cards -->
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
                    <span class="text-emerald-500 font-semibold inline-flex items-center mr-1">
                        <svg class="w-3.5 h-3.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L10 10.586 13.586 7H12z" clip-rule="evenodd"></path></svg>
                        100%
                    </span>
                    <span>Total kepemilikan aset perusahaan</span>
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
                    <span class="text-amber-500 font-semibold inline-flex items-center mr-1">
                        Hutang & Kewajiban
                    </span>
                    <span>Kewajiban bayar lancar & panjang</span>
                </div>
            </div>

            <!-- Total Equity -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Modal (Ekuitas)</span>
                        <h3 class="text-2xl font-bold font-display text-gray-900 mt-2">Rp {{ number_format($stats['totalEquity'], 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-3 rounded-lg bg-purple-50 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span>Modal awal & laba ditahan</span>
                </div>
            </div>

            <!-- Net Profit/Loss -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Laba / Rugi Bersih</span>
                        <h3 class="text-2xl font-bold font-display mt-2 {{ $stats['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            Rp {{ number_format($stats['netProfit'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-3 rounded-lg {{ $stats['netProfit'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-xs text-gray-500">
                    <span class="text-gray-400">Pemasukan: </span>
                    <span class="font-mono text-gray-800 ml-1 mr-2">Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</span>
                    <span class="text-gray-400">Pengeluaran: </span>
                    <span class="font-mono text-gray-800 ml-1">Rp {{ number_format($stats['totalExpenses'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-sans">
            <!-- Chart 1: Revenue vs Expense SVG Chart -->
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 font-display">Perbandingan Pemasukan & Pengeluaran</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Analisis visual proporsi biaya perusahaan.</p>
                    </div>
                    <div class="flex space-x-3 text-xs">
                        <span class="flex items-center"><span class="w-3 h-3 bg-blue-500 rounded mr-1"></span> Pemasukan</span>
                        <span class="flex items-center"><span class="w-3 h-3 bg-rose-500 rounded mr-1"></span> Pengeluaran</span>
                    </div>
                </div>
                
                @php
                    $maxAmount = max($stats['totalIncome'], $stats['totalExpenses'], 1);
                    $incomeHeight = ($stats['totalIncome'] / $maxAmount) * 200;
                    $expenseHeight = ($stats['totalExpenses'] / $maxAmount) * 200;
                @endphp

                <!-- SVG Chart representation -->
                <div class="h-64 flex items-end justify-between px-4 pb-2 border-b border-l border-gray-200 relative pt-8">
                    <!-- Grid background lines -->
                    <div class="absolute inset-x-0 top-1/4 border-t border-gray-100"></div>
                    <div class="absolute inset-x-0 top-2/4 border-t border-gray-100"></div>
                    <div class="absolute inset-x-0 top-3/4 border-t border-gray-100"></div>
                    
                    <!-- Pemasukan Bar -->
                    <div class="flex flex-col items-center w-1/3 z-10">
                        <div class="w-16 bg-gradient-to-t from-blue-600 to-blue-400 rounded-t-lg shadow-md transition-all duration-500 hover:opacity-90 animate-pulse" style="height: {{ max($incomeHeight, 15) }}px"></div>
                        <span class="text-[10px] font-semibold text-gray-500 mt-2">Pemasukan</span>
                        <span class="text-xs font-mono font-bold text-gray-700">Rp {{ number_format($stats['totalIncome'], 0, ',', '.') }}</span>
                    </div>

                    <!-- Pengeluaran Bar -->
                    <div class="flex flex-col items-center w-1/3 z-10">
                        <div class="w-16 bg-gradient-to-t from-rose-600 to-rose-400 rounded-t-lg shadow-md transition-all duration-500 hover:opacity-90" style="height: {{ max($expenseHeight, 15) }}px"></div>
                        <span class="text-[10px] font-semibold text-gray-500 mt-2">Pengeluaran</span>
                        <span class="text-xs font-mono font-bold text-gray-700">Rp {{ number_format($stats['totalExpenses'], 0, ',', '.') }}</span>
                    </div>

                    <!-- Net Income Summary -->
                    <div class="flex flex-col items-center w-1/3 z-10 justify-end h-full">
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg text-center w-full max-w-[150px]">
                            <span class="text-[10px] font-semibold text-gray-400 block uppercase">Net Profit Margin</span>
                            <span class="text-lg font-bold font-display {{ $stats['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                @if($stats['totalIncome'] > 0)
                                    {{ number_format(($stats['netProfit'] / $stats['totalIncome']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chart 2: Cash Flow Trend (Quick Ledger Balance Gauge) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm space-y-4 font-sans">
                <div class="border-b border-gray-100 pb-4">
                    <h4 class="text-sm font-bold text-gray-900 font-display">Likuiditas & Arus Kas</h4>
                    <p class="text-xs text-gray-400 mt-0.5">Rasio dan kesiapan kas perusahaan.</p>
                </div>
                
                <div class="space-y-4">
                    <!-- Cash & Bank Indicator -->
                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-500 mb-1">
                            <span>Saldo Kas & Bank</span>
                            <span class="font-mono text-gray-900 font-bold">Rp {{ number_format($stats['cashBankBalance'], 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2.5 rounded-full overflow-hidden">
                            @php
                                $liquidRatio = $stats['totalAssets'] > 0 ? ($stats['cashBankBalance'] / $stats['totalAssets']) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-full rounded-full" style="width: {{ min($liquidRatio, 100) }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 block">Rasio likuiditas aset: {{ number_format($liquidRatio, 1) }}%</span>
                    </div>

                    <!-- AR vs AP balance indicator -->
                    <div class="pt-2 border-t border-gray-50">
                        <h5 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2.5">Perbandingan AR vs AP</h5>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div class="p-2.5 bg-emerald-50/50 border border-emerald-100 rounded-lg animate-pulse">
                                <span class="text-[10px] text-emerald-800 uppercase block font-medium">Piutang (AR)</span>
                                <span class="text-sm font-mono font-bold text-emerald-700">Rp {{ number_format($stats['receivablesBalance'], 0, ',', '.') }}</span>
                            </div>
                            <div class="p-2.5 bg-rose-50/50 border border-rose-100 rounded-lg">
                                <span class="text-[10px] text-rose-800 uppercase block font-medium">Hutang (AP)</span>
                                <span class="text-sm font-mono font-bold text-rose-700">Rp {{ number_format($stats['payablesBalance'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Net Working Capital -->
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-center">
                        <span class="text-[10px] text-gray-400 uppercase font-semibold block">Modal Kerja Bersih (AR - AP)</span>
                        <span class="text-md font-mono font-bold text-gray-800">
                            Rp {{ number_format($stats['receivablesBalance'] - $stats['payablesBalance'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIA Modules breakdown -->
        <div>
            <div class="border-b border-gray-100 pb-3 mb-5">
                <h3 class="text-lg font-bold text-gray-900 font-display">Integrasi Sistem Informasi Akuntansi (SIA)</h3>
                <p class="text-xs text-gray-400 mt-0.5">Rincian parameter operasional dan modul SIA pembentuk laporan keuangan.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 font-sans">
                <!-- Modul 1: Kas & Bank -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between hover:border-blue-300 transition duration-200">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 font-display">Modul Kas & Bank</h4>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between"><span>Akun Bank Terdaftar:</span><span class="font-bold text-gray-800">{{ $stats['bankAccountsCount'] }} akun</span></div>
                            <div class="flex justify-between"><span>Volume Transaksi:</span><span class="font-bold text-gray-800">{{ $stats['cashTransactionsCount'] }} trx</span></div>
                            <div class="flex justify-between"><span>Arus Kas Masuk:</span><span class="font-bold text-emerald-600">Rp {{ number_format($stats['cashTransactionsTotal'], 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                    <a href="{{ route('cash-bank') }}" class="mt-5 inline-flex items-center justify-center py-2 px-3 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition duration-150">
                        Buka Modul Kas & Bank
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Modul 2: Piutang Usaha (AR) -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between hover:border-emerald-300 transition duration-200">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 font-display">Modul Piutang (AR)</h4>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between"><span>Outstanding AR:</span><span class="font-bold text-gray-800">Rp {{ number_format($stats['receivablesBalance'], 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span>Penerimaan Pembayaran:</span><span class="font-bold text-gray-800">{{ $stats['receiptsCount'] }} kwitansi</span></div>
                            <div class="flex justify-between"><span>Volume Penerimaan:</span><span class="font-bold text-emerald-600">Rp {{ number_format($stats['receiptsTotal'], 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                    <a href="{{ route('accounts-receivable') }}" class="mt-5 inline-flex items-center justify-center py-2 px-3 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition duration-150">
                        Buka Modul Piutang (AR)
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Modul 3: Hutang Usaha (AP) -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between hover:border-rose-300 transition duration-200">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-rose-50 text-rose-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 font-display">Modul Hutang (AP)</h4>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between"><span>Outstanding AP:</span><span class="font-bold text-gray-800">Rp {{ number_format($stats['payablesBalance'], 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span>Rencana Pembayaran (Pending):</span><span class="font-bold text-amber-600">{{ $stats['pendingSchedulesCount'] }} PO (Rp {{ number_format($stats['pendingSchedulesTotal'], 0, ',', '.') }})</span></div>
                            <div class="flex justify-between"><span>Disbursements Terbayar:</span><span class="font-bold text-rose-600">Rp {{ number_format($stats['disbursementsTotal'], 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                    <a href="{{ route('accounts-payable') }}" class="mt-5 inline-flex items-center justify-center py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold rounded-lg transition duration-150">
                        Buka Modul Hutang (AP)
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Modul 4: Taxes & e-Faktur -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between hover:border-amber-300 transition duration-200">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 font-display">Taxes & e-Faktur</h4>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between"><span>Jumlah Faktur Pajak:</span><span class="font-bold text-gray-800">{{ $stats['taxInvoicesCount'] }} faktur</span></div>
                            <div class="flex justify-between"><span>PPN Masukan (Kredit):</span><span class="font-bold text-emerald-600">Rp {{ number_format($stats['taxPPNMasukan'], 0, ',', '.') }}</span></div>
                            <div class="flex justify-between"><span>PPN Keluaran (Hutang):</span><span class="font-bold text-rose-600">Rp {{ number_format($stats['taxPPNKeluaran'], 0, ',', '.') }}</span></div>
                        </div>
                    </div>
                    <a href="{{ route('taxes') }}" class="mt-5 inline-flex items-center justify-center py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-semibold rounded-lg transition duration-150">
                        Buka Manajemen Pajak
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Modul 5: Budgeting -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between hover:border-purple-300 transition duration-200">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 font-display">Budgeting & Pengendalian</h4>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between"><span>Rencana Anggaran Aktif:</span><span class="font-bold text-gray-800">{{ $stats['budgetsCount'] }} divisi</span></div>
                            <div class="flex justify-between"><span>Total Plafond Anggaran:</span><span class="font-bold text-indigo-600">Rp {{ number_format($stats['totalPlannedBudget'], 0, ',', '.') }}</span></div>
                            
                            <!-- Budget limit indicator -->
                            <div class="pt-1.5">
                                <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    @php
                                        $budgetUtil = $stats['totalPlannedBudget'] > 0 ? min(($stats['totalExpenses'] / $stats['totalPlannedBudget']) * 100, 100) : 0;
                                    @endphp
                                    <div class="h-full rounded-full {{ $budgetUtil > 90 ? 'bg-rose-500' : ($budgetUtil > 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $budgetUtil }}%"></div>
                                </div>
                                <span class="text-[9px] text-gray-400 mt-1 block">Pemakaian Anggaran (Biaya/Budget): {{ number_format($budgetUtil, 1) }}%</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('budgets') }}" class="mt-5 inline-flex items-center justify-center py-2 px-3 bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold rounded-lg transition duration-150">
                        Buka Manajemen Anggaran
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Modul 6: Workflow & Approvals -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-between hover:border-cyan-300 transition duration-200">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-cyan-50 text-cyan-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 font-display">Workflow & Approvals</h4>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between"><span>Menunggu Persetujuan:</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $stats['pendingApprovalsCount'] > 0 ? 'bg-amber-100 text-amber-800 animate-pulse' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $stats['pendingApprovalsCount'] }} dokumen
                                </span>
                            </div>
                            <div class="flex justify-between"><span>Audit Trail:</span><span class="font-bold text-gray-800">Aktif</span></div>
                            <div class="flex justify-between"><span>Sistem Kontrol Intern:</span><span class="font-bold text-emerald-600">Terbuka</span></div>
                        </div>
                    </div>
                    <a href="{{ route('approvals') }}" class="mt-5 inline-flex items-center justify-center py-2 px-3 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 text-xs font-semibold rounded-lg transition duration-150">
                        Buka Workflow Approval
                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Ledger Activity & Logs -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden font-sans">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h4 class="text-sm font-bold text-gray-900 font-display">Aktifitas Jurnal Ledger Terbaru</h4>
                    <p class="text-xs text-gray-400 mt-0.5">Entri jurnal akuntansi double-entry yang baru saja diposting.</p>
                </div>
                <button wire:click="$set('activeTab', 'journals')" class="text-xs text-blue-600 font-semibold hover:underline">Lihat Semua Ledger</button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs text-left">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 font-semibold border-b border-gray-200">
                            <th class="py-3 px-6">No Referensi</th>
                            <th class="py-3 px-6">Tanggal</th>
                            <th class="py-3 px-6">Nama Akun</th>
                            <th class="py-3 px-6">Keterangan</th>
                            <th class="py-3 px-6 text-right">Debit</th>
                            <th class="py-3 px-6 text-right">Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stats['recentTransactions'] as $trx)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-3.5 px-6 font-mono font-bold text-blue-600">{{ $trx->reference_number }}</td>
                                <td class="py-3.5 px-6 text-gray-500">{{ $trx->transaction_date }}</td>
                                <td class="py-3.5 px-6">
                                    <span class="font-medium text-gray-900 block">{{ $trx->account->name }}</span>
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $trx->account->code }}</span>
                                </td>
                                <td class="py-3.5 px-6 text-gray-600 truncate max-w-[200px]" title="{{ $trx->description }}">{{ $trx->description }}</td>
                                <td class="py-3.5 px-6 text-right font-mono font-semibold {{ $trx->type === 'debit' ? 'text-gray-900' : 'text-gray-300' }}">
                                    {{ $trx->type === 'debit' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3.5 px-6 text-right font-mono font-semibold {{ $trx->type === 'credit' ? 'text-gray-900' : 'text-gray-300' }}">
                                    {{ $trx->type === 'credit' ? 'Rp ' . number_format($trx->amount, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-400">Tidak ada aktifitas jurnal terbaru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab === 'coa')
        <!-- Search Accounts -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4 font-sans">
            <div class="flex-1 max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search accounts by code or name...">
            </div>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Account Code</th>
                            <th class="py-3.5 px-6">Account Name</th>
                            <th class="py-3.5 px-6">Type</th>
                            <th class="py-3.5 px-6 text-right">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($accounts as $acc)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $acc->code }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $acc->name }}</td>
                                <td class="py-4 px-6 text-gray-500">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($acc->type) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono font-medium text-gray-950">Rp {{ number_format($acc->balance, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">No accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'journals')
        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 font-sans">
            <div class="flex space-x-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Journal Type</label>
                    <select wire:model.live="filter_journal_type" class="mt-1 block w-48 border border-gray-300 rounded-md py-1.5 px-3 text-sm bg-white">
                        <option value="">-- All Types --</option>
                        <option value="general">General Ledger</option>
                        <option value="adjustment">Adjusting Entry</option>
                        <option value="closing">Closing Entry</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Journals Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Reference</th>
                            <th class="py-3.5 px-6">Date</th>
                            <th class="py-3.5 px-6">Account</th>
                            <th class="py-3.5 px-6">Description</th>
                            <th class="py-3.5 px-6 text-center">Type</th>
                            <th class="py-3.5 px-6 text-right">Debit</th>
                            <th class="py-3.5 px-6 text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($journals as $j)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $j->reference_number }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $j->transaction_date }}</td>
                                <td class="py-4 px-6 font-semibold text-gray-900">{{ $j->account->code }} - {{ $j->account->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $j->description }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        {{ $j->journal_type === 'closing' ? 'bg-red-100 text-red-800' : ($j->journal_type === 'adjustment' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                        {{ ucfirst($j->journal_type ?: 'general') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-600 font-semibold">{{ $j->type === 'debit' ? 'Rp '.number_format($j->amount, 0, ',', '.') : '-' }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-600 font-semibold">{{ $j->type === 'credit' ? 'Rp '.number_format($j->amount, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">No journal ledger entries found.</td>
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

    @elseif($activeTab === 'ledger_detail')
        <!-- Account and Date filter -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 font-sans space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Select Account</label>
                    <select wire:model.live="selected_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white">
                        <option value="">-- Choose Account --</option>
                        @foreach($allAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">Start Date</label>
                    <input type="date" wire:model.live="ledger_start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase">End Date</label>
                    <input type="date" wire:model.live="ledger_end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                </div>
            </div>
        </div>

        @if($selected_account_id)
            <!-- Ledger Detail view -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Buku Besar: {{ $allAccounts->find($selected_account_id)->name }}</h3>
                        <p class="text-sm text-gray-500 font-mono">Code: {{ $allAccounts->find($selected_account_id)->code }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Opening Balance:</span>
                        <div class="text-lg font-bold text-gray-900 font-mono">Rp {{ number_format($openingBalance, 2, ',', '.') }}</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3.5 px-6">Date</th>
                                <th class="py-3.5 px-6">Ref / Journal No</th>
                                <th class="py-3.5 px-6">Description</th>
                                <th class="py-3.5 px-6 text-right">Debit</th>
                                <th class="py-3.5 px-6 text-right">Credit</th>
                                <th class="py-3.5 px-6 text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <!-- Opening row -->
                            <tr class="bg-blue-50 bg-opacity-30">
                                <td class="py-4 px-6 text-gray-400 font-medium" colspan="3">Opening Balance</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-900 font-bold">Rp {{ number_format($openingBalance, 2, ',', '.') }}</td>
                            </tr>
                            @forelse($ledgerEntries as $entry)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-4 px-6 text-gray-500">{{ $entry->transaction_date }}</td>
                                    <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $entry->reference_number }}</td>
                                    <td class="py-4 px-6 text-gray-700">{{ $entry->description }}</td>
                                    <td class="py-4 px-6 text-right font-mono text-emerald-600 font-semibold">
                                        {{ $entry->type === 'debit' ? 'Rp '.number_format($entry->amount, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-red-600 font-semibold">
                                        {{ $entry->type === 'credit' ? 'Rp '.number_format($entry->amount, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-4 px-6 text-right font-mono text-gray-900 font-bold">
                                        Rp {{ number_format($entry->running_balance, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">No postings in this period.</td>
                                </tr>
                            @endforelse
                            <!-- Closing row -->
                            <tr class="bg-emerald-50 bg-opacity-30">
                                <td class="py-4 px-6 text-gray-900 font-bold" colspan="3">Closing Balance</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-gray-400">-</td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-700 font-bold">Rp {{ number_format($closingBalance, 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center text-gray-500 font-sans">
                Please select an account above to view details.
            </div>
        @endif

    @elseif($activeTab === 'closing')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-sans">
            <!-- Tutup Buku form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4 lg:col-span-1">
                <h3 class="text-lg font-bold text-gray-900">Execute Period Closing</h3>
                <p class="text-sm text-gray-500">Closing nominal accounts (revenues & expenses) zeroing them out and transferring the net balance to Laba Ditahan (Retained Earnings).</p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Closing Date</label>
                    <input type="date" wire:model="closing_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('closing_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Closing Note / Remarks</label>
                    <textarea wire:model="closing_note" rows="3" placeholder="e.g. Closing Q2 2026" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                    @error('closing_note') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button wire:click="executeClosing" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none font-semibold cursor-pointer">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Close Fiscal Period
                    </button>
                </div>
            </div>

            <!-- Tutup Buku History -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4 lg:col-span-2">
                <h3 class="text-lg font-bold text-gray-900">Period Closing History</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3.5 px-6">Closing Date</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Closed By</th>
                                <th class="py-3.5 px-6">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($closings as $c)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-4 px-6 font-mono text-gray-900 font-semibold">{{ $c->closing_date }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">UID: {{ $c->closed_by }}</td>
                                    <td class="py-4 px-6 text-gray-600">{{ $c->notes }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-gray-500">No periods closed yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @else
        <!-- Fixed Assets Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Asset Code</th>
                            <th class="py-3.5 px-6">Asset Name</th>
                            <th class="py-3.5 px-6 text-right">Cost Price</th>
                            <th class="py-3.5 px-6 text-right">Salvage Value</th>
                            <th class="py-3.5 px-6 text-center">Useful Life (Yrs)</th>
                            <th class="py-3.5 px-6 text-right">Current Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($assets as $asset)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $asset->asset_code }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $asset->asset_name }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($asset->purchase_price, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($asset->residual_value, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">{{ $asset->useful_life_months }} mo ({{ round($asset->useful_life_months / 12, 1) }} yrs)</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">
                                    @php
                                    $dep = $asset->depreciations->sum('amount');
                                    $current = $asset->purchase_price - $dep;
                                    @endphp
                                    Rp {{ number_format($current, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">No assets recorded.</td>
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

    <!-- Journal Post Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">Post Double-Entry Journal Entry</h3>
                        <div class="mt-4 space-y-4">
                            <!-- Date & Journal Type -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date</label>
                                    <input type="date" wire:model="journal_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('journal_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Journal Type</label>
                                    <select wire:model="journal_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="general">General Journal</option>
                                        <option value="adjustment">Adjusting Journal</option>
                                        <option value="closing">Closing Journal</option>
                                    </select>
                                    @error('journal_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <input type="text" wire:model="description" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. Sales cash deposit, office utility payment">
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Debit Account -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Debit Account (+Asset / +Expense)</label>
                                <select wire:model="debit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                                @error('debit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Credit Account -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Credit Account (+Liability / +Equity / +Revenue)</label>
                                <select wire:model="credit_account_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Account</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ ucfirst($acc->type) }})</option>
                                    @endforeach
                                </select>
                                @error('credit_account_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Amount (IDR)</label>
                                <input type="number" wire:model="amount" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('amount') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                        <button type="button" wire:click="storeJournal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">Post Journal</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Asset Register Modal -->
    @if($isOpenAssetModal)
        <div class="fixed inset-0 z-50 overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isOpenAssetModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="$set('isOpenAssetModal', false)" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">Register Fixed Asset</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asset Code</label>
                                <input type="text" wire:model="asset_code" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-gray-50 font-mono" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Asset Name</label>
                                <input type="text" wire:model="asset_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. Server Rack, Delivery Van">
                                @error('asset_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <select wire:model="asset_category" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="Electronics">Electronics & IT</option>
                                    <option value="Vehicles">Vehicles</option>
                                    <option value="Machinery">Machinery & Factory Equipment</option>
                                    <option value="Buildings">Buildings & Real Estate</option>
                                    <option value="Equipment">Office Equipment</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Purchase Date</label>
                                    <input type="date" wire:model="asset_purchase_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_purchase_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Purchase Cost (IDR)</label>
                                    <input type="number" wire:model="asset_purchase_price" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_purchase_price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Useful Life (Months)</label>
                                    <input type="number" wire:model="asset_useful_life" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_useful_life') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Residual Value (IDR)</label>
                                    <input type="number" wire:model="asset_residual_value" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('asset_residual_value') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('isOpenAssetModal', false)" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">Cancel</button>
                        <button type="button" wire:click="storeAsset" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-semibold">Save Asset</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
