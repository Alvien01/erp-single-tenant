<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ProductManager;
use App\Livewire\SalesManager;
use App\Livewire\PurchaseManager;
use App\Livewire\InventoryManager;
use App\Livewire\HRManager;
use App\Livewire\AccountingManager;
use App\Livewire\CustomerManager;
use App\Livewire\SupplierManager;
use App\Livewire\WarehouseManager;
use App\Livewire\SalesQuotationManager;
use App\Livewire\SalesOrderManager;
use App\Livewire\PurchaseRequestManager;
use App\Livewire\RFQManager;
use App\Livewire\GoodReceiptManager;
use App\Livewire\BOMManager;
use App\Livewire\ProductionOrderManager;
use App\Livewire\SettingsManager;
use App\Livewire\ReportManager;

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('products', ProductManager::class)
    ->middleware(['auth', 'verified'])
    ->name('products');

Route::get('categories', \App\Livewire\CategoryManager::class)
    ->middleware(['auth', 'verified'])
    ->name('categories');

Route::get('customers', CustomerManager::class)
    ->middleware(['auth', 'verified'])
    ->name('customers');

Route::get('suppliers', SupplierManager::class)
    ->middleware(['auth', 'verified'])
    ->name('suppliers');

Route::get('warehouses', WarehouseManager::class)
    ->middleware(['auth', 'verified'])
    ->name('warehouses');

use App\Http\Controllers\SalesQuotationPdfController;

Route::get('sales-quotations', SalesQuotationManager::class)
    ->middleware(['auth', 'verified'])
    ->name('sales-quotations');

Route::get('sales-quotations/{id}/pdf', [SalesQuotationPdfController::class, 'download'])
    ->middleware(['auth', 'verified'])
    ->name('sales-quotations.pdf');

Route::get('sales-quotations/{id}/pdf/stream', [SalesQuotationPdfController::class, 'stream'])
    ->middleware(['auth', 'verified'])
    ->name('sales-quotations.pdf.stream');

Route::get('sales-orders', SalesOrderManager::class)
    ->middleware(['auth', 'verified'])
    ->name('sales-orders');

Route::get('sales', SalesManager::class)
    ->middleware(['auth', 'verified'])
    ->name('sales');

Route::get('purchase-requests', PurchaseRequestManager::class)
    ->middleware(['auth', 'verified'])
    ->name('purchase-requests');

Route::get('rfqs', RFQManager::class)
    ->middleware(['auth', 'verified'])
    ->name('rfqs');

Route::get('purchases', PurchaseManager::class)
    ->middleware(['auth', 'verified'])
    ->name('purchases');

Route::get('good-receipts', GoodReceiptManager::class)
    ->middleware(['auth', 'verified'])
    ->name('good-receipts');

Route::get('inventory', InventoryManager::class)
    ->middleware(['auth', 'verified'])
    ->name('inventory');

Route::get('bom', BOMManager::class)
    ->middleware(['auth', 'verified'])
    ->name('bom');

Route::get('production-orders', ProductionOrderManager::class)
    ->middleware(['auth', 'verified'])
    ->name('production-orders');

Route::get('hr', HRManager::class)
    ->middleware(['auth', 'verified'])
    ->name('hr');

Route::get('accounting', AccountingManager::class)
    ->middleware(['auth', 'verified'])
    ->name('accounting');

Route::get('cash-bank', \App\Livewire\CashBankManager::class)
    ->middleware(['auth', 'verified'])
    ->name('cash-bank');

Route::get('accounts-receivable', \App\Livewire\AccountsReceivableManager::class)
    ->middleware(['auth', 'verified'])
    ->name('accounts-receivable');

Route::get('accounts-payable', \App\Livewire\AccountsPayableManager::class)
    ->middleware(['auth', 'verified'])
    ->name('accounts-payable');

Route::get('settings', SettingsManager::class)
    ->middleware(['auth', 'verified'])
    ->name('settings');

Route::get('reports', ReportManager::class)
    ->middleware(['auth', 'verified'])
    ->name('reports');

Route::get('crm', \App\Livewire\CrmManager::class)
    ->middleware(['auth', 'verified'])
    ->name('crm');

Route::get('pos', \App\Livewire\PosTerminal::class)
    ->middleware(['auth', 'verified'])
    ->name('pos');

Route::get('pos-members', \App\Livewire\MemberManager::class)
    ->middleware(['auth', 'verified'])
    ->name('pos-members');

Route::get('pos-promos', \App\Livewire\PromoManager::class)
    ->middleware(['auth', 'verified'])
    ->name('pos-promos');

Route::get('pos-stores', \App\Livewire\StoreManager::class)
    ->middleware(['auth', 'verified'])
    ->name('pos-stores');

Route::get('pos-reports', \App\Livewire\PosReportManager::class)
    ->middleware(['auth', 'verified'])
    ->name('pos-reports');

Route::get('helpdesk', \App\Livewire\HelpdeskManager::class)
    ->middleware(['auth', 'verified'])
    ->name('helpdesk');

Route::get('projects', \App\Livewire\ProjectManager::class)
    ->middleware(['auth', 'verified'])
    ->name('projects-manager');

Route::get('stock-valuation', \App\Livewire\StockValuationManager::class)
    ->middleware(['auth', 'verified'])
    ->name('stock-valuation');

Route::get('expenses', \App\Livewire\ExpenseManager::class)
    ->middleware(['auth', 'verified'])
    ->name('expenses');

Route::get('bank-reconciliation', \App\Livewire\BankReconciliationManager::class)
    ->middleware(['auth', 'verified'])
    ->name('bank-reconciliation');

Route::get('taxes', \App\Livewire\TaxManager::class)
    ->middleware(['auth', 'verified'])
    ->name('taxes');

Route::get('currencies', \App\Livewire\CurrencyManager::class)
    ->middleware(['auth', 'verified'])
    ->name('currencies');

Route::get('budgets', \App\Livewire\BudgetManager::class)
    ->middleware(['auth', 'verified'])
    ->name('budgets');

Route::get('quality-control', \App\Livewire\QualityControlManager::class)
    ->middleware(['auth', 'verified'])
    ->name('quality-control');

Route::get('fleet', \App\Livewire\FleetManager::class)
    ->middleware(['auth', 'verified'])
    ->name('fleet');

Route::get('maintenance', \App\Livewire\MaintenanceManager::class)
    ->middleware(['auth', 'verified'])
    ->name('maintenance');

Route::get('recruitment', \App\Livewire\RecruitmentManager::class)
    ->middleware(['auth', 'verified'])
    ->name('recruitment');

Route::get('appraisals', \App\Livewire\AppraisalManager::class)
    ->middleware(['auth', 'verified'])
    ->name('appraisals');

Route::get('documents', \App\Livewire\DocumentManager::class)
    ->middleware(['auth', 'verified'])
    ->name('documents');

Route::get('barcode-scanner', \App\Livewire\BarcodeScanner::class)
    ->middleware(['auth', 'verified'])
    ->name('barcode-scanner');

Route::get('discuss', \App\Livewire\DiscussManager::class)
    ->middleware(['auth', 'verified'])
    ->name('discuss');

Route::get('delivery-orders', \App\Livewire\DeliveryOrderManager::class)
    ->middleware(['auth', 'verified'])
    ->name('delivery-orders');

Route::get('returns', \App\Livewire\ReturnManager::class)
    ->middleware(['auth', 'verified'])
    ->name('returns');

Route::get('invoices', \App\Livewire\InvoiceManager::class)
    ->middleware(['auth', 'verified'])
    ->name('invoices');

Route::get('warehouse-transfers', \App\Livewire\WarehouseTransferManager::class)
    ->middleware(['auth', 'verified'])
    ->name('warehouse-transfers');

Route::get('reordering-rules', \App\Livewire\ReorderingRuleManager::class)
    ->middleware(['auth', 'verified'])
    ->name('reordering-rules');

Route::get('schedules', \App\Livewire\ScheduleManager::class)
    ->middleware(['auth', 'verified'])
    ->name('schedules');

Route::get('approvals', \App\Livewire\ApprovalManager::class)
    ->middleware(['auth', 'verified'])
    ->name('approvals');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('subscriptions', \App\Livewire\SubscriptionManager::class)
    ->middleware(['auth', 'verified'])
    ->name('subscriptions');

Route::get('rentals', \App\Livewire\RentalManager::class)
    ->middleware(['auth', 'verified'])
    ->name('rentals');

Route::get('marketing', \App\Livewire\MarketingManager::class)
    ->middleware(['auth', 'verified'])
    ->name('marketing');

Route::get('ecommerce', \App\Livewire\EcommerceManager::class)
    ->middleware(['auth', 'verified'])
    ->name('ecommerce');

Route::get('plm', \App\Livewire\PlmManager::class)
    ->middleware(['auth', 'verified'])
    ->name('plm');

Route::get('sign', \App\Livewire\SignManager::class)
    ->middleware(['auth', 'verified'])
    ->name('sign');

Route::get('hpp-calculator', \App\Livewire\HppCalculatorManager::class)
    ->middleware(['auth', 'verified'])
    ->name('hpp-calculator');

Route::get('content', \App\Livewire\ContentManager::class)
    ->middleware(['auth', 'verified'])
    ->name('content-manager');

Route::get('manage-menu', \App\Livewire\MenuManager::class)
    ->middleware(['auth', 'verified'])
    ->name('manage-menu');

Route::get('config-basic', \App\Livewire\ConfigBasicManager::class)
    ->middleware(['auth', 'verified'])
    ->name('config-basic-manager');


// Advanced Enterprise Modules
Route::get('multi-company', \App\Livewire\MultiCompanyManager::class)->middleware(['auth', 'verified', 'module:multi-company'])->name('multi-company');

Route::get('website-cms', \App\Livewire\WebsiteCmsManager::class)->middleware(['auth', 'verified', 'module:cms'])->name('website-cms');
Route::get('advanced-logistics', \App\Livewire\AdvancedLogisticsManager::class)->middleware(['auth', 'verified', 'module:logistics'])->name('advanced-logistics');
Route::get('advanced-manufacturing', \App\Livewire\AdvancedManufacturingManager::class)->middleware(['auth', 'verified', 'module:manufacturing'])->name('advanced-manufacturing');
Route::get('advanced-accounting', \App\Livewire\AdvancedAccountingManager::class)->middleware(['auth', 'verified', 'module:accounting'])->name('advanced-accounting');
Route::get('marketing-automation', \App\Livewire\MarketingAutomationManager::class)->middleware(['auth', 'verified', 'module:marketing'])->name('marketing-automation');
Route::get('fsm', \App\Livewire\FieldServiceManager::class)->middleware(['auth', 'verified', 'module:fsm'])->name('fsm');

Route::match(['get', 'post'], 'logout', function () {
    \Illuminate\Support\Facades\Auth::guard('web')->logout();
    \Illuminate\Support\Facades\Session::invalidate();
    \Illuminate\Support\Facades\Session::regenerateToken();
    return redirect()->route('login');
})->name('logout');


// ── Midtrans Payment Webhook (no CSRF, no auth) ──────────────────
Route::post('/midtrans/webhook', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
    ->name('midtrans.webhook');

require __DIR__.'/auth.php';
