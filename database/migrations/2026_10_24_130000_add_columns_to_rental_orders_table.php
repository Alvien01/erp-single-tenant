<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('rental_orders', 'rental_number')) {
                $table->string('rental_number', 50)->nullable()->after('id');
            }
            if (!Schema::hasColumn('rental_orders', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->after('rental_number');
            }
            if (!Schema::hasColumn('rental_orders', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete()->after('customer_id');
            }
            if (!Schema::hasColumn('rental_orders', 'item_name')) {
                $table->string('item_name')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('rental_orders', 'quantity')) {
                $table->decimal('quantity', 10, 2)->default(1)->after('item_name');
            }
            if (!Schema::hasColumn('rental_orders', 'rental_rate')) {
                $table->decimal('rental_rate', 15, 2)->default(0)->after('quantity');
            }
            if (!Schema::hasColumn('rental_orders', 'rate_unit')) {
                $table->string('rate_unit', 30)->default('daily')->after('rental_rate');
            }
            if (!Schema::hasColumn('rental_orders', 'duration')) {
                $table->integer('duration')->default(1)->after('rate_unit');
            }
            if (!Schema::hasColumn('rental_orders', 'start_date')) {
                $table->date('start_date')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('rental_orders', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('rental_orders', 'return_date')) {
                $table->date('return_date')->nullable()->after('end_date');
            }
            if (!Schema::hasColumn('rental_orders', 'deposit_amount')) {
                $table->decimal('deposit_amount', 15, 2)->default(0)->after('return_date');
            }
            if (!Schema::hasColumn('rental_orders', 'delivery_fee')) {
                $table->decimal('delivery_fee', 15, 2)->default(0)->after('deposit_amount');
            }
            if (!Schema::hasColumn('rental_orders', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('delivery_fee');
            }
            if (!Schema::hasColumn('rental_orders', 'status')) {
                $table->string('status', 30)->default('draft')->after('total_amount');
            }
            if (!Schema::hasColumn('rental_orders', 'payment_status')) {
                $table->string('payment_status', 30)->default('unpaid')->after('status');
            }
            if (!Schema::hasColumn('rental_orders', 'condition_out')) {
                $table->text('condition_out')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('rental_orders', 'condition_in')) {
                $table->text('condition_in')->nullable()->after('condition_out');
            }
            if (!Schema::hasColumn('rental_orders', 'notes')) {
                $table->text('notes')->nullable()->after('condition_in');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rental_orders', function (Blueprint $table) {
            $cols = [
                'rental_number', 'customer_id', 'product_id', 'item_name', 
                'quantity', 'rental_rate', 'rate_unit', 'duration',
                'start_date', 'end_date', 'return_date', 'deposit_amount',
                'delivery_fee', 'total_amount', 'status', 'payment_status',
                'condition_out', 'condition_in', 'notes'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('rental_orders', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
