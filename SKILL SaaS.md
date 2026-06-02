---
name: erp-gede-saas-migration
description: >
  Panduan lengkap migrasi database erp-gede dari single-tenant menjadi arsitektur SaaS
  multi-tenant. Gunakan skill ini ketika diminta membuat migration script SQL, schema
  perubahan tabel, atau query untuk menambahkan tenant isolation ke erp-gede. Juga
  gunakan ketika ada pertanyaan tentang multi-tenancy, SaaS architecture, atau
  penambahan tenant_id ke tabel erp-gede.
---

# ERP-Gede → SaaS Migration Skill

Skill ini berisi semua perubahan SQL yang diperlukan untuk mengubah **erp-gede**
(single-tenant) menjadi sistem **SaaS multi-tenant**, mengacu pada pola arsitektur
yang sudah diterapkan di **pos-app**.

---

## Strategi: Shared Database, Shared Schema

Pola yang digunakan sama dengan pos-app: satu database, semua tenant dipisahkan
via kolom `tenant_id` di setiap tabel. Ini adalah pendekatan paling pragmatis untuk
migrasi bertahap.

---

## FASE 1 — Core Foundation (Wajib)

### 1.1 Tabel `tenants` (Root Tenant)

```sql
CREATE TABLE tenants (
  id               bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name             varchar(255) NOT NULL,
  slug             varchar(255) NOT NULL UNIQUE,   -- subdomain: wikasa.erp.com
  custom_domain    varchar(255) DEFAULT NULL,       -- domain custom opsional
  plan_id          bigint UNSIGNED DEFAULT NULL,
  is_active        tinyint(1) NOT NULL DEFAULT 1,
  trial_ends_at    datetime DEFAULT NULL,
  created_at       timestamp NULL,
  updated_at       timestamp NULL,
  INDEX idx_slug (slug),
  INDEX idx_plan_id (plan_id)
);
```

### 1.2 Tabel `plans` (Paket Berlangganan)

```sql
CREATE TABLE plans (
  id               bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name             varchar(255) NOT NULL,           -- Starter, Pro, Enterprise
  price_monthly    decimal(15,2) NOT NULL DEFAULT 0,
  price_yearly     decimal(15,2) NOT NULL DEFAULT 0,
  max_users        int DEFAULT NULL,                -- NULL = unlimited
  max_products     int DEFAULT NULL,
  max_warehouses   int DEFAULT NULL,
  max_stores       int DEFAULT NULL,
  features         json DEFAULT NULL,               -- modul yang bisa diakses
  is_active        tinyint(1) NOT NULL DEFAULT 1,
  created_at       timestamp NULL,
  updated_at       timestamp NULL
);
```

### 1.3 Isi tabel `subscriptions` (saat ini kosong)

```sql
-- Ubah struktur subscriptions yang sudah ada tapi kosong
ALTER TABLE subscriptions
  ADD COLUMN tenant_id       bigint UNSIGNED NOT NULL AFTER id,
  ADD COLUMN plan_id         bigint UNSIGNED NOT NULL,
  ADD COLUMN status          ENUM('trial','active','past_due','cancelled','expired')
                             NOT NULL DEFAULT 'trial',
  ADD COLUMN billing_cycle   ENUM('monthly','yearly') NOT NULL DEFAULT 'monthly',
  ADD COLUMN started_at      datetime DEFAULT NULL,
  ADD COLUMN ends_at         datetime DEFAULT NULL,
  ADD COLUMN cancelled_at    datetime DEFAULT NULL,
  ADD INDEX idx_tenant_id (tenant_id),
  ADD INDEX idx_status (status);
```

### 1.4 Tabel `billing_invoices`

```sql
CREATE TABLE billing_invoices (
  id               bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenant_id        bigint UNSIGNED NOT NULL,
  subscription_id  bigint UNSIGNED NOT NULL,
  amount           decimal(15,2) NOT NULL,
  status           ENUM('unpaid','paid','failed') NOT NULL DEFAULT 'unpaid',
  due_date         date NOT NULL,
  paid_at          datetime DEFAULT NULL,
  created_at       timestamp NULL,
  updated_at       timestamp NULL,
  INDEX idx_tenant_id (tenant_id),
  INDEX idx_subscription_id (subscription_id)
);
```

---

## FASE 2 — Tambah `tenant_id` ke Semua Tabel

> **Urutan penting**: tambahkan dulu ke tabel induk, baru tabel anak.

### 2.1 Tabel Master / Induk

```sql
-- Users
ALTER TABLE users
  ADD COLUMN tenant_id bigint UNSIGNED DEFAULT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Products & Categories
ALTER TABLE products
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE product_categories
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Customers & Suppliers
ALTER TABLE customers
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE suppliers
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Warehouses & Stores
ALTER TABLE warehouses
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE stores
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Finance
ALTER TABLE accounts
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE taxes
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE currencies
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- HR
ALTER TABLE employees
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE departments
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE shifts
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);
```

### 2.2 Tabel Transaksi

```sql
-- Sales
ALTER TABLE sales
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE sale_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE sales_orders
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE sales_order_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE sales_quotations
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE sales_quotation_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Purchases
ALTER TABLE purchases
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE purchase_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE purchase_requests
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE purchase_request_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Invoices & Payments
ALTER TABLE invoices
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE invoice_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE payment_transactions
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Finance / Accounting
ALTER TABLE journals
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE budgets
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE budget_lines
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE expenses
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Inventory & Warehouse
ALTER TABLE inventories
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE stock_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE stock_adjustments
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE stock_adjustment_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE stock_opnames
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE stock_opname_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE warehouse_transfers
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE warehouse_transfer_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE warehouse_locations
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE delivery_orders
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE delivery_order_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- POS
ALTER TABLE pos_sessions
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE pos_transactions
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE pos_transaction_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE pos_pending_orders
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Members & Vouchers
ALTER TABLE members
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE member_point_logs
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE vouchers
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE promotions
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE promotion_products
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- HR & Payroll
ALTER TABLE attendances
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE leaves
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE payrolls
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE appraisals
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE employee_schedules
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- CRM & Projects
ALTER TABLE leads
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE tickets
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE projects
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE tasks
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE timesheets
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Sisanya
ALTER TABLE assets
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE asset_depreciations
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE activity_logs
  ADD COLUMN tenant_id bigint UNSIGNED DEFAULT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE returns
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE rfqs
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE rfq_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE good_receipts
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE good_receipt_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE bom
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE bom_items
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE production_orders
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE production_materials
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE cashier_attendances
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE fleets
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE fleet_services
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE fleet_fuel_logs
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE documents
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE document_versions
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE approvals
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

ALTER TABLE approval_rules
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);
```

---

## FASE 3 — User & Role Management Per Tenant

Ganti sistem `users.role` enum global menjadi relasi per tenant:

```sql
-- Tabel pivot: satu user bisa jadi member banyak tenant
CREATE TABLE tenant_users (
  id           bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenant_id    bigint UNSIGNED NOT NULL,
  user_id      bigint UNSIGNED NOT NULL,
  role         ENUM('owner','admin','manager','finance','warehouse','hr','user')
               NOT NULL DEFAULT 'user',
  is_active    tinyint(1) NOT NULL DEFAULT 1,
  invited_by   bigint UNSIGNED DEFAULT NULL,
  invited_at   datetime DEFAULT NULL,
  joined_at    datetime DEFAULT NULL,
  created_at   timestamp NULL,
  updated_at   timestamp NULL,
  UNIQUE KEY uq_tenant_user (tenant_id, user_id),
  INDEX idx_tenant_id (tenant_id),
  INDEX idx_user_id (user_id)
);

-- Undangan user ke tenant via email
CREATE TABLE tenant_invitations (
  id           bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenant_id    bigint UNSIGNED NOT NULL,
  email        varchar(255) NOT NULL,
  role         varchar(100) DEFAULT 'user',
  token        varchar(255) NOT NULL UNIQUE,
  expires_at   datetime NOT NULL,
  accepted_at  datetime DEFAULT NULL,
  created_at   timestamp NULL,
  updated_at   timestamp NULL,
  INDEX idx_tenant_id (tenant_id),
  INDEX idx_token (token)
);
```

> **Catatan**: kolom `users.role` dan `users.store_id` / `users.company_id` bisa
> di-deprecate bertahap setelah `tenant_users` aktif digunakan.

---

## FASE 4 — Konfigurasi Per Tenant

```sql
CREATE TABLE tenant_settings (
  id                bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenant_id         bigint UNSIGNED NOT NULL UNIQUE,

  -- Identitas
  company_name      varchar(255) DEFAULT NULL,
  logo              varchar(255) DEFAULT NULL,
  address           text DEFAULT NULL,
  phone             varchar(50) DEFAULT NULL,
  email             varchar(255) DEFAULT NULL,
  tax_number        varchar(100) DEFAULT NULL,

  -- Lokalisasi
  currency_code     varchar(10) NOT NULL DEFAULT 'IDR',
  timezone          varchar(100) NOT NULL DEFAULT 'Asia/Jakarta',
  date_format       varchar(50) NOT NULL DEFAULT 'd/m/Y',
  time_format       ENUM('12','24') NOT NULL DEFAULT '24',
  language          varchar(10) NOT NULL DEFAULT 'id',

  -- Numbering / prefix dokumen
  invoice_prefix    json DEFAULT NULL,
  -- contoh: {"sales":"INV","purchase":"PO","return":"RET"}

  -- Modul aktif
  modules_enabled   json DEFAULT NULL,
  -- contoh: ["pos","hrm","crm","manufacturing","accounting"]

  -- Konfigurasi email SMTP per tenant
  email_settings    json DEFAULT NULL,

  created_at        timestamp NULL,
  updated_at        timestamp NULL,
  INDEX idx_tenant_id (tenant_id)
);
```

---

## FASE 5 — Feature Flag Per Tenant (Opsional)

Untuk kontrol modul yang lebih granular per plan:

```sql
CREATE TABLE tenant_modules (
  id           bigint UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tenant_id    bigint UNSIGNED NOT NULL,
  module       varchar(100) NOT NULL,
  -- nilai: 'pos','hrm','crm','manufacturing','accounting',
  --        'fleet','ecommerce','lms','fsm','blog','survey'
  is_enabled   tinyint(1) NOT NULL DEFAULT 1,
  enabled_at   datetime DEFAULT NULL,
  created_at   timestamp NULL,
  updated_at   timestamp NULL,
  UNIQUE KEY uq_tenant_module (tenant_id, module),
  INDEX idx_tenant_id (tenant_id)
);
```

---

## FASE 6 — Companies Jadi Tenant-Aware

Tabel `companies` yang ada digunakan untuk struktur holding/multi-company per tenant:

```sql
ALTER TABLE companies
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);

-- Inter-company rules juga perlu tenant_id
ALTER TABLE inter_company_rules
  ADD COLUMN tenant_id bigint UNSIGNED NOT NULL AFTER id,
  ADD INDEX idx_tenant_id (tenant_id);
```

---

## FASE 7 — Data Migration (Existing Data)

Jika sudah ada data sebelumnya, jalankan ini setelah FASE 1-6:

```sql
-- 1. Buat tenant default untuk data existing
INSERT INTO tenants (name, slug, is_active, created_at, updated_at)
VALUES ('Default Tenant', 'default', 1, NOW(), NOW());

SET @default_tenant_id = LAST_INSERT_ID();

-- 2. Isi tenant_id = default untuk semua tabel yang sudah punya data
UPDATE users                 SET tenant_id = @default_tenant_id WHERE tenant_id IS NULL OR tenant_id = 0;
UPDATE products              SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE customers             SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE suppliers             SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE sales                 SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE purchases             SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE invoices              SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE warehouses            SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE stores                SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE accounts              SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
UPDATE employees             SET tenant_id = @default_tenant_id WHERE tenant_id = 0;
-- ... lanjutkan untuk semua tabel

-- 3. Insert user existing ke tenant_users
INSERT INTO tenant_users (tenant_id, user_id, role, is_active, joined_at, created_at, updated_at)
SELECT @default_tenant_id, id, role, 1, created_at, NOW(), NOW()
FROM users;
```

---

## Checklist Migrasi

```
FASE 1 — Core Foundation
  [ ] CREATE TABLE tenants
  [ ] CREATE TABLE plans
  [ ] ALTER TABLE subscriptions (isi strukturnya)
  [ ] CREATE TABLE billing_invoices

FASE 2 — Tambah tenant_id
  [ ] Tabel master (users, products, customers, suppliers, warehouses, stores, accounts)
  [ ] Tabel transaksi (sales, purchases, invoices, pos_*)
  [ ] Tabel inventory (stock_items, warehouse_transfers, delivery_orders)
  [ ] Tabel HR (employees, attendances, payrolls, leaves)
  [ ] Tabel lainnya (leads, tickets, projects, assets, fleets, bom)

FASE 3 — User Management
  [ ] CREATE TABLE tenant_users
  [ ] CREATE TABLE tenant_invitations

FASE 4 — Konfigurasi
  [ ] CREATE TABLE tenant_settings

FASE 5 — Feature Flag (opsional)
  [ ] CREATE TABLE tenant_modules

FASE 6 — Companies
  [ ] ALTER TABLE companies ADD tenant_id
  [ ] ALTER TABLE inter_company_rules ADD tenant_id

FASE 7 — Data Migration
  [ ] Insert default tenant
  [ ] Update semua tenant_id = default untuk data existing
  [ ] Migrate users ke tenant_users
```

---

## Tabel yang TIDAK Perlu `tenant_id`

Tabel-tabel berikut bersifat global/sistem, tidak perlu diisolasi per tenant:

| Tabel | Alasan |
|---|---|
| `migrations` | Sistem Laravel, global |
| `jobs`, `failed_jobs`, `job_batches` | Queue sistem, global |
| `cache`, `cache_locks` | Cache sistem, global |
| `sessions` | Session auth, global |
| `password_reset_tokens` | Auth, global |
| `currencies` | Referensi global (atau bisa di-override via `tenant_settings`) |
| `payment_providers` | Dikonfigurasi admin platform |
| `plans` | Master plan SaaS, milik platform |
| `tenants` | Root tenant table itu sendiri |
