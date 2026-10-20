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
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('shipping_price', 15, 2)->default(0)->after('total_amount');
            $table->decimal('discount_amount', 15, 2)->default(0)->after('shipping_price');
            $table->string('tax_type', 20)->default('pph_05')->after('discount_amount');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->string('model')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('model');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['shipping_price', 'discount_amount', 'tax_type']);
        });
    }
};
