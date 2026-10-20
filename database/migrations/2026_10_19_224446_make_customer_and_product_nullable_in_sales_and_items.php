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
            $table->foreignId('customer_id')->nullable()->change();
            $table->string('customer_name')->nullable()->after('customer_id');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->change();
            $table->string('product_name')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->foreignId('product_id')->nullable(false)->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('customer_name');
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }
};
