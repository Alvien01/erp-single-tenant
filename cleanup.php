<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

// Disable FK checks for clean drop
DB::statement('SET FOREIGN_KEY_CHECKS=0');

Schema::dropIfExists('promotion_products');
Schema::dropIfExists('pos_transaction_items');
Schema::dropIfExists('pos_transactions');
Schema::dropIfExists('pos_sessions');
Schema::dropIfExists('pos_pending_orders');
Schema::dropIfExists('member_point_logs');
Schema::dropIfExists('vouchers');
Schema::dropIfExists('promotions');
Schema::dropIfExists('members');
Schema::dropIfExists('stock_opname_items');
Schema::dropIfExists('stock_opnames');
Schema::dropIfExists('cashier_attendances');
Schema::dropIfExists('product_units');
Schema::dropIfExists('product_recipes');
Schema::dropIfExists('stores');

try {
    Schema::table('users', function(Blueprint $table) {
        if (Schema::hasColumn('users', 'store_id')) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        }
    });
} catch (\Exception $e) {
    // Column might not exist
}

try {
    $cols = [];
    foreach (['barcode','sku','purchase_price','image'] as $c) {
        if (Schema::hasColumn('products', $c)) $cols[] = $c;
    }
    if ($cols) {
        Schema::table('products', function(Blueprint $table) use ($cols) {
            $table->dropColumn($cols);
        });
    }
} catch (\Exception $e) {}

// Also remove the migration record so it can re-run
DB::table('migrations')->where('migration', '2026_05_28_140000_create_pos_system_tables')->delete();

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Cleanup done!\n";
