# SKILLS.md — LaravelERP Architect
**Agent Identity:** Laravel ERP Architect  
**Spesialisasi:** Perancangan & Pengembangan ERP Skala Enterprise  
**Stack:** Laravel 13 + Breeze + Blade + Alpine.js + MySQL 8.4  
**UI Style:** Minimalis, Keren, Modern (Tailwind CSS)

---

## TYPOGRAFI SYSTEM

### Font Stack (Minimalis Modern)
```css
/* Heading — Display Font */
font-family: 'Plus Jakarta Sans', 'DM Sans', sans-serif;

/* Body — Readable & Clean */
font-family: 'Inter', 'IBM Plex Sans', sans-serif;

/* Monospace — Kode & ID */
font-family: 'JetBrains Mono', 'Fira Code', monospace;
```

### Type Scale (Tailwind)
| Token     | Class          | Size     | Weight   | Pakai untuk              |
|-----------|----------------|----------|----------|--------------------------|
| Display   | `text-3xl`     | 30px     | 700      | Page title utama         |
| H1        | `text-2xl`     | 24px     | 700      | Section header           |
| H2        | `text-xl`      | 20px     | 600      | Card title               |
| H3        | `text-lg`      | 18px     | 600      | Sub-section              |
| Body LG   | `text-base`    | 16px     | 400      | Teks utama               |
| Body SM   | `text-sm`      | 14px     | 400      | Label, deskripsi         |
| Caption   | `text-xs`      | 12px     | 400      | Badge, timestamp         |
| Mono      | `font-mono text-sm` | 14px | 500    | Kode, nomor invoice, ID  |

### Typography Rules
- Line height body: `leading-relaxed` (1.625)
- Line height heading: `leading-tight` (1.25)
- Letter spacing heading: `tracking-tight`
- Maksimum lebar baris teks: `max-w-prose` (65ch)

---

## 1. CORE TECHNICAL SKILLS

### 1.1 Backend Development (Laravel 13)

**Routing & Middleware**
```php
// Route grouping dengan middleware
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('employees', EmployeeController::class);
});
```

**Controller Pattern (Service Layer)**
```php
// app/Http/Controllers/SalesController.php
class SalesController extends Controller
{
    public function __construct(private SalesService $salesService) {}

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $this->salesService->createSale($request->validated());
        return redirect()->route('sales.index')->with('success', 'Invoice berhasil dibuat.');
    }
}
```

**Eloquent ORM — Wajib Patuhi:**
- Eager Loading → cegah N+1: `->with(['customer', 'items.product'])`
- Select kolom spesifik: `->select('id', 'invoice_number', 'grand_total', 'status')`
- Cursor untuk data besar: `Product::cursor()->each(fn($p) => ...)`
- Chunk untuk update massal: `Sale::chunk(500, fn($sales) => ...)`
- Scope query reusable: `->active()`, `->thisMonth()`, `->byWarehouse($id)`

**Queue & Job (Laporan Berat)**
```php
// Dispatch ke queue, jangan block HTTP request
GeneratePayrollReport::dispatch($period)->onQueue('reports');
SendInvoiceEmail::dispatch($sale)->delay(now()->addMinutes(1));
```

**Event & Listener (Audit Trail)**
```php
// Event otomatis catat ke activity_logs
SaleCreated::dispatch($sale);    // → LogActivityListener
StockUpdated::dispatch($item);   // → UpdateStockSnapshot
```

---

### 1.2 Authentication (Laravel Breeze + Blade)

**Stack yang digunakan:** Blade + Alpine.js (BUKAN Livewire / React / Vue)

**Fitur Breeze yang diaktifkan:**
- Login & Register dengan validasi
- Password reset (email link)
- Email verification (opsional, aktifkan di `User implements MustVerifyEmail`)
- Password confirmation untuk aksi sensitif (hapus data, approve payroll)

**Role-based Redirect:**
```php
// app/Http/Controllers/Auth/AuthenticatedSessionController.php
protected function redirectTo(): string
{
    return match(auth()->user()->role) {
        'admin'     => route('dashboard'),
        'manager'   => route('reports.index'),
        'hr'        => route('employees.index'),
        default     => route('dashboard'),
    };
}
```

**Middleware Role:**
```php
// app/Http/Middleware/CheckRole.php
public function handle(Request $request, Closure $next, string ...$roles): Response
{
    if (!in_array(auth()->user()->role, $roles)) {
        abort(403, 'Akses ditolak.');
    }
    return $next($request);
}
```

**Roles yang tersedia** (berdasarkan field `users.role`):
- `admin` — akses penuh semua modul
- `manager` — akses laporan & approval
- `hr` — akses modul HRD
- `warehouse` — akses modul gudang & inventori
- `finance` — akses modul akuntansi & payroll
- `user` — akses terbatas (view only)

---

### 1.3 Frontend (Blade + Alpine.js + Tailwind CSS)

**Blade Layout Master:**
```
resources/views/
├── layouts/
│   ├── app.blade.php          ← layout utama (sidebar + topbar)
│   ├── auth.blade.php         ← layout login/register
│   └── print.blade.php        ← layout cetak invoice/laporan
├── components/
│   ├── sidebar.blade.php
│   ├── topbar.blade.php
│   ├── stat-card.blade.php
│   ├── data-table.blade.php
│   ├── modal.blade.php
│   ├── badge.blade.php
│   └── alert.blade.php
```

**Alpine.js Pattern Wajib:**

Modal:
```html
<div x-data="{ open: false }">
    <button @click="open = true">Hapus</button>
    <div x-show="open" x-transition class="modal-backdrop">
        <div class="modal-box">
            <h3>Konfirmasi Hapus</h3>
            <button @click="open = false">Batal</button>
            <button type="submit">Ya, Hapus</button>
        </div>
    </div>
</div>
```

Sidebar collapse (state disimpan localStorage):
```html
<div x-data="{ collapsed: localStorage.getItem('sidebar') === 'true' }"
     @click="collapsed = !collapsed; localStorage.setItem('sidebar', collapsed)">
```

Form dinamis (tambah/hapus baris item invoice):
```html
<div x-data="{ items: [{ product_id: '', qty: 1, price: 0 }] }">
    <template x-for="(item, i) in items" :key="i">
        <div>
            <select :name="`items[${i}][product_id]`" x-model="item.product_id">...</select>
            <button @click="items.splice(i, 1)">Hapus</button>
        </div>
    </template>
    <button @click="items.push({ product_id: '', qty: 1, price: 0 })">Tambah Baris</button>
</div>
```

---

### 1.4 Database MySQL 8.4 — Optimization Rules

**Index Wajib (berdasarkan skema ERP ini):**
```sql
-- Foreign key yang sering di-join
INDEX idx_sales_customer    (customer_id)
INDEX idx_purchases_supplier (supplier_id)
INDEX idx_stock_items_product_warehouse (product_id, warehouse_id)  -- composite
INDEX idx_journals_account_date (account_id, transaction_date)      -- composite
INDEX idx_attendances_employee_date (employee_id, date)             -- composite

-- Filter kombinasi umum
INDEX idx_sales_status_date (status, sale_date)
INDEX idx_production_orders_status (status, start_date)
```

**Query Optimization:**
```php
// BAIK: pilih kolom, eager load, index-friendly
Sale::select('id', 'invoice_number', 'grand_total', 'status', 'sale_date')
    ->with(['customer:id,name', 'items'])
    ->where('status', 'confirmed')
    ->whereBetween('sale_date', [$from, $to])
    ->orderByDesc('sale_date')
    ->paginate(25);

// BURUK: select *, tanpa eager load
Sale::all(); // ← JANGAN
```

**Raw Query untuk Laporan Kompleks:**
```php
$report = DB::select("
    SELECT p.code, p.name, SUM(si.qty_on_hand) as total_stock,
           SUM(si.qty_on_hand * p.price) as stock_value
    FROM stock_items si
    JOIN products p ON p.id = si.product_id
    JOIN warehouses w ON w.id = si.warehouse_id
    GROUP BY p.id, p.code, p.name
    ORDER BY stock_value DESC
");
```

---

## 2. UI/UX DESIGN SYSTEM

### 2.1 Design Principles

| Prinsip | Implementasi Tailwind |
|---------|-----------------------|
| Clean & Airy | `p-6 space-y-4`, whitespace konsisten |
| Subtle Shadows | `shadow-sm` card, `shadow-md` dropdown |
| Rounded Corners | `rounded-lg` (8px) card, `rounded-md` input/button |
| Consistent Spacing | Gunakan: `gap-4`, `gap-6`, `p-4`, `p-6`, `mb-8` |
| Smooth Transition | `transition-all duration-200 ease-in-out` |
| Fast Perception | Skeleton loader, bukan spinner blank |

### 2.2 Color Palette

```css
/* === LIGHT MODE === */
--color-primary:    #3B82F6;  /* Blue-500  → CTA button, link, active border */
--color-primary-hover: #2563EB; /* Blue-600 */
--color-secondary:  #10B981;  /* Emerald-500 → sukses, badge completed */
--color-warning:    #F59E0B;  /* Amber-500  → peringatan, pending */
--color-danger:     #EF4444;  /* Red-500    → error, delete, cancel */
--color-info:       #06B6D4;  /* Cyan-500   → informasi */
--color-background: #F9FAFB;  /* Gray-50    → latar halaman */
--color-surface:    #FFFFFF;  /* White      → kartu, modal, sidebar */
--color-text:       #111827;  /* Gray-900   → teks utama */
--color-text-muted: #6B7280;  /* Gray-500   → label, placeholder */
--color-border:     #E5E7EB;  /* Gray-200   → border, divider */

/* === DARK MODE === */
--color-background-dark: #111827; /* Gray-900 */
--color-surface-dark:    #1F2937; /* Gray-800 */
--color-text-dark:       #F9FAFB; /* Gray-50  */
--color-border-dark:     #374151; /* Gray-700 */
```

### 2.3 Component Patterns

**Badge Status:**
```html
<!-- Tailwind badge berdasarkan status -->
@php
$badgeClass = match($sale->status) {
    'draft'     => 'bg-gray-100 text-gray-700',
    'confirmed' => 'bg-blue-100 text-blue-700',
    'shipped'   => 'bg-amber-100 text-amber-700',
    'delivered' => 'bg-emerald-100 text-emerald-700',
    'cancelled' => 'bg-red-100 text-red-700',
    default     => 'bg-gray-100 text-gray-500',
};
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
    {{ ucfirst($sale->status) }}
</span>
```

**Stat Card:**
```html
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">Total Penjualan</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">
                Rp {{ number_format($totalSales, 0, ',', '.') }}
            </p>
        </div>
        <div class="p-3 bg-blue-50 rounded-lg">
            <svg class="w-6 h-6 text-blue-600">...</svg>
        </div>
    </div>
    <p class="mt-4 text-xs text-gray-500">
        <span class="text-emerald-600 font-medium">↑ 12%</span> dari bulan lalu
    </p>
</div>
```

---

## 3. STRUKTUR FOLDER PROJECT

```
erp-mini/
├── app/
│   ├── Console/
│   ├── Events/
│   │   ├── SaleCreated.php
│   │   ├── StockUpdated.php
│   │   └── PayrollProcessed.php
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    ← Breeze controllers
│   │   │   ├── AccountController.php
│   │   │   ├── AssetController.php
│   │   │   ├── AttendanceController.php
│   │   │   ├── BomController.php
│   │   │   ├── CustomerController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── DepartmentController.php
│   │   │   ├── EmployeeController.php
│   │   │   ├── GoodReceiptController.php
│   │   │   ├── InventoryController.php
│   │   │   ├── JournalController.php
│   │   │   ├── LeaveController.php
│   │   │   ├── PayrollController.php
│   │   │   ├── ProductCategoryController.php
│   │   │   ├── ProductController.php
│   │   │   ├── ProductionOrderController.php
│   │   │   ├── PurchaseController.php
│   │   │   ├── PurchaseRequestController.php
│   │   │   ├── RfqController.php
│   │   │   ├── ReportController.php
│   │   │   ├── SaleController.php
│   │   │   ├── SalesOrderController.php
│   │   │   ├── SalesQuotationController.php
│   │   │   ├── StockAdjustmentController.php
│   │   │   ├── StockItemController.php
│   │   │   ├── SupplierController.php
│   │   │   ├── UserController.php
│   │   │   └── WarehouseController.php
│   │   ├── Middleware/
│   │   │   ├── CheckRole.php
│   │   │   └── LogActivity.php
│   │   └── Requests/
│   │       ├── StoreSaleRequest.php
│   │       ├── StorePurchaseRequest.php
│   │       ├── StoreEmployeeRequest.php
│   │       └── ... (per modul)
│   ├── Listeners/
│   │   ├── LogActivityListener.php
│   │   └── UpdateStockOnSale.php
│   ├── Models/
│   │   ├── Account.php
│   │   ├── ActivityLog.php
│   │   ├── Asset.php
│   │   ├── AssetDepreciation.php
│   │   ├── Attendance.php
│   │   ├── Bom.php
│   │   ├── BomItem.php
│   │   ├── Company.php
│   │   ├── Customer.php
│   │   ├── Department.php
│   │   ├── Employee.php
│   │   ├── GoodReceipt.php
│   │   ├── GoodReceiptItem.php
│   │   ├── Inventory.php
│   │   ├── Journal.php
│   │   ├── Leave.php
│   │   ├── Payroll.php
│   │   ├── ProductCategory.php
│   │   ├── Product.php
│   │   ├── ProductionMaterial.php
│   │   ├── ProductionOrder.php
│   │   ├── Purchase.php
│   │   ├── PurchaseItem.php
│   │   ├── PurchaseRequest.php
│   │   ├── PurchaseRequestItem.php
│   │   ├── Rfq.php
│   │   ├── RfqItem.php
│   │   ├── Sale.php
│   │   ├── SaleItem.php
│   │   ├── SalesOrder.php
│   │   ├── SalesOrderItem.php
│   │   ├── SalesQuotation.php
│   │   ├── SalesQuotationItem.php
│   │   ├── StockAdjustment.php
│   │   ├── StockAdjustmentItem.php
│   │   ├── StockItem.php
│   │   ├── Supplier.php
│   │   ├── User.php
│   │   ├── Warehouse.php
│   │   └── WarehouseLocation.php
│   └── Services/
│       ├── AccountingService.php
│       ├── InventoryService.php
│       ├── PayrollService.php
│       ├── ProductionService.php
│       ├── PurchaseService.php
│       └── SalesService.php
│
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2025_11_08_103305_create_erp_tables.php
│   │   ├── 2025_11_17_022241_create_warehouses_table.php
│   │   ├── 2025_11_17_022303_create_warehouse_locations_table.php
│   │   ├── 2025_11_17_022312_create_stock_items_table.php
│   │   ├── 2025_11_17_022437_create_good_receipts_table.php
│   │   ├── 2025_11_17_022521_create_sales_orders_table.php
│   │   ├── 2025_11_17_022545_create_purchase_requests_table.php
│   │   ├── 2025_11_17_022617_create_rfqs_table.php
│   │   ├── 2025_11_17_022635_create_bom_table.php
│   │   ├── 2025_11_17_022654_create_stock_adjustments_table.php
│   │   ├── 2025_11_17_022713_create_assets_table.php
│   │   ├── 2025_11_17_022729_create_notifications_table.php
│   │   └── 2026_05_15_074917_create_departments_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php
│       ├── ProductCategorySeeder.php
│       └── WarehouseSeeder.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php
│       │   ├── auth.blade.php
│       │   └── print.blade.php
│       ├── components/
│       │   ├── sidebar.blade.php
│       │   ├── topbar.blade.php
│       │   ├── stat-card.blade.php
│       │   ├── data-table.blade.php
│       │   ├── modal-confirm.blade.php
│       │   ├── badge.blade.php
│       │   └── alert.blade.php
│       ├── auth/                         ← Breeze views
│       │   ├── login.blade.php
│       │   ├── register.blade.php
│       │   └── forgot-password.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       ├── products/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── edit.blade.php
│       │   └── show.blade.php
│       ├── sales/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   ├── show.blade.php
│       │   └── print.blade.php
│       ├── purchases/
│       │   ├── index.blade.php
│       │   ├── create.blade.php
│       │   └── show.blade.php
│       ├── purchase-requests/
│       ├── rfqs/
│       ├── sales-quotations/
│       ├── sales-orders/
│       ├── good-receipts/
│       ├── warehouses/
│       ├── stock-items/
│       ├── stock-adjustments/
│       ├── production-orders/
│       ├── bom/
│       ├── employees/
│       ├── attendances/
│       ├── leaves/
│       ├── payrolls/
│       ├── accounts/
│       ├── journals/
│       ├── assets/
│       ├── reports/
│       │   ├── sales.blade.php
│       │   ├── purchases.blade.php
│       │   ├── stock.blade.php
│       │   ├── payroll.blade.php
│       │   └── profit-loss.blade.php
│       └── settings/
│           ├── company.blade.php
│           └── users.blade.php
│
└── routes/
    ├── web.php
    └── auth.php                          ← Breeze routes
```

---

## 4. DATABASE SCHEMA (erp-mini)

### 4.1 Diagram Relasi Antar Modul

```
MASTER DATA
├── users              → semua modul (created_by, user_id)
├── companies          → konfigurasi perusahaan
├── product_categories → products
├── products           → semua transaksi (produk/barang)
├── customers          → sales, sales_quotations, sales_orders
├── suppliers          → purchases, good_receipts, rfqs
├── warehouses         → warehouse_locations, stock_items, good_receipt_items
├── warehouse_locations → (referensi lokasi gudang)
├── accounts           → journals (COA)
└── departments        → (referensi departemen HR)

MODUL PENJUALAN (Sales)
├── sales_quotations   → sales_quotation_items → products
│       ↓ convert
├── sales_orders       → sales_order_items → products
│       ↓ invoice
└── sales              → sale_items → products

MODUL PEMBELIAN (Procurement)
├── purchase_requests  → purchase_request_items → products
│       ↓
├── rfqs               → rfq_items → products (via purchase_requests)
│       ↓
├── purchases          → purchase_items → products
│       ↓ terima barang
└── good_receipts      → good_receipt_items → products, warehouses

MODUL GUDANG (Inventory)
├── stock_items        ← saldo stok per produk per gudang
├── inventories        ← log transaksi stok (in/out)
└── stock_adjustments  → stock_adjustment_items → products

MODUL PRODUKSI (Manufacturing)
├── bom                → bom_items → products (bill of materials)
└── production_orders  → production_materials → products

MODUL HRD
├── employees
├── departments
├── attendances        → employees
├── leaves             → employees
└── payrolls           → employees

MODUL AKUNTANSI
├── accounts           (Chart of Accounts)
├── journals           → accounts
└── assets             → asset_depreciations

SISTEM
├── activity_logs      → users
├── notifications      (internal notification)
└── failed_jobs        (queue monitor)
```

### 4.2 Tabel Lengkap & Penjelasan

#### USERS & AUTH
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `users` | id, name, email, password, phone, **role** | Role: admin/manager/hr/warehouse/finance/user |
| `password_reset_tokens` | email, token, created_at | Laravel default |
| `personal_access_tokens` | tokenable_type, tokenable_id, token | Sanctum API |

#### MASTER DATA
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `companies` | name, address, phone, email, tax_number | Profil perusahaan |
| `product_categories` | name, description | Kategori produk |
| `products` | **code** (unique), name, category_id, price, **stock**, min_stock, unit | Stock di-maintain manual + lewat stock_items |
| `customers` | name, company_name, phone, email, address, type (individual/company) | Master pelanggan |
| `suppliers` | name, contact_person, phone, email, address | Master pemasok |
| `warehouses` | **warehouse_code** (unique), warehouse_name, address, city, country | Master gudang |
| `warehouse_locations` | warehouse_id, **location_code**, location_name | Lokasi rak dalam gudang |
| `departments` | name (unique), description, is_active | Departemen HRD |
| `accounts` | **code** (unique), name, type (asset/liability/equity/income/expense), balance | COA Akuntansi |

#### MODUL PENJUALAN
| Tabel | Kolom Penting | Status Enum |
|-------|--------------|-------------|
| `sales_quotations` | sq_number, customer_id, valid_until, created_by | draft/sent/converted/canceled |
| `sales_quotation_items` | sales_quotation_id, product_id, qty, price, discount, subtotal | — |
| `sales_orders` | so_number, customer_id, sales_quotation_id, order_date, created_by | draft/confirmed/partially_delivered/completed/canceled |
| `sales_order_items` | sales_order_id, product_id, qty, price, discount, subtotal | — |
| `sales` | invoice_number, customer_id, sale_date, due_date, total_amount, tax_amount, grand_total | draft/confirmed/shipped/delivered/cancelled |
| `sale_items` | sale_id, product_id, quantity, unit_price, total_price | — |

#### MODUL PEMBELIAN
| Tabel | Kolom Penting | Status Enum |
|-------|--------------|-------------|
| `purchase_requests` | pr_number, requested_by, department | draft/approved/rejected/canceled |
| `purchase_request_items` | purchase_request_id, product_id, qty, notes | — |
| `rfqs` | rfq_number, supplier_id, purchase_request_id, created_by | draft/sent/responded/canceled |
| `rfq_items` | rfq_id, product_id, qty, price_offered | — |
| `purchases` | purchase_number, supplier_id, purchase_date, due_date, total_amount, tax_amount, grand_total | draft/ordered/received/cancelled |
| `purchase_items` | purchase_id, product_id, quantity, unit_price, total_price | — |
| `good_receipts` | gr_number, supplier_id, received_by, reference | draft/received/canceled |
| `good_receipt_items` | good_receipt_id, product_id, **warehouse_id**, qty_received, unit_price, subtotal | — |

#### MODUL GUDANG & STOK
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `stock_items` | product_id, warehouse_id, **qty_on_hand**, qty_reserved, qty_available | Saldo stok real-time per lokasi gudang |
| `inventories` | product_id, quantity, type (in/out), reference_type, reference_id, notes | Log perpindahan stok |
| `stock_adjustments` | adj_number, warehouse_id, adjusted_by, type (opname/addition/reduction) | opname/addition/reduction |
| `stock_adjustment_items` | stock_adjustment_id, product_id, system_qty, actual_qty, difference | Selisih stok opname |

#### MODUL PRODUKSI
| Tabel | Kolom Penting | Status Enum |
|-------|--------------|-------------|
| `bom` | product_id, bom_version, notes | Bill of Materials header |
| `bom_items` | bom_id, component_id (→products), qty, unit | Komponen dalam BOM |
| `production_orders` | order_number, product_id, quantity, start_date, end_date, total_cost | planned/in_progress/completed/cancelled |
| `production_materials` | production_order_id, product_id, qty_required, qty_used, unit_cost, total_cost | Material yang digunakan |

#### MODUL HRD
| Tabel | Kolom Penting | Status Enum |
|-------|--------------|-------------|
| `employees` | employee_number, name, email, phone, position, department, join_date, salary | active/inactive |
| `attendances` | employee_id, date, check_in, check_out | present/absent/late/leave |
| `leaves` | employee_id, start_date, end_date, type (annual/sick/personal/maternity), reason | pending/approved/rejected |
| `payrolls` | employee_id, period, basic_salary, allowances, deductions, overtime_pay, total_salary | draft/paid |

#### MODUL AKUNTANSI
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `accounts` | code, name, type (asset/liability/equity/income/expense), balance | Chart of Accounts |
| `journals` | transaction_date, reference_number (unique), description, amount, type (debit/credit), account_id, reference_type, reference_id | Double-entry bookkeeping |
| `assets` | asset_code, asset_name, category, purchase_date, purchase_price, useful_life_months, residual_value | Aset tetap perusahaan |
| `asset_depreciations` | asset_id, depreciation_date, amount, accumulated, book_value | Penyusutan bulanan |

#### SISTEM
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `activity_logs` | user_id, module, action, reference_id, description, ip_address | Audit trail semua aktivitas |
| `failed_jobs` | uuid, connection, queue, payload, exception, failed_at | Monitor queue gagal |

### 4.3 Enterprise Modules (Odoo-like Features) - *Implemented*
Modul tingkat lanjut ini melengkapi ERP standar menjadi *Enterprise-ready* setara Odoo:

#### MODUL RETAIL & LAYANAN (POS, CRM, Helpdesk)
| Tabel/Modul | Deskripsi | Status |
|-------|--------------|------------|
| **Point of Sale (POS)** | Sistem kasir ritel dinamis berbasis Livewire, mendukung pemindaian, kalkulasi instan, dan multi-pembayaran. Terintegrasi dengan Sales & Inventory. | *Implemented* |
| **CRM Pipeline** | Manajemen *leads* dan prospek menggunakan papan Kanban (New, Qualified, Proposition, Won, Lost), aktivitas prospek, dan konversi ke Quotation. | *Implemented* |
| **Helpdesk Tickets** | Manajemen keluhan pelanggan (customer support), SLA, penugasan tiket, dan riwayat penyelesaian (resolutions). | *Implemented* |

#### MODUL VALUASI INVENTORI & LANDED COSTS
| Tabel/Modul | Deskripsi | Status |
|-------|--------------|------------|
| **Inventory Valuation (FIFO)** | Penilaian HPP (Harga Pokok Penjualan) secara *real-time* dengan metode FIFO, melacak pergerakan harga dasar unit secara akurat per transaksi. | *Implemented* |
| **Landed Costs** | Pengalokasian biaya bea cukai, logistik, asuransi impor ke dalam harga dasar modal unit produk (COGS/HPP). | *Implemented* |

#### MODUL MANAJEMEN PROYEK & LAYANAN
| Tabel/Modul | Deskripsi | Status |
|-------|--------------|------------|
| **Project Management** | Manajemen tugas (tasks), milestones, penugasan karyawan, dan status papan Kanban untuk proyek pelanggan. | *Implemented* |
| **Timesheets** | Pencatatan log waktu kerja karyawan pada suatu proyek untuk analisis performa HR. | *Implemented* |

#### MODUL MANAJEMEN GUDANG & LOGISTIK (Lanjutan)
| Tabel/Modul | Deskripsi | Status |
|-------|--------------|------------|
| **Barcode Scanning (WMS)** | Antarmuka khusus untuk perangkat scanner guna mempercepat proses picking, packing, penerimaan barang, dan opname. | *Implemented* |
| **Warehouse Internal Transfers** | Alur pergerakan barang antar-gudang (lokasi internal) secara sistematis. | *Implemented* |
| **Returns & Reverse Logistics** | Alur resmi untuk mengelola retur barang dari pelanggan (Sales Return) dan retur barang ke supplier (Purchase Return). | *Implemented* |

#### MODUL HR & GENERAL AFFAIRS
| Tabel/Modul | Deskripsi | Status |
|-------|--------------|------------|
| **Recruitment** | Sistem pelacakan pelamar kerja (Applicant Tracking System), tahapan wawancara, dan pembuatan kontrak kerja. | *Implemented* |
| **Appraisals** | Sistem penilaian kinerja/evaluasi karyawan secara berkala. | *Implemented* |
| **Fleet Management** | Manajemen kendaraan operasional perusahaan, termasuk log bahan bakar, asuransi, odometer, dan riwayat perbaikan. | *Implemented* |
| **Maintenance** | Penjadwalan pemeliharaan aset/mesin pabrik secara preventif dan korektif. | *Implemented* |

#### MODUL PRODUKTIVITAS & KOMUNIKASI
| Tabel/Modul | Deskripsi | Status |
|-------|--------------|------------|
| **Discuss / Internal Chat** | Platform komunikasi internal antar karyawan yang terhubung langsung dengan dokumen/transaksi. | *Implemented* |
| **Documents (DMS)** | Ruang penyimpanan arsip digital terpusat untuk menyimpan kontrak, tagihan vendor, dan berkas perusahaan. | *Implemented* |
| **Approvals** | Sistem hierarki persetujuan khusus (multi-level approval) yang fleksibel untuk berbagai permintaan/pengajuan. | *Implemented* |

---

### 4.4 Enterprise Modules Database Schema

#### HELPDESK TICKETS
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `tickets` | id, subject, customer_id, description, status (open/in_progress/resolved/closed), priority (low/medium/high/urgent), assigned_to, resolved_at | Keluhan & Tiket Customer |

#### PROJECTS, TASKS & TIMESHEETS
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `projects` | id, name, description, customer_id, start_date, end_date, status (planned/in_progress/completed/on_hold) | Master Proyek |
| `tasks` | id, project_id, name, description, assigned_to, status (todo/in_progress/done), priority, due_date | Tugas Proyek |
| `timesheets` | id, task_id, user_id, date, duration_hours, description | Catatan jam kerja tugas |

#### STOCK VALUATIONS & LANDED COSTS
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `stock_valuations` | id, product_id, quantity, unit_cost, total_value, type (in/out), reference_type, reference_id | Log audit harga dasar produk |
| `landed_costs` | id, landed_cost_number, description, total_amount, purchase_id, status (draft/applied) | Alokasi biaya logistik masuk |

#### HIGH-PRIORITY ODOO CORE MODULES
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `expenses` | id, employee_id, date, category, amount, description, status (draft/submitted/approved/paid) | Pengajuan klaim biaya karyawan |
| `bank_statements` | id, date, description, amount, reference, is_reconciled, journal_entry_id | Rekonsiliasi mutasi bank |
| `taxes` | id, name, rate, type (sales/purchase), is_active | Master konfigurasi PPN/PPh |
| `currencies` | id, code, name, symbol, exchange_rate | Multi-currency exchange rates |
| `budgets` | id, name, department_id, start_date, end_date, status | Anggaran departemen |
| `budget_lines` | id, budget_id, account_id, planned_amount, actual_amount | Rincian pos anggaran CoA |
| `quality_checkpoints` | id, product_id, test_name, criteria | Standar QC per produk |
| `quality_checks` | id, quality_checkpoint_id, reference_type, reference_id, status (passed/failed) | Log hasil QC masuk/produksi |

#### 4.5 EXPANDED ODOO MODULES (Baru / Tersembunyi)
Tabel-tabel di bawah mencakup fitur Odoo tingkat lanjut untuk Sales, HR, Produksi, dan Produktivitas.

**SALES & MARKETING (E-Commerce, Subs, Rental, Marketing)**
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `subscriptions` | id, customer_id, plan_id, start_date, end_date, status | Langganan berulang |
| `rental_orders` | id, customer_id, start_date, end_date, total_amount, status | Penyewaan alat/barang |
| `marketing_campaigns` | id, name, type (email/sms), status, sent_at | Promosi pemasaran massal |
| `ecommerce_orders` | id, customer_id, total, status | Transaksi dari Website/E-Commerce |

**OPERATIONAL & LOGISTICS (PLM, WMS, Returns, Fleet, Maintenance)**
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `warehouse_transfers` | id, transfer_number, from_warehouse_id, to_warehouse_id, status | Perpindahan stok antar gudang |
| `returns` | id, return_number, sale_id, purchase_id, type, status | Retur pelanggan dan supplier |
| `maintenance_requests`| id, asset_id, user_id, request_date, status, priority | Permintaan pemeliharaan/perbaikan |
| `fleets` | id, vehicle_name, license_plate, driver_id, status | Kendaraan operasional perusahaan |
| `fleet_services` | id, fleet_id, service_date, cost, description | Log servis kendaraan |
| `work_centers` | id, code, name, cost_per_hour, capacity | Stasiun kerja produksi (PLM) |

**HR & PRODUCTIVITY (Recruitment, Appraisals, Discuss, Docs, Sign, Approvals)**
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `applicants` | id, job_position_id, name, email, status | Kandidat pelamar kerja |
| `appraisals` | id, employee_id, manager_id, period, score | Evaluasi kinerja karyawan |
| `chat_messages` | id, sender_id, receiver_id, message, is_read | Chat internal (Discuss) |
| `documents` | id, name, file_path, folder, uploaded_by | Document Management |
| `approvals` | id, reference_type, reference_id, approver_id, status | Hierarki persetujuan bertingkat |
| `sign_requests` | id, document_id, requested_by, status | E-Signature requests |

**PAYMENTS & PORTAL (E-Commerce, Subscriptions, Customer Portal)**
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `payment_providers` | id, name, code, is_active, credentials | Master payment gateway (Midtrans, Stripe) |
| `payment_transactions`| id, transaction_number, amount, provider_id, status | Log transaksi pembayaran digital |
| `cms_pages` | id, title, slug, status | Halaman Website (CMS) |
| `appointments` | id, title, customer_id, start_time, status | Layanan Pemesanan Daring (Booking) |
| `events` | id, name, start_date, max_attendees, ticket_price | Manajemen Acara & Penjualan Tiket |
| `courses` | id, title, instructor_id, duration_hours | Platform eLearning (LMS) |

**ADVANCED MANUFACTURING & LOGISTICS**
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `mrp_routings` | id, name, bom_id | Langkah rute produksi & standar waktu |
| `routing_operations` | id, routing_id, work_center_id, sequence, time_cycle | Operasi rinci manufaktur |
| `subcontracting_orders`| id, subcontract_number, supplier_id, bom_id | Maklon/subkontrak produksi |
| `eco_requests` | id, eco_number, bom_id, status | Engineering Change Orders (PLM) |
| `delivery_carriers` | id, name, provider (JNE, SiCepat), default_cost | Ekspedisi dan metode pengiriman |
| `dropship_orders` | id, dropship_number, supplier_id, customer_id | Pesanan Dropship dari vendor ke kustomer |

**PRODUCTIVITY, SURVEYS & FSM (Marketing Auto, Field Service)**
| Tabel | Kolom Penting | Keterangan |
|-------|--------------|------------|
| `surveys` | id, title, description, status | Kuesioner dinamis untuk HR / Marketing |
| `survey_responses` | id, survey_id, user_id, question_id, answer | Log jawaban kuesioner |
| `automation_workflows`| id, name, trigger_type, status | Alur pemasaran otomatis |
| `field_service_orders`| id, fsm_number, customer_id, assigned_to, status | Tugas lapangan teknisi (FSM) |
| `fsm_worksheets` | id, field_service_order_id, labor_hours, materials_used | Laporan tugas lapangan (Worksheet) |

---

## 5. MODEL ELOQUENT — RELASI UTAMA

```php
// app/Models/Product.php
class Product extends Model
{
    protected $fillable = ['code','name','category_id','description','price','stock','min_stock','unit'];

    public function category(): BelongsTo     { return $this->belongsTo(ProductCategory::class); }
    public function stockItems(): HasMany      { return $this->hasMany(StockItem::class); }
    public function saleItems(): HasMany       { return $this->hasMany(SaleItem::class); }
    public function purchaseItems(): HasMany   { return $this->hasMany(PurchaseItem::class); }
    public function boms(): HasMany            { return $this->hasMany(Bom::class); }
}

// app/Models/Sale.php
class Sale extends Model
{
    protected $fillable = ['invoice_number','customer_id','sale_date','due_date',
                           'total_amount','tax_amount','grand_total','status','notes'];

    public function customer(): BelongsTo     { return $this->belongsTo(Customer::class); }
    public function items(): HasMany           { return $this->hasMany(SaleItem::class); }

    // Scope filter cepat
    public function scopeThisMonth($q)        { return $q->whereMonth('sale_date', now()->month); }
    public function scopeByStatus($q, $status){ return $q->where('status', $status); }
}

// app/Models/StockItem.php
class StockItem extends Model
{
    public function product(): BelongsTo   { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }

    // Virtual: qty available
    public function getQtyAvailableAttribute(): float
    {
        return $this->qty_on_hand - $this->qty_reserved;
    }
}
```

---

## 6. KONVENSI KODE

### Naming Convention
| Konteks | Convention | Contoh |
|---------|-----------|--------|
| Model | PascalCase | `SalesOrder`, `GoodReceipt` |
| Controller | PascalCase + Controller | `SalesOrderController` |
| Migration | snake_case datetime prefix | `2025_11_17_022521_create_sales_orders_table` |
| Route name | kebab-case dengan titik | `sales-orders.index`, `good-receipts.store` |
| Blade view | kebab-case folder | `resources/views/sales-orders/index.blade.php` |
| Service | PascalCase + Service | `SalesService`, `InventoryService` |
| Event | PascalCase past tense | `SaleCreated`, `StockUpdated` |

### Nomor Dokumen Format
| Dokumen | Format | Contoh |
|---------|--------|--------|
| Product | `PROD-YYYYMMDD-XXXX` | INV-20260518-0001 |
| Invoice | `INV-YYYYMMDD-XXXX` | INV-20260518-0001 |
| Purchase Order | `PO-YYYYMMDD-XXXX` | PO-20260518-0001 |
| Good Receipt | `GR-YYYYMMDD-XXXX` | GR-20260518-0001 |
| Purchase Request | `PR-YYYYMMDD-XXXX` | PR-20260518-0001 |
| RFQ | `RFQ-YYYYMMDD-XXXX` | RFQ-20260518-0001 |
| Sales Quotation | `SQ-YYYYMMDD-XXXX` | SQ-20260518-0001 |
| Sales Order | `SO-YYYYMMDD-XXXX` | SO-20260518-0001 |
| Stock Adjustment | `ADJ-YYYYMMDD-XXXX` | ADJ-20260518-0001 |
| Production Order | `PRD-YYYYMMDD-XXXX` | PRD-20260518-0001 |
| Journal Entry | `JRN-YYYYMMDD-XXXX` | JRN-20260518-0001 |

---

## 7. BEST PRACTICES & CHECKLIST

### Setiap Modul Baru WAJIB:
- [ ] Migration dengan index yang tepat (FK + composite index)
- [ ] Model dengan `$fillable`, relasi, dan scope
- [ ] FormRequest untuk validasi input
- [ ] Resource Controller (index, create, store, show, edit, update, destroy)
- [ ] Service class untuk logika bisnis kompleks
- [ ] Route dalam grup middleware `auth` dan `role:...`
- [ ] Blade views: index (table + pagination), create/edit (form), show (detail)
- [ ] Activity log di service: `ActivityLog::create([...])`
- [ ] Flash message redirect: `->with('success', '...')`

### Performance Checklist:
- [ ] Gunakan `->select()` kolom yang diperlukan saja
- [ ] Gunakan `->with()` eager loading, tidak boleh ada N+1
- [ ] Pagination wajib: `->paginate(25)` untuk list
- [ ] Laporan berat → dispatch ke Queue
- [ ] Index database untuk kolom FK dan filter kombinasi
- [ ] Cache untuk data master yang jarang berubah: `Cache::remember('warehouses', 3600, fn() => ...)`

### Security Checklist:
- [ ] Semua route dilindungi middleware `auth`
- [ ] Role check dengan middleware `CheckRole`
- [ ] Input validasi dengan FormRequest (wajib)
- [ ] Mass assignment dilindungi dengan `$fillable`
- [ ] Aksi sensitif (delete, approve) + konfirmasi modal Alpine.js
- [ ] Activity log untuk semua aksi create/update/delete

---

*SKILLS.md ini adalah dokumen referensi aktif. Update setiap kali ada perubahan arsitektur, skema database baru, atau konvensi baru disepakati.*
