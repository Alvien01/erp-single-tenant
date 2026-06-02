<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Stores / Branches ──────────────────────────────────────
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(11.00); // PPN 11%
            $table->decimal('service_charge_rate', 5, 2)->default(0);
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receipt_header')->nullable();
            $table->string('receipt_footer')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── Alter users: add store_id and extend roles ─────────────
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        // ── Alter products: add POS fields ─────────────────────────
        Schema::table('products', function (Blueprint $table) {
            $table->string('barcode')->nullable()->unique()->after('code');
            $table->string('sku')->nullable()->after('barcode');
            $table->decimal('purchase_price', 15, 2)->default(0)->after('price');
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('unit');
            }
        });

        // ── Product Units (multiple units per product) ─────────────
        Schema::create('product_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('unit_name'); // e.g. pack, dus, karton
            $table->decimal('conversion_factor', 10, 4)->default(1); // 1 dus = 12 pcs -> 12
            $table->decimal('price', 15, 2)->default(0);
            $table->string('barcode')->nullable();
            $table->timestamps();
        });

        // ── Product Recipes (racikan / finished goods) ─────────────
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // finished good
            $table->foreignId('ingredient_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('qty', 10, 4);
            $table->string('unit')->default('pcs');
            $table->timestamps();
        });

        // ── Members & Loyalty ──────────────────────────────────────
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code', 30)->unique();
            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('tier', ['bronze', 'silver', 'gold'])->default('bronze');
            $table->integer('total_points')->default(0);
            $table->decimal('total_spending', 15, 2)->default(0);
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('member_point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->integer('points'); // positive = earn, negative = redeem
            $table->enum('type', ['earn', 'redeem', 'adjust', 'expire']);
            $table->string('reference')->nullable(); // e.g. transaction number
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // ── Promotions & Discounts ─────────────────────────────────
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'percentage',     // diskon %
                'fixed',          // diskon nominal
                'bogo',           // buy X get Y free
                'bundle',         // bundle produk
                'tiered',         // bertingkat (belanja 100k diskon 5%, 200k diskon 10%)
                'member_only',    // khusus member
            ]);
            $table->decimal('value', 15, 2)->default(0); // discount value
            $table->integer('buy_qty')->nullable();      // for BOGO: buy X
            $table->integer('free_qty')->nullable();     // for BOGO: get Y free
            $table->decimal('min_purchase', 15, 2)->default(0);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->json('tiered_rules')->nullable(); // for tiered: [{min: 100000, discount: 5}, ...]
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->enum('member_tier', ['bronze', 'silver', 'gold'])->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable(); // promo jam tertentu
            $table->time('end_time')->nullable();
            $table->json('active_days')->nullable(); // ["monday","tuesday",...]
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promotion_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // ── Vouchers ───────────────────────────────────────────────
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 15, 2);
            $table->decimal('min_purchase', 15, 2)->default(0);
            $table->decimal('max_discount', 15, 2)->nullable();
            $table->integer('max_uses')->default(1);
            $table->integer('used_count')->default(0);
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── POS Sessions (Shift / EOD) ─────────────────────────────
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // cashier
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->nullable();
            $table->decimal('expected_cash', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->datetime('opened_at');
            $table->datetime('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();
        });

        // ── POS Transactions ───────────────────────────────────────
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // cashier
            $table->foreignId('pos_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete(); // link to accounting

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->string('discount_description')->nullable();
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('service_charge', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->enum('payment_method', ['cash', 'qris', 'bank_transfer', 'multi'])->default('cash');
            $table->decimal('cash_received', 15, 2)->default(0);
            $table->decimal('cash_change', 15, 2)->default(0);
            $table->string('qris_reference')->nullable();

            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('promotion_id')->nullable()->constrained()->nullOnDelete();

            $table->integer('points_earned')->default(0);
            $table->integer('points_redeemed')->default(0);

            $table->enum('status', ['completed', 'voided', 'refunded'])->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['store_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('product_name'); // snapshot
            $table->string('product_code'); // snapshot
            $table->decimal('unit_price', 15, 2);
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('pcs');
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Pending Orders ─────────────────────────────────────────
        Schema::create('pos_pending_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_name')->nullable(); // e.g. "Meja 5", "Pak Ahmad"
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->json('cart_data'); // JSON snapshot of cart
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // ── Stock Opname ───────────────────────────────────────────
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number')->unique();
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('opname_date');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'approved'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('system_qty', 10, 2);
            $table->decimal('actual_qty', 10, 2);
            $table->decimal('difference', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ── Cashier Attendance ─────────────────────────────────────
        Schema::create('cashier_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'leave'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_attendances');
        Schema::dropIfExists('stock_opname_items');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('pos_pending_orders');
        Schema::dropIfExists('pos_transaction_items');
        Schema::dropIfExists('pos_transactions');
        Schema::dropIfExists('pos_sessions');
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('promotion_products');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('member_point_logs');
        Schema::dropIfExists('members');
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('product_units');

        Schema::table('products', function (Blueprint $table) {
            $cols = [];
            foreach (['barcode', 'sku', 'purchase_price', 'image'] as $col) {
                if (Schema::hasColumn('products', $col)) {
                    $cols[] = $col;
                }
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });

        Schema::dropIfExists('stores');
    }
};
