<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            if (!Schema::hasColumn('return_items', 'quantity')) {
                $table->decimal('quantity', 10, 2)->nullable()->after('qty');
            }
            if (!Schema::hasColumn('return_items', 'reason')) {
                $table->string('reason')->nullable()->after('subtotal');
            }
            // Ensure unit_price and subtotal have default value
            $table->decimal('unit_price', 15, 2)->default(0)->change();
            $table->decimal('subtotal', 15, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            if (Schema::hasColumn('return_items', 'quantity')) {
                $table->dropColumn('quantity');
            }
            if (Schema::hasColumn('return_items', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }
};
