<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_quotations', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
            $table->string('customer_name')->nullable()->after('customer_id');
            $table->date('quotation_date')->nullable()->after('customer_name');
            $table->decimal('total_amount', 15, 2)->default(0)->after('valid_until');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('total_amount');
            $table->string('tax_type', 20)->default('pph_05')->after('discount_amount');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_type');
            $table->decimal('shipping_price', 15, 2)->default(0)->after('tax_amount');
            $table->decimal('grand_total', 15, 2)->default(0)->after('shipping_price');
            $table->text('notes')->nullable()->after('status');
        });

        Schema::table('sales_quotation_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('product_name')->nullable()->after('product_id');
            $table->string('model')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_quotation_items', function (Blueprint $table) {
            $table->dropColumn(['product_name', 'model']);
            $table->foreignId('product_id')->nullable(false)->change();
        });

        Schema::table('sales_quotations', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name',
                'quotation_date',
                'total_amount',
                'discount_amount',
                'tax_type',
                'tax_amount',
                'shipping_price',
                'grand_total',
                'notes',
            ]);
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
