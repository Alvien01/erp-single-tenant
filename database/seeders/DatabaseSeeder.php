<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseLocation;
use App\Models\Employee;
use App\Models\StockItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Admin User
        $admin = User::create([
            'name' => 'ERP Administrator',
            'email' => 'admin@erp.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '081234567890',
        ]);

        // 2. Create Company Config
        Company::create([
            'name' => 'Quantum Enterprise Ltd',
            'address' => 'Technology Park Block B, Jakarta',
            'phone' => '021-5550192',
            'email' => 'info@quantum.com',
            'tax_number' => '01.234.567.8-999.000',
        ]);

        // 3. Create Product Categories
        $electronics = ProductCategory::create(['name' => 'Electronics', 'description' => 'Electronic components & gadgets']);
        $office = ProductCategory::create(['name' => 'Office Supplies', 'description' => 'Office stationary and furniture']);

        // 4. Create Products
        $p1 = Product::create([
            'code' => 'PROD-ELE-001',
            'name' => 'Quantum Smart Monitor 27"',
            'category_id' => $electronics->id,
            'price' => 4500000.00,
            'stock' => 150,
            'min_stock' => 20,
            'unit' => 'pcs'
        ]);

        $p2 = Product::create([
            'code' => 'PROD-ELE-002',
            'name' => 'Mechanical Keyboard RGB',
            'category_id' => $electronics->id,
            'price' => 1250000.00,
            'stock' => 300,
            'min_stock' => 50,
            'unit' => 'pcs'
        ]);

        $p3 = Product::create([
            'code' => 'PROD-OFF-001',
            'name' => 'Ergonomic Office Chair',
            'category_id' => $office->id,
            'price' => 2800000.00,
            'stock' => 80,
            'min_stock' => 10,
            'unit' => 'pcs'
        ]);

        // 5. Create Customers & Suppliers
        $cust1 = Customer::create([
            'name' => 'Ahmad Fauzi',
            'company_name' => 'CV. Jaya Sentosa',
            'phone' => '087812345678',
            'email' => 'fauzi@jayacentosa.id',
            'address' => 'Jl. Merdeka No. 12, Bandung',
            'type' => 'company'
        ]);

        $cust2 = Customer::create([
            'name' => 'Diana Putri',
            'phone' => '082198765432',
            'email' => 'diana@gmail.com',
            'address' => 'Jl. Sudirman No. 45, Jakarta',
            'type' => 'individual'
        ]);

        $supp = Supplier::create([
            'name' => 'PT. Global Distribusi',
            'contact_person' => 'Budi Santoso',
            'phone' => '021-777888',
            'email' => 'sales@globaldist.com',
            'address' => 'Kawasan Industri Cikarang, Bekasi'
        ]);

        // 6. Warehouses & Warehouse Locations
        $wh = Warehouse::create([
            'warehouse_code' => 'WH-JKT-01',
            'warehouse_name' => 'Main Jakarta Warehouse',
            'address' => 'Jl. Daan Mogot KM 12, West Jakarta',
            'city' => 'West Jakarta',
            'country' => 'Indonesia'
        ]);

        WarehouseLocation::create([
            'warehouse_id' => $wh->id,
            'location_code' => 'A-01-01',
            'location_name' => 'Rack A Row 1 Level 1'
        ]);

        WarehouseLocation::create([
            'warehouse_id' => $wh->id,
            'location_code' => 'B-02-03',
            'location_name' => 'Rack B Row 2 Level 3'
        ]);

        // 7. Stock Items
        StockItem::create([
            'product_id' => $p1->id,
            'warehouse_id' => $wh->id,
            'qty_on_hand' => 100,
            'qty_reserved' => 10
        ]);

        StockItem::create([
            'product_id' => $p2->id,
            'warehouse_id' => $wh->id,
            'qty_on_hand' => 250,
            'qty_reserved' => 20
        ]);

        StockItem::create([
            'product_id' => $p3->id,
            'warehouse_id' => $wh->id,
            'qty_on_hand' => 70,
            'qty_reserved' => 5
        ]);

        // 8. Employees (HR)
        Employee::create([
            'employee_number' => 'EMP-001',
            'name' => 'Rian Hidayat',
            'email' => 'rian@quantum.com',
            'phone' => '081299998888',
            'position' => 'Warehouse Supervisor',
            'department' => 'Logistics',
            'join_date' => '2024-01-15',
            'salary' => 8500000.00,
            'status' => 'active'
        ]);

        Employee::create([
            'employee_number' => 'EMP-002',
            'name' => 'Siti Aminah',
            'email' => 'siti@quantum.com',
            'phone' => '081277776666',
            'position' => 'Sales Representative',
            'department' => 'Sales & Marketing',
            'join_date' => '2024-03-01',
            'salary' => 6000000.00,
            'status' => 'active'
        ]);

        // 9. Sales Transactions
        $s1 = Sale::create([
            'invoice_number' => 'INV-20260519-0001',
            'customer_id' => $cust1->id,
            'sale_date' => now()->subDays(2)->format('Y-m-d'),
            'due_date' => now()->addDays(14)->format('Y-m-d'),
            'total_amount' => 4500000.00,
            'tax_amount' => 450000.00,
            'grand_total' => 4950000.00,
            'status' => 'delivered',
            'notes' => 'Urgent delivery request'
        ]);

        SaleItem::create([
            'sale_id' => $s1->id,
            'product_id' => $p1->id,
            'quantity' => 1,
            'unit_price' => 4500000.00,
            'total_price' => 4500000.00
        ]);

        $s2 = Sale::create([
            'invoice_number' => 'INV-20260519-0002',
            'customer_id' => $cust2->id,
            'sale_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'total_amount' => 1250000.00,
            'tax_amount' => 125000.00,
            'grand_total' => 1375000.00,
            'status' => 'confirmed'
        ]);

        SaleItem::create([
            'sale_id' => $s2->id,
            'product_id' => $p2->id,
            'quantity' => 1,
            'unit_price' => 1250000.00,
            'total_price' => 1250000.00
        ]);

        // 10. Purchase Transactions
        Purchase::create([
            'purchase_number' => 'PO-20260519-0001',
            'supplier_id' => $supp->id,
            'purchase_date' => now()->subDays(5)->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'total_amount' => 25000000.00,
            'tax_amount' => 2500000.00,
            'grand_total' => 27500000.00,
            'status' => 'ordered'
        ]);

        // 11. Fleet & Fuel Logs
        $fleet1 = \App\Models\Fleet::create([
            'license_plate' => 'B 1234 SQA',
            'model' => 'Toyota Avanza 2022',
            'driver_id' => 1, // Rian Hidayat
            'status' => 'active',
            'odometer' => 15420.00,
            'acquisition_date' => '2024-02-10',
        ]);

        $fleet2 = \App\Models\Fleet::create([
            'license_plate' => 'B 5678 SQA',
            'model' => 'Mitsubishi Fuso Box',
            'driver_id' => 2, // Siti Aminah
            'status' => 'in_service',
            'odometer' => 45890.00,
            'acquisition_date' => '2023-08-05',
        ]);

        \App\Models\FleetFuelLog::create([
            'fleet_id' => $fleet1->id,
            'date' => '2026-05-18',
            'liters' => 35.5,
            'cost_per_liter' => 12500,
            'total_cost' => 443750,
            'odometer' => 15420,
        ]);

        \App\Models\FleetService::create([
            'fleet_id' => $fleet2->id,
            'service_date' => '2026-05-19',
            'description' => 'Regular engine oil & filter replacements',
            'cost' => 750000,
            'provider' => 'Auto2000 Cikarang',
            'status' => 'in_progress',
        ]);

        // 12. Equipment Maintenance Requests
        \App\Models\MaintenanceRequest::create([
            'asset_id' => null,
            'asset_name' => 'Main Server Rack #3',
            'description' => 'Cooling fan failed, CPU temperatures elevated',
            'request_date' => '2026-05-19',
            'cost' => 1200000.00,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        // 13. Recruitment Job Openings & Applicants
        $job = \App\Models\JobPosition::create([
            'title' => 'Senior PHP Engineer',
            'department' => 'Technology & Product',
            'expected_employees' => 2,
            'status' => 'open',
            'description' => 'Responsible for maintaining core ERP microservices built in Laravel.',
        ]);

        \App\Models\Applicant::create([
            'name' => 'Faisal Rahman',
            'email' => 'faisal@example.com',
            'phone' => '085698765432',
            'job_position_id' => $job->id,
            'status' => 'interview',
            'applied_date' => '2026-05-15',
            'notes' => 'Passed initial screening, technical interview scheduled.',
        ]);

        // 14. Performance Appraisals
        \App\Models\Appraisal::create([
            'employee_id' => 1, // Rian Hidayat
            'appraisal_date' => '2026-05-19',
            'manager_id' => $admin->id,
            'period' => '2026 Mid-Year Review',
            'score' => 4,
            'notes' => 'Excellent work managing warehouse stock operations with zero discrepancies.',
            'status' => 'confirmed',
        ]);

        // 15. Documents & E-Sign
        $doc1 = \App\Models\Document::create([
            'name' => 'Joint Venture Agreement 2026',
            'category' => 'Contract',
            'file_path' => 'mock_documents/jv_agreement_2026_final.pdf',
            'version' => 2,
            'status' => 'signed',
            'created_by' => $admin->id,
            'signed_at' => now()->subDays(1),
            'signature_data' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAiIGhlaWdodD0iMTAwIj48dGV4dCB4PSIwIiB5PSI1MCIgZmlsbD0iYmx1ZSIgZm9udC1zaXplPSIyMCI+RVJQQWRtaW48L3RleHQ+PC9zdmc+',
        ]);

        \App\Models\DocumentVersion::create([
            'document_id' => $doc1->id,
            'version' => 1,
            'file_path' => 'mock_documents/jv_agreement_2026_draft.pdf',
            'created_by' => $admin->id,
        ]);

        \App\Models\Document::create([
            'name' => 'Annual Budget Plan Proposal',
            'category' => 'General',
            'file_path' => 'mock_documents/annual_budget_proposal.pdf',
            'version' => 1,
            'status' => 'pending_signature',
            'created_by' => $admin->id,
        ]);

        // 16. Discuss / Internal Chat Messages
        \App\Models\ChatMessage::create([
            'sender_id' => $admin->id,
            'is_group' => true,
            'channel_name' => '#general',
            'message' => 'Welcome to the new Quantum Enterprise Discuss channel!',
        ]);

        // 18. Currencies
        \App\Models\Currency::create([
            'code' => 'IDR',
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'exchange_rate' => 1.0,
        ]);
        \App\Models\Currency::create([
            'code' => 'USD',
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 16250.0,
        ]);
        \App\Models\Currency::create([
            'code' => 'EUR',
            'name' => 'Euro',
            'symbol' => '€',
            'exchange_rate' => 17500.0,
        ]);
        \App\Models\Currency::create([
            'code' => 'SGD',
            'name' => 'Singapore Dollar',
            'symbol' => 'S$',
            'exchange_rate' => 12000.0,
        ]);

        // 17. Activity Logs
        ActivityLog::create([
            'user_id' => $admin->id,
            'module' => 'Auth',
            'action' => 'User Login',
            'description' => 'Administrator logged in from IP 127.0.0.1'
        ]);

        ActivityLog::create([
            'user_id' => $admin->id,
            'module' => 'Sales',
            'action' => 'Create Invoice',
            'description' => 'Invoice INV-20260519-0002 has been successfully created'
        ]);

        \App\Models\AttendanceSetting::create([
            'office_name' => 'Kantor Pusat Quantum',
            'office_latitude' => -6.200000,
            'office_longitude' => 106.816666,
            'allowed_radius' => 200,
            'work_start_time' => '08:00',
            'work_end_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'early_checkin_minutes' => 60,
            'require_location' => true,
            'is_active' => true,
        ]);
    }
}

