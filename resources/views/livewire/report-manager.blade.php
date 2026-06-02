<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Executive Reports</h1>
            <p class="text-sm text-gray-500 mt-1">Review sales performance, purchase expenditures, inventory health, payroll costs, and profit & loss statements.</p>
        </div>
        
        <!-- Print Button -->
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 active:bg-gray-200 focus:outline-none transition ease-in-out duration-150 print:hidden cursor-pointer">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Report
        </button>
    </div>

    <!-- Filters (Date range) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row items-end gap-4 print:hidden">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase">Start Date</label>
            <input type="date" wire:model.live="start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase">End Date</label>
            <input type="date" wire:model.live="end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
        </div>
        <div class="text-xs text-gray-500 pb-2.5">
            * Filters apply to Sales, Purchases, Payroll and Profit & Loss reports.
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display print:hidden">
        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'sales')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'sales' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Sales Report
            </button>
            <button wire:click="$set('activeTab', 'purchases')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'purchases' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Purchases Report
            </button>
            <button wire:click="$set('activeTab', 'stock')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'stock' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Stock & Inventory
            </button>
            <button wire:click="$set('activeTab', 'payroll')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'payroll' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Payroll Report
            </button>
            <button wire:click="$set('activeTab', 'pl')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'pl' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Profit & Loss
            </button>
            <button wire:click="$set('activeTab', 'bs')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'bs' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Balance Sheet
            </button>
            <button wire:click="$set('activeTab', 'cf')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'cf' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Cash Flow
            </button>
            <button wire:click="$set('activeTab', 'tb')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'tb' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Trial Balance
            </button>
            <button wire:click="$set('activeTab', 'aging')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer whitespace-nowrap {{ $activeTab === 'aging' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                AR/AP Aging
            </button>
        </nav>
    </div>

    <!-- Active Tab Report view -->
    @if($activeTab === 'sales')
        <!-- Sales Report View -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat 1 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500 font-display">Total Delivered Invoices</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 font-mono">{{ $sales->count() }}</p>
            </div>
            <!-- Stat 2 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500 font-display">Total Revenue</p>
                <p class="mt-2 text-3xl font-bold text-emerald-600 font-mono">Rp {{ number_format($sales->sum('grand_total'), 0, ',', '.') }}</p>
            </div>
            <!-- Stat 3 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500 font-display">Average Invoice Value</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 font-mono">Rp {{ number_format($sales->avg('grand_total') ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Top Selling Products -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-1">
                <h3 class="text-md font-bold text-gray-900 font-display mb-4">Top 5 Selling Products</h3>
                <div class="space-y-4 font-sans">
                    @forelse($topProducts as $idx => $p)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                            <div>
                                <span class="text-xs text-gray-400 font-mono mr-1">#{{ $idx+1 }}</span>
                                <span class="font-medium text-gray-800 text-sm">{{ $p->name }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs font-semibold text-gray-900">{{ number_format($p->total_qty) }} sold</span>
                                <span class="block text-xs text-gray-500 font-mono">Rp {{ number_format($p->total_revenue, 0) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 italic">No sales recorded for this period.</p>
                    @endforelse
                </div>
            </div>

            <!-- Invoices List -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden lg:col-span-2 font-sans">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-md font-bold text-gray-900 font-display">Delivered Invoices Ledger</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3 px-6">Invoice</th>
                                <th class="py-3 px-6">Customer</th>
                                <th class="py-3 px-6">Date</th>
                                <th class="py-3 px-6 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($sales as $sale)
                                <tr>
                                    <td class="py-3 px-6 font-mono text-blue-600 font-medium">{{ $sale->invoice_number }}</td>
                                    <td class="py-3 px-6 text-gray-800">{{ $sale->customer->name }}</td>
                                    <td class="py-3 px-6 text-gray-500">{{ $sale->sale_date }}</td>
                                    <td class="py-3 px-6 text-right font-mono text-gray-900">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">No invoices.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'purchases')
        <!-- Purchases Report View -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500 font-display">Completed Purchases Count</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 font-mono">{{ $purchases->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500 font-display">Total Expenditures</p>
                <p class="mt-2 text-3xl font-bold text-red-600 font-mono">Rp {{ number_format($purchases->sum('grand_total'), 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-6 font-sans">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-md font-bold text-gray-900 font-display">Received Purchase Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3 px-6">PO Number</th>
                            <th class="py-3 px-6">Supplier</th>
                            <th class="py-3 px-6">Date</th>
                            <th class="py-3 px-6 text-right">Total Cost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($purchases as $p)
                            <tr>
                                <td class="py-3 px-6 font-mono text-blue-600 font-medium">{{ $p->purchase_number }}</td>
                                <td class="py-3 px-6 text-gray-800">{{ $p->supplier->name }}</td>
                                <td class="py-3 px-6 text-gray-500">{{ $p->purchase_date }}</td>
                                <td class="py-3 px-6 text-right font-mono text-gray-900">Rp {{ number_format($p->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-500">No purchases found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'stock')
        <!-- Stock Report View -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Total Stock items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h4 class="text-sm font-semibold text-gray-500 font-display">Total Stock Balances Listed</h4>
                <p class="mt-2 text-3xl font-bold text-gray-900 font-mono">{{ number_format($stocks->sum('qty_on_hand'), 2) }} units</p>
            </div>
            <!-- Low stock alert counts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h4 class="text-sm font-semibold text-gray-500 font-display">Critical Low Stock Alerts</h4>
                <p class="mt-2 text-3xl font-bold text-amber-600 font-mono">{{ $lowStocks->count() }} items</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
            <!-- Low Stock Warnings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-1 font-sans">
                <h3 class="text-md font-bold text-gray-900 font-display mb-4 text-amber-700">Low Stock Warnings</h3>
                <div class="space-y-4">
                    @forelse($lowStocks as $ls)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2 text-xs">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $ls->name }}</span>
                                <span class="block text-gray-400 font-mono">{{ $ls->code }}</span>
                            </div>
                            <div class="text-right">
                                <span class="block font-bold text-red-600">Stock: {{ number_format($ls->stock) }}</span>
                                <span class="block text-gray-400">Min: {{ number_format($ls->min_stock) }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 italic">All products are healthy and above min stock.</p>
                    @endforelse
                </div>
            </div>

            <!-- Stocks per Location -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden lg:col-span-2 font-sans">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-md font-bold text-gray-900 font-display">Warehouse Stock Ledger</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3 px-6">Product</th>
                                <th class="py-3 px-6">Warehouse</th>
                                <th class="py-3 px-6 text-right">Available Qty</th>
                                <th class="py-3 px-6 text-right">On Hand Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($stocks as $stock)
                                <tr>
                                    <td class="py-3 px-6 font-medium text-gray-900">{{ $stock->product->name }}</td>
                                    <td class="py-3 px-6 text-gray-700 font-medium">{{ $stock->warehouse->warehouse_name }}</td>
                                    <td class="py-3 px-6 text-right font-mono font-semibold text-emerald-600">{{ number_format($stock->qty_available, 2) }}</td>
                                    <td class="py-3 px-6 text-right font-mono text-gray-800">{{ number_format($stock->qty_on_hand, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">No stock recorded in warehouses.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($activeTab === 'payroll')
        <!-- Payroll Report View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 font-sans">
            <h3 class="text-md font-bold text-gray-900 font-display mb-4">Total Payroll Costs</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <span class="block text-xs text-gray-500 uppercase">Gross Salary Sum</span>
                    <span class="block text-xl font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($payrolls->sum('basic_salary'), 0) }}</span>
                </div>
                <div class="p-4 bg-emerald-50 rounded-lg">
                    <span class="block text-xs text-emerald-600 uppercase">Total Allowances</span>
                    <span class="block text-xl font-bold font-mono text-emerald-700 mt-1">+Rp {{ number_format($payrolls->sum('allowances'), 0) }}</span>
                </div>
                <div class="p-4 bg-blue-50 rounded-lg">
                    <span class="block text-xs text-blue-600 uppercase">Net Disbursed</span>
                    <span class="block text-xl font-bold font-mono text-blue-700 mt-1">Rp {{ number_format($payrolls->sum('total_salary'), 0) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto border border-gray-100 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3 px-6">Period</th>
                            <th class="py-3 px-6">Employee</th>
                            <th class="py-3 px-6 text-right">Basic Salary</th>
                            <th class="py-3 px-6 text-right">Allowances</th>
                            <th class="py-3 px-6 text-right">Net Salary</th>
                            <th class="py-3 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payrolls as $pr)
                            <tr>
                                <td class="py-3 px-6 font-mono text-gray-800">{{ $pr->period }}</td>
                                <td class="py-3 px-6 font-medium text-gray-900">{{ $pr->employee->name }}</td>
                                <td class="py-3 px-6 text-right font-mono">Rp {{ number_format($pr->basic_salary, 0) }}</td>
                                <td class="py-3 px-6 text-right font-mono text-emerald-600">+Rp {{ number_format($pr->allowances, 0) }}</td>
                                <td class="py-3 px-6 text-right font-mono font-bold text-gray-900">Rp {{ number_format($pr->total_salary, 0) }}</td>
                                <td class="py-3 px-6 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $pr->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($pr->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">No payroll data for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($activeTab === 'pl')
        <!-- Profit & Loss View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-4xl mx-auto font-sans">
            <div class="text-center pb-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold font-display text-gray-900">Profit & Loss Statement</h2>
                <p class="text-sm text-gray-500 mt-1">Period: {{ $start_date }} to {{ $end_date }}</p>
            </div>

            <div class="mt-8 space-y-6">
                <!-- Revenues Section -->
                <div>
                    <div class="flex items-center justify-between text-lg font-bold text-gray-900 border-b border-gray-300 pb-2">
                        <span>REVENUES</span>
                        <span>Rp {{ number_format($pl['revenue'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pl-4 py-2 flex justify-between text-sm text-gray-600 border-b border-gray-100">
                        <span>Sales Revenue (Delivered Invoices)</span>
                        <span>Rp {{ number_format($pl['revenue'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Expenses Section -->
                <div>
                    <div class="flex items-center justify-between text-lg font-bold text-gray-900 border-b border-gray-300 pb-2">
                        <span>COST OF GOODS & OPERATING EXPENSES</span>
                        <span class="text-red-600">(Rp {{ number_format($pl['total_expenses'], 0, ',', '.') }})</span>
                    </div>
                    <div class="pl-4 py-2 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Cost of Goods Sold (Received Purchase Orders)</span>
                            <span>Rp {{ number_format($pl['cogs'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Employee Payroll Costs</span>
                            <span>Rp {{ number_format($pl['payroll'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Other Operating Expenses (Journals Debit)</span>
                            <span>Rp {{ number_format($pl['other_expenses'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Total expenses row -->
                <div class="flex items-center justify-between text-sm font-semibold text-gray-900 bg-gray-50 p-3 rounded">
                    <span>Total Operating Expenses</span>
                    <span>Rp {{ number_format($pl['total_expenses'], 0, ',', '.') }}</span>
                </div>

                <!-- Net Income Section -->
                <div class="pt-6 border-t-2 border-double border-gray-300">
                    <div class="flex items-center justify-between text-xl font-bold {{ $pl['net_profit'] >= 0 ? 'text-emerald-700' : 'text-red-700' }} p-4 bg-gray-50 rounded-lg">
                        <span>NET PROFIT / (LOSS)</span>
                        <span class="font-mono">Rp {{ number_format($pl['net_profit'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'bs')
        <!-- Balance Sheet View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-4xl mx-auto font-sans">
            <div class="text-center pb-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold font-display text-gray-900">Balance Sheet Statement</h2>
                <p class="text-sm text-gray-500 mt-1">As of: {{ $end_date }}</p>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Assets Side -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-lg font-bold text-gray-900 border-b border-gray-300 pb-2">
                        <span>ASSETS</span>
                        <span>Rp {{ number_format($bs['total_assets'], 0, ',', '.') }}</span>
                    </div>
                    <div class="space-y-2">
                        @forelse($bs['assets'] as $assetAcc)
                            <div class="flex justify-between text-sm text-gray-600 border-b border-gray-100 pb-1">
                                <span>{{ $assetAcc->code }} - {{ $assetAcc->name }}</span>
                                <span class="font-mono text-gray-900">Rp {{ number_format($assetAcc->balance, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-500 italic">No asset accounts recorded.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Liabilities & Equity Side -->
                <div class="space-y-6">
                    <!-- Liabilities -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-lg font-bold text-gray-900 border-b border-gray-300 pb-2">
                            <span>LIABILITIES</span>
                            <span>Rp {{ number_format($bs['total_liabilities'], 0, ',', '.') }}</span>
                        </div>
                        <div class="space-y-2">
                            @forelse($bs['liabilities'] as $liabAcc)
                                <div class="flex justify-between text-sm text-gray-600 border-b border-gray-100 pb-1">
                                    <span>{{ $liabAcc->code }} - {{ $liabAcc->name }}</span>
                                    <span class="font-mono text-gray-900">Rp {{ number_format($liabAcc->balance, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 italic">No liability accounts recorded.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Equity -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-lg font-bold text-gray-900 border-b border-gray-300 pb-2">
                            <span>EQUITY</span>
                            <span>Rp {{ number_format($bs['total_equity'], 0, ',', '.') }}</span>
                        </div>
                        <div class="space-y-2">
                            @forelse($bs['equity'] as $eqAcc)
                                <div class="flex justify-between text-sm text-gray-600 border-b border-gray-100 pb-1">
                                    <span>{{ $eqAcc->code }} - {{ $eqAcc->name }}</span>
                                    <span class="font-mono text-gray-900">Rp {{ number_format($eqAcc->balance, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-500 italic">No equity accounts recorded.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Totals comparison section -->
            <div class="mt-8 pt-6 border-t-2 border-double border-gray-300 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center text-md font-bold text-blue-800 p-3 bg-blue-50 rounded">
                    <span>TOTAL ASSETS</span>
                    <span class="font-mono">Rp {{ number_format($bs['total_assets'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-md font-bold text-emerald-800 p-3 bg-emerald-50 rounded">
                    <span>TOTAL LIABILITIES & EQUITY</span>
                    <span class="font-mono">Rp {{ number_format($bs['total_liabilities'] + $bs['total_equity'], 0, ',', '.') }}</span>
                </div>
            </div>
            
            @if(round($bs['total_assets']) !== round($bs['total_liabilities'] + $bs['total_equity']))
                <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-800 text-xs rounded text-center">
                    ⚠️ Balance Sheet is out of balance. Check adjusting entries or starting balances.
                </div>
            @endif
        </div>
    @elseif($activeTab === 'cf')
        <!-- Cash Flow Statement View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-4xl mx-auto font-sans">
            <div class="text-center pb-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold font-display text-gray-900">Cash Flow Statement</h2>
                <p class="text-sm text-gray-500 mt-1">Period: {{ $start_date }} to {{ $end_date }}</p>
            </div>

            <div class="mt-8 space-y-6">
                <!-- Cash Flow from Operating Activities -->
                <div>
                    <div class="flex items-center justify-between text-md font-bold text-gray-900 border-b border-gray-300 pb-2">
                        <span>CASH FLOW FROM OPERATING ACTIVITIES</span>
                        <span class="{{ $cf['net_operating'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($cf['net_operating'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pl-4 py-2 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Cash receipts from customers (Sales ledger debits)</span>
                            <span class="text-emerald-600">+Rp {{ number_format($cf['receipts'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Cash paid to suppliers & employees (Sales ledger credits)</span>
                            <span class="text-red-600">-Rp {{ number_format($cf['payments'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cash Flow from Investing Activities -->
                <div>
                    <div class="flex items-center justify-between text-md font-bold text-gray-900 border-b border-gray-300 pb-2">
                        <span>CASH FLOW FROM INVESTING ACTIVITIES</span>
                        <span class="{{ $cf['net_investing'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($cf['net_investing'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pl-4 py-2 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Cash from assets disposal</span>
                            <span class="text-emerald-600">+Rp {{ number_format($cf['investing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Cash paid for acquisitions / equipment</span>
                            <span class="text-red-600">-Rp {{ number_format($cf['investing_out'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cash Flow from Financing Activities -->
                <div>
                    <div class="flex items-center justify-between text-md font-bold text-gray-900 border-b border-gray-300 pb-2">
                        <span>CASH FLOW FROM FINANCING ACTIVITIES</span>
                        <span class="{{ $cf['net_financing'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($cf['net_financing'], 0, ',', '.') }}</span>
                    </div>
                    <div class="pl-4 py-2 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Cash receipts from equity additions</span>
                            <span class="text-emerald-600">+Rp {{ number_format($cf['financing_in'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 pb-1">
                            <span>Drawings / dividends paid</span>
                            <span class="text-red-600">-Rp {{ number_format($cf['financing_out'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Summary Row -->
                <div class="pt-6 border-t-2 border-double border-gray-300">
                    <div class="flex items-center justify-between text-lg font-bold {{ $cf['net_increase'] >= 0 ? 'text-emerald-700' : 'text-red-700' }} p-4 bg-gray-50 rounded-lg">
                        <span>NET INCREASE / (DECREASE) IN CASH</span>
                        <span class="font-mono">Rp {{ number_format($cf['net_increase'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($activeTab === 'tb')
        <!-- Trial Balance View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-4xl mx-auto font-sans">
            <div class="text-center pb-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold font-display text-gray-900">Trial Balance</h2>
                <p class="text-sm text-gray-500 mt-1">As of: {{ $end_date }}</p>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-bold text-gray-700 uppercase tracking-wider">
                            <th class="py-3 px-6">Account Code</th>
                            <th class="py-3 px-6">Account Name</th>
                            <th class="py-3 px-6 text-right">Debit Balances</th>
                            <th class="py-3 px-6 text-right">Credit Balances</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($tb['lines'] as $line)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-2.5 px-6 font-mono text-gray-500">{{ $line['code'] }}</td>
                                <td class="py-2.5 px-6 font-medium text-gray-800">{{ $line['name'] }}</td>
                                <td class="py-2.5 px-6 text-right font-mono text-gray-900">
                                    {{ $line['debit'] > 0 ? 'Rp ' . number_format($line['debit'], 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-2.5 px-6 text-right font-mono text-gray-900">
                                    {{ $line['credit'] > 0 ? 'Rp ' . number_format($line['credit'], 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t-2 border-double border-gray-300">
                        <tr>
                            <td colspan="2" class="py-3 px-6 text-left">GRAND TOTAL</td>
                            <td class="py-3 px-6 text-right font-mono text-blue-800">Rp {{ number_format($tb['total_debit'], 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right font-mono text-blue-800">Rp {{ number_format($tb['total_credit'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if(round($tb['total_debit']) !== round($tb['total_credit']))
                <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-800 text-xs rounded text-center">
                    ⚠️ Out of balance discrepancy detected: Rp {{ number_format(abs($tb['total_debit'] - $tb['total_credit']), 0, ',', '.') }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'aging')
        <!-- AR/AP Aging Reports View -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-4xl mx-auto font-sans">
            <div class="text-center pb-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold font-display text-gray-900">AR & AP Aging Reports</h2>
                <p class="text-sm text-gray-500 mt-1">Outstanding receivables and payables aging categories.</p>
            </div>

            <div class="mt-8 space-y-8">
                <!-- AR Aging Ledger -->
                <div>
                    <h3 class="text-md font-bold text-blue-800 font-display mb-3">Accounts Receivable (AR) Aging</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="block text-xs text-gray-500">Current (0-30 Days)</span>
                            <span class="block text-sm font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($aging['ar']['current'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="block text-xs text-gray-500">31-60 Days</span>
                            <span class="block text-sm font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($aging['ar']['thirty'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="block text-xs text-gray-500">61-90 Days</span>
                            <span class="block text-sm font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($aging['ar']['sixty'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-red-50 rounded">
                            <span class="block text-xs text-red-600">Over 90 Days</span>
                            <span class="block text-sm font-bold font-mono text-red-700 mt-1">Rp {{ number_format($aging['ar']['over_ninety'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-blue-50 rounded col-span-2 md:col-span-1">
                            <span class="block text-xs text-blue-600 font-bold">Total Receivables</span>
                            <span class="block text-sm font-bold font-mono text-blue-700 mt-1">Rp {{ number_format($aging['ar']['total'], 0) }}</span>
                        </div>
                    </div>
                </div>

                <!-- AP Aging Ledger -->
                <div>
                    <h3 class="text-md font-bold text-rose-800 font-display mb-3">Accounts Payable (AP) Aging</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="block text-xs text-gray-500">Current (0-30 Days)</span>
                            <span class="block text-sm font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($aging['ap']['current'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="block text-xs text-gray-500">31-60 Days</span>
                            <span class="block text-sm font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($aging['ap']['thirty'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded">
                            <span class="block text-xs text-gray-500">61-90 Days</span>
                            <span class="block text-sm font-bold font-mono text-gray-900 mt-1">Rp {{ number_format($aging['ap']['sixty'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-red-50 rounded">
                            <span class="block text-xs text-red-600">Over 90 Days</span>
                            <span class="block text-sm font-bold font-mono text-red-700 mt-1">Rp {{ number_format($aging['ap']['over_ninety'], 0) }}</span>
                        </div>
                        <div class="p-3 bg-rose-50 rounded col-span-2 md:col-span-1">
                            <span class="block text-xs text-rose-600 font-bold">Total Payables</span>
                            <span class="block text-sm font-bold font-mono text-rose-700 mt-1">Rp {{ number_format($apAgingSum = $aging['ap']['total'], 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
