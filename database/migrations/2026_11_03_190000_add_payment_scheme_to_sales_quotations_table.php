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
            if (!Schema::hasColumn('sales_quotations', 'payment_scheme')) {
                $table->string('payment_scheme', 255)->nullable()->default('100% di muka')->after('valid_until');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_quotations', function (Blueprint $table) {
            if (Schema::hasColumn('sales_quotations', 'payment_scheme')) {
                $table->dropColumn('payment_scheme');
            }
        });
    }
};

