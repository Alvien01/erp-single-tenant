@php
    $activeMenus = \App\Models\MenuSetting::getActiveRoutes();
    $show = function($routeName) use ($activeMenus) {
        return in_array($routeName, $activeMenus);
    };
    
    // Group visibility checks
    $showMasterData = $show('products') || $show('categories') || $show('customers') || $show('suppliers') || $show('warehouses');
    $showSalesWorkflow = $show('crm') || $show('pos') || $show('pos-stores') || $show('pos-members') || $show('pos-promos') || $show('pos-reports') || $show('sales-quotations') || $show('sales-orders') || $show('sales') || $show('delivery-orders') || $show('returns') || $show('invoices') || $show('subscriptions') || $show('rentals');
    $showManufacturing = $show('bom') || $show('production-orders') || $show('plm') || $show('advanced-manufacturing');
    $showWarehouseStock = $show('good-receipts') || $show('inventory') || $show('stock-valuation') || $show('hpp-calculator') || $show('quality-control') || $show('barcode-scanner') || $show('warehouse-transfers') || $show('reordering-rules') || $show('advanced-logistics');
    $showHRRecruitment = $show('hr') || $show('expenses') || $show('recruitment') || $show('appraisals') || $show('schedules');
    $showAccountingSIA = $show('accounting') || $show('cash-bank') || $show('accounts-receivable') || $show('accounts-payable') || $show('bank-reconciliation') || $show('taxes') || $show('currencies') || $show('budgets') || $show('approvals') || $show('advanced-accounting');
    $showProcurement = $show('purchase-requests') || $show('rfqs') || $show('purchases');
    $showOperations = $show('fleet') || $show('maintenance') || $show('fsm');
    $showContent = $show('content-manager');
    $showConfigBasic = $show('config-basic-manager');
    $showWebsiteMarketing = $show('website-cms') || $show('ecommerce') || $show('marketing') || $show('marketing-automation');
@endphp

<div class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-all duration-300 ease-in-out transform"
     :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    
    <div class="flex items-center justify-between px-6 h-16 border-b border-gray-200">
        <span class="text-xl font-bold font-display text-blue-600">CV <span class="text-gray-900">RADI AMARTHA</span></span>
        <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="h-20 w-auto object-contain transition-transform duration-300 group-hover:scale-105">
        <!-- Mobile close button -->
        <button @click="sidebarOpen = false" class="text-gray-500 hover:text-gray-700 lg:hidden focus:outline-none transition duration-150">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="overflow-y-auto h-full p-4 space-y-1 pb-20">
        
        <!-- Dashboard -->
        @if ($show('dashboard'))
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            Dashboard
        </a>
        @endif

        <!-- Master Data -->
        @if ($showMasterData)
        <div x-data="{ expanded: {{ request()->routeIs(['products', 'categories', 'customers', 'suppliers', 'warehouses']) ? 'true' : 'false' }} }" class="pt-2">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Master Data
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('products')) <a href="{{ route('products') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('products') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Products</a> @endif
                @if ($show('categories')) <a href="{{ route('categories') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('categories') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Categories</a> @endif
                @if ($show('customers')) <a href="{{ route('customers') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('customers') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Customers</a> @endif
                @if ($show('suppliers')) <a href="{{ route('suppliers') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('suppliers') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Suppliers</a> @endif
                @if ($show('warehouses')) <a href="{{ route('warehouses') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('warehouses') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Warehouses</a> @endif
            </div>
        </div>
        @endif

        <!-- Sales Dropdown -->
        @if ($showSalesWorkflow)
        <div x-data="{ expanded: {{ request()->routeIs(['sales-quotations', 'sales-orders', 'sales', 'crm', 'pos', 'pos-members', 'pos-promos', 'pos-stores', 'pos-reports', 'delivery-orders', 'returns', 'invoices', 'subscriptions', 'rentals']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Sales Workflow
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('crm')) <a href="{{ route('crm') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('crm') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">CRM Pipeline</a> @endif
                @if ($show('pos')) <a href="{{ route('pos') }}" target="_blank" class="block px-2 py-1.5 text-sm rounded-md text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 font-bold">POS Terminal ↗</a> @endif
                @if ($show('pos-stores')) <a href="{{ route('pos-stores') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('pos-stores') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Cabang / Store POS</a> @endif
                @if ($show('pos-members')) <a href="{{ route('pos-members') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('pos-members') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Member & Loyalty</a> @endif
                @if ($show('pos-promos')) <a href="{{ route('pos-promos') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('pos-promos') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Promo & Diskon</a> @endif
                @if ($show('pos-reports')) <a href="{{ route('pos-reports') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('pos-reports') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Laporan POS & Shift</a> @endif
                @if ($show('sales-quotations')) <a href="{{ route('sales-quotations') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('sales-quotations') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Quotations</a> @endif
                @if ($show('sales-orders')) <a href="{{ route('sales-orders') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('sales-orders') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Sales Orders</a> @endif
                @if ($show('sales')) <a href="{{ route('sales') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('sales') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Invoices / Sales</a> @endif
                @if ($show('delivery-orders')) <a href="{{ route('delivery-orders') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('delivery-orders') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Delivery Orders</a> @endif
                @if ($show('returns')) <a href="{{ route('returns') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('returns') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Product Returns</a> @endif
                @if ($show('invoices')) <a href="{{ route('invoices') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('invoices') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Credit/Debit Notes</a> @endif
                @if ($show('subscriptions')) <a href="{{ route('subscriptions') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('subscriptions') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Subscriptions</a> @endif
                @if ($show('rentals')) <a href="{{ route('rentals') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('rentals') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Rentals</a> @endif
            </div>
        </div>
        @endif

        <!-- Manufacturing Dropdown -->
        @if ($showManufacturing)
        <div x-data="{ expanded: {{ request()->routeIs(['bom', 'production-orders', 'plm', 'advanced-manufacturing']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Manufacturing
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('bom')) <a href="{{ route('bom') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('bom') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Bill of Materials (BOM)</a> @endif
                @if ($show('production-orders')) <a href="{{ route('production-orders') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('production-orders') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Production Orders</a> @endif
                @if ($show('plm')) <a href="{{ route('plm') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('plm') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Product Lifecycle (PLM)</a> @endif
                @if ($show('advanced-manufacturing')) <a href="{{ route('advanced-manufacturing') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('advanced-manufacturing') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Advanced Mfg (MRP II)</a> @endif
            </div>
        </div>
        @endif

        <!-- Warehouse & Stock Dropdown -->
        @if ($showWarehouseStock)
        <div x-data="{ expanded: {{ request()->routeIs(['good-receipts', 'inventory', 'stock-valuation', 'quality-control', 'barcode-scanner', 'warehouse-transfers', 'reordering-rules', 'hpp-calculator', 'advanced-logistics']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Warehouse & Stock
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('good-receipts')) <a href="{{ route('good-receipts') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('good-receipts') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Good Receipts</a> @endif
                @if ($show('inventory')) <a href="{{ route('inventory') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('inventory') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Stock Balance</a> @endif
                @if ($show('stock-valuation')) <a href="{{ route('stock-valuation') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('stock-valuation') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Stock Valuation / LC</a> @endif
                @if ($show('hpp-calculator')) <a href="{{ route('hpp-calculator') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('hpp-calculator') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Kalkulator HPP</a> @endif
                @if ($show('quality-control')) <a href="{{ route('quality-control') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('quality-control') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Quality Control (QC)</a> @endif
                @if ($show('barcode-scanner')) <a href="{{ route('barcode-scanner') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('barcode-scanner') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Barcode Scanner (SKU)</a> @endif
                @if ($show('warehouse-transfers')) <a href="{{ route('warehouse-transfers') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('warehouse-transfers') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Warehouse Transfers</a> @endif
                @if ($show('reordering-rules')) <a href="{{ route('reordering-rules') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('reordering-rules') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Reordering Rules</a> @endif
                @if ($show('advanced-logistics')) <a href="{{ route('advanced-logistics') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('advanced-logistics') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Advanced Logistics</a> @endif
            </div>
        </div>
        @endif
        
        <!-- HRD Dropdown -->
        @if ($showHRRecruitment)
        <div x-data="{ expanded: {{ request()->routeIs(['hr', 'expenses', 'recruitment', 'appraisals', 'schedules']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    HR & Recruitment
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('hr')) <a href="{{ route('hr') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('hr') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">HR & Payroll</a> @endif
                @if ($show('expenses')) <a href="{{ route('expenses') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('expenses') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Expenses Claim</a> @endif
                @if ($show('recruitment')) <a href="{{ route('recruitment') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('recruitment') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Recruitment (ATS)</a> @endif
                @if ($show('appraisals')) <a href="{{ route('appraisals') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('appraisals') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Performance Reviews</a> @endif
                @if ($show('schedules')) <a href="{{ route('schedules') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('schedules') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Work Schedules</a> @endif
            </div>
        </div>
        @endif
        
        <!-- Accounting Dropdown -->
        @if ($showAccountingSIA)
        <div x-data="{ expanded: {{ request()->routeIs(['accounting', 'cash-bank', 'accounts-receivable', 'accounts-payable', 'bank-reconciliation', 'taxes', 'currencies', 'budgets', 'approvals', 'advanced-accounting']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Accounting & SIA
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('accounting')) <a href="{{ route('accounting') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('accounting') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Dashboard & Ledger</a> @endif
                @if ($show('cash-bank')) <a href="{{ route('cash-bank') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('cash-bank') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Kas & Bank</a> @endif
                @if ($show('accounts-receivable')) <a href="{{ route('accounts-receivable') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('accounts-receivable') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Piutang (AR)</a> @endif
                @if ($show('accounts-payable')) <a href="{{ route('accounts-payable') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('accounts-payable') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Hutang (AP)</a> @endif
                @if ($show('bank-reconciliation')) <a href="{{ route('bank-reconciliation') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('bank-reconciliation') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Bank Reconciliation</a> @endif
                @if ($show('taxes')) <a href="{{ route('taxes') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('taxes') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Tax Management & e-Faktur</a> @endif
                @if ($show('currencies')) <a href="{{ route('currencies') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('currencies') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Multi-Currency</a> @endif
                @if ($show('budgets')) <a href="{{ route('budgets') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('budgets') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Budgeting</a> @endif
                @if ($show('approvals')) <a href="{{ route('approvals') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('approvals') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Workflow Approvals</a> @endif
                @if ($show('hpp-calculator')) <a href="{{ route('hpp-calculator') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('hpp-calculator') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">HPP Calculator</a> @endif
                @if ($show('advanced-accounting')) <a href="{{ route('advanced-accounting') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('advanced-accounting') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Advanced Accounting</a> @endif
            </div>
        </div>
        @endif

        <!-- Procurement Dropdown -->
        @if ($showProcurement)
        <div x-data="{ expanded: {{ request()->routeIs(['purchase-requests', 'rfqs', 'purchases']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    Procurement
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('purchase-requests')) <a href="{{ route('purchase-requests') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('purchase-requests') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Purchase Requests</a> @endif
                @if ($show('rfqs')) <a href="{{ route('rfqs') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('rfqs') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">RFQs</a> @endif
                @if ($show('purchases')) <a href="{{ route('purchases') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('purchases') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Purchase Orders</a> @endif
            </div>
        </div>
        @endif

        <!-- Operations Dropdown -->
        @if ($showOperations)
        <div x-data="{ expanded: {{ request()->routeIs(['fleet', 'maintenance', 'fsm']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    Operations
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('fleet')) <a href="{{ route('fleet') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('fleet') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Fleet Management</a> @endif
                @if ($show('maintenance')) <a href="{{ route('maintenance') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('maintenance') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Equipment Maintenance</a> @endif
                @if ($show('fsm')) <a href="{{ route('fsm') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('fsm') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Field Service (FSM)</a> @endif
            </div>
        </div>
        @endif

        <!-- Content Parent Dropdown -->
        @if ($showContent)
        <div x-data="{ expanded: {{ request()->routeIs('content-manager') ? 'true' : 'false' }} }" class="pt-2">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2"></path></svg>
                    Content
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                <a href="{{ route('content-manager', ['tab' => 'banner']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'banner' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Banner</a>
                <a href="{{ route('content-manager', ['tab' => 'about']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'about' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data About us</a>
                <a href="{{ route('content-manager', ['tab' => 'service']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'service' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Our Services</a>
                <a href="{{ route('content-manager', ['tab' => 'value']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'value' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Our Value</a>
                <a href="{{ route('content-manager', ['tab' => 'gallery']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'gallery' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Gallery</a>
                <a href="{{ route('content-manager', ['tab' => 'client']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'client' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Our Client</a>
                <a href="{{ route('content-manager', ['tab' => 'tagline']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'tagline' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Tagline</a>
                <a href="{{ route('content-manager', ['tab' => 'testimoni']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'testimoni' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Testimoni</a>
                <a href="{{ route('content-manager', ['tab' => 'contact']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'contact' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Contact Us</a>
                <a href="{{ route('content-manager', ['tab' => 'template']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('content-manager') && request('tab') == 'template' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Data Themes Selection</a>
            </div>
        </div>
        @endif

        <!-- Config Basic Parent Dropdown -->
        @if ($showConfigBasic)
        <div x-data="{ expanded: {{ request()->routeIs('config-basic-manager') ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Config Basic
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                <a href="{{ route('config-basic-manager', ['tab' => 'banner']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'banner' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Banner</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'about']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'about' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config About us</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'service']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'service' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Our Services</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'gallery']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'gallery' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Our Gallery</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'news']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'news' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config News</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'value']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'value' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Value</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'testimoni']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'testimoni' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Testimoni</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'tagline']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'tagline' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Tagline</a>
                <a href="{{ route('config-basic-manager', ['tab' => 'contact']) }}" class="block px-2 py-1.5 text-xs rounded-md {{ request()->routeIs('config-basic-manager') && request('tab') == 'contact' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Config Contact Us</a>
            </div>
        </div>
        @endif

        <!-- Website & Marketing Dropdown -->
        @if ($showWebsiteMarketing)
        <div x-data="{ expanded: {{ request()->routeIs(['website-cms', 'ecommerce', 'marketing', 'marketing-automation']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                    Website & Marketing
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('website-cms')) <a href="{{ route('website-cms') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('website-cms') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Website & CMS</a> @endif
                @if ($show('ecommerce')) <a href="{{ route('ecommerce') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('ecommerce') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">E-Commerce</a> @endif
                @if ($show('marketing')) <a href="{{ route('marketing') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('marketing') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Email Marketing</a> @endif
                @if ($show('marketing-automation')) <a href="{{ route('marketing-automation') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('marketing-automation') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Marketing Automation</a> @endif
            </div>
        </div>
        @endif

        <!-- Discuss Hub -->
        @if ($show('discuss'))
        <a href="{{ route('discuss') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('discuss') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('discuss') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            Discuss / Chat
        </a>
        @endif

        <!-- Documents & Sign Dropdown -->
        @if ($show('documents') || $show('sign'))
        <div x-data="{ expanded: {{ request()->routeIs(['documents', 'sign']) ? 'true' : 'false' }} }" class="pt-1">
            <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    Documents & Sign
                </div>
                <svg :class="{'rotate-180': expanded}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div x-show="expanded" x-collapse class="pl-11 pr-4 py-1 space-y-1">
                @if ($show('documents')) <a href="{{ route('documents') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('documents') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">Document Archive</a> @endif
                @if ($show('sign')) <a href="{{ route('sign') }}" class="block px-2 py-1.5 text-sm rounded-md {{ request()->routeIs('sign') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">E-Sign Requests</a> @endif
            </div>
        </div>
        @endif

        <!-- Projects & Tasks -->
        @if ($show('projects-manager'))
        <a href="{{ route('projects-manager') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('projects-manager') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('projects-manager') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            Projects & Tasks
        </a>
        @endif

        <!-- Helpdesk Tickets -->
        @if ($show('helpdesk'))
        <a href="{{ route('helpdesk') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('helpdesk') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('helpdesk') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Helpdesk Tickets
        </a>
        @endif

        <!-- Executive Reports -->
        @if ($show('reports'))
        <a href="{{ route('reports') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('reports') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('reports') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Reports
        </a>
        @endif
        <!-- Multi-Company -->
        @if ($show('multi-company'))
        <a href="{{ route('multi-company') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('multi-company') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('multi-company') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            Multi-Company
        </a>
        @endif
        <!-- Settings -->
        @if ($show('settings'))
        <a href="{{ route('settings') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('settings') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('settings') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            Settings
        </a>
        @endif

        <!-- Manage Menu -->
        @if ($show('manage-menu'))
        <a href="{{ route('manage-menu') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-md mt-1 {{ request()->routeIs('manage-menu') ? 'bg-blue-50 text-blue-700 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('manage-menu') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            Manage Menu
        </a>
        @endif
    </div>
</div>
<!-- Mobile backdrop -->
<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 lg:hidden" @click="sidebarOpen = false"></div>