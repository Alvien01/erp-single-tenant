<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_settings', function (Blueprint $table) {
            $table->id();
            $table->string('route_name')->unique();
            $table->string('label');
            $table->string('group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default menu items
        $menus = [
            // Dashboard
            ['route_name' => 'dashboard', 'label' => 'Dashboard', 'group' => 'Main', 'is_active' => true, 'sort_order' => 1],

            // Master Data
            ['route_name' => 'products', 'label' => 'Products', 'group' => 'Master Data', 'is_active' => true, 'sort_order' => 10],
            ['route_name' => 'categories', 'label' => 'Categories', 'group' => 'Master Data', 'is_active' => true, 'sort_order' => 11],
            ['route_name' => 'customers', 'label' => 'Customers', 'group' => 'Master Data', 'is_active' => true, 'sort_order' => 12],
            ['route_name' => 'suppliers', 'label' => 'Suppliers', 'group' => 'Master Data', 'is_active' => true, 'sort_order' => 13],
            ['route_name' => 'warehouses', 'label' => 'Warehouses', 'group' => 'Master Data', 'is_active' => true, 'sort_order' => 14],

            // Sales Workflow
            ['route_name' => 'crm', 'label' => 'CRM Pipeline', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 20],
            ['route_name' => 'pos', 'label' => 'POS Terminal', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 21],
            ['route_name' => 'pos-stores', 'label' => 'Cabang / Store POS', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 22],
            ['route_name' => 'pos-members', 'label' => 'Member & Loyalty', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 23],
            ['route_name' => 'pos-promos', 'label' => 'Promo & Diskon', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 24],
            ['route_name' => 'pos-reports', 'label' => 'Laporan POS & Shift', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 25],
            ['route_name' => 'sales-quotations', 'label' => 'Quotations', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 26],
            ['route_name' => 'sales-orders', 'label' => 'Sales Orders', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 27],
            ['route_name' => 'sales', 'label' => 'Invoices / Sales', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 28],
            ['route_name' => 'delivery-orders', 'label' => 'Delivery Orders', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 29],
            ['route_name' => 'returns', 'label' => 'Product Returns', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 30],
            ['route_name' => 'invoices', 'label' => 'Credit/Debit Notes', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 31],
            ['route_name' => 'subscriptions', 'label' => 'Subscriptions', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 32],
            ['route_name' => 'rentals', 'label' => 'Rentals', 'group' => 'Sales Workflow', 'is_active' => true, 'sort_order' => 33],

            // Manufacturing
            ['route_name' => 'bom', 'label' => 'Bill of Materials (BOM)', 'group' => 'Manufacturing', 'is_active' => true, 'sort_order' => 40],
            ['route_name' => 'production-orders', 'label' => 'Production Orders', 'group' => 'Manufacturing', 'is_active' => true, 'sort_order' => 41],
            ['route_name' => 'plm', 'label' => 'Product Lifecycle (PLM)', 'group' => 'Manufacturing', 'is_active' => true, 'sort_order' => 42],
            ['route_name' => 'advanced-manufacturing', 'label' => 'Advanced Mfg (MRP II)', 'group' => 'Manufacturing', 'is_active' => true, 'sort_order' => 43],

            // Warehouse & Stock
            ['route_name' => 'good-receipts', 'label' => 'Good Receipts', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 50],
            ['route_name' => 'inventory', 'label' => 'Stock Balance', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 51],
            ['route_name' => 'stock-valuation', 'label' => 'Stock Valuation / LC', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 52],
            ['route_name' => 'hpp-calculator', 'label' => 'Kalkulator HPP Impor', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 53],
            ['route_name' => 'quality-control', 'label' => 'Quality Control (QC)', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 54],
            ['route_name' => 'barcode-scanner', 'label' => 'Barcode Scanner (SKU)', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 55],
            ['route_name' => 'warehouse-transfers', 'label' => 'Warehouse Transfers', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 56],
            ['route_name' => 'reordering-rules', 'label' => 'Reordering Rules', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 57],
            ['route_name' => 'advanced-logistics', 'label' => 'Advanced Logistics', 'group' => 'Warehouse & Stock', 'is_active' => true, 'sort_order' => 58],

            // HR & Recruitment
            ['route_name' => 'hr', 'label' => 'HR & Payroll', 'group' => 'HR & Recruitment', 'is_active' => true, 'sort_order' => 60],
            ['route_name' => 'expenses', 'label' => 'Expenses Claim', 'group' => 'HR & Recruitment', 'is_active' => true, 'sort_order' => 61],
            ['route_name' => 'recruitment', 'label' => 'Recruitment (ATS)', 'group' => 'HR & Recruitment', 'is_active' => true, 'sort_order' => 62],
            ['route_name' => 'appraisals', 'label' => 'Performance Reviews', 'group' => 'HR & Recruitment', 'is_active' => true, 'sort_order' => 63],
            ['route_name' => 'schedules', 'label' => 'Work Schedules', 'group' => 'HR & Recruitment', 'is_active' => true, 'sort_order' => 64],

            // Accounting & SIA
            ['route_name' => 'accounting', 'label' => 'Journal Ledger', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 70],
            ['route_name' => 'cash-bank', 'label' => 'Kas & Bank', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 71],
            ['route_name' => 'accounts-receivable', 'label' => 'Piutang (AR)', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 72],
            ['route_name' => 'accounts-payable', 'label' => 'Hutang (AP)', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 73],
            ['route_name' => 'bank-reconciliation', 'label' => 'Bank Reconciliation', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 74],
            ['route_name' => 'taxes', 'label' => 'Tax Management & e-Faktur', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 75],
            ['route_name' => 'currencies', 'label' => 'Multi-Currency', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 76],
            ['route_name' => 'budgets', 'label' => 'Budgeting', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 77],
            ['route_name' => 'approvals', 'label' => 'Workflow Approvals', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 78],
            ['route_name' => 'advanced-accounting', 'label' => 'Advanced Accounting', 'group' => 'Accounting & SIA', 'is_active' => true, 'sort_order' => 79],

            // Procurement
            ['route_name' => 'purchase-requests', 'label' => 'Purchase Requests', 'group' => 'Procurement', 'is_active' => true, 'sort_order' => 80],
            ['route_name' => 'rfqs', 'label' => 'RFQs', 'group' => 'Procurement', 'is_active' => true, 'sort_order' => 81],
            ['route_name' => 'purchases', 'label' => 'Purchase Orders', 'group' => 'Procurement', 'is_active' => true, 'sort_order' => 82],

            // Operations
            ['route_name' => 'fleet', 'label' => 'Fleet Management', 'group' => 'Operations', 'is_active' => true, 'sort_order' => 90],
            ['route_name' => 'maintenance', 'label' => 'Equipment Maintenance', 'group' => 'Operations', 'is_active' => true, 'sort_order' => 91],
            ['route_name' => 'fsm', 'label' => 'Field Service (FSM)', 'group' => 'Operations', 'is_active' => true, 'sort_order' => 92],

            // Content
            ['route_name' => 'content-manager', 'label' => 'Content Manager', 'group' => 'Content', 'is_active' => true, 'sort_order' => 100],

            // Config Basic
            ['route_name' => 'config-basic-manager', 'label' => 'Config Basic', 'group' => 'Config Basic', 'is_active' => true, 'sort_order' => 105],

            // Website & Marketing
            ['route_name' => 'website-cms', 'label' => 'Website & CMS', 'group' => 'Website & Marketing', 'is_active' => true, 'sort_order' => 110],
            ['route_name' => 'ecommerce', 'label' => 'E-Commerce', 'group' => 'Website & Marketing', 'is_active' => true, 'sort_order' => 111],
            ['route_name' => 'marketing', 'label' => 'Email Marketing', 'group' => 'Website & Marketing', 'is_active' => true, 'sort_order' => 112],
            ['route_name' => 'marketing-automation', 'label' => 'Marketing Automation', 'group' => 'Website & Marketing', 'is_active' => true, 'sort_order' => 113],

            // Standalone items
            ['route_name' => 'discuss', 'label' => 'Discuss / Chat', 'group' => 'Collaboration', 'is_active' => true, 'sort_order' => 120],
            ['route_name' => 'documents', 'label' => 'Document Archive', 'group' => 'Documents & Sign', 'is_active' => true, 'sort_order' => 130],
            ['route_name' => 'sign', 'label' => 'E-Sign Requests', 'group' => 'Documents & Sign', 'is_active' => true, 'sort_order' => 131],
            ['route_name' => 'projects-manager', 'label' => 'Projects & Tasks', 'group' => 'Collaboration', 'is_active' => true, 'sort_order' => 140],
            ['route_name' => 'helpdesk', 'label' => 'Helpdesk Tickets', 'group' => 'Collaboration', 'is_active' => true, 'sort_order' => 150],
            ['route_name' => 'reports', 'label' => 'Reports', 'group' => 'System', 'is_active' => true, 'sort_order' => 160],
            ['route_name' => 'settings', 'label' => 'Settings', 'group' => 'System', 'is_active' => true, 'sort_order' => 170],
            ['route_name' => 'manage-menu', 'label' => 'Manage Menu', 'group' => 'System', 'is_active' => true, 'sort_order' => 175],
            ['route_name' => 'multi-company', 'label' => 'Multi-Company', 'group' => 'System', 'is_active' => true, 'sort_order' => 180],
        ];

        $now = now();
        foreach ($menus as &$menu) {
            $menu['created_at'] = $now;
            $menu['updated_at'] = $now;
        }

        DB::table('menu_settings')->insert($menus);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_settings');
    }
};
