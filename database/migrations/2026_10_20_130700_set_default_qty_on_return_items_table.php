<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            $table->decimal('qty', 10, 2)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('return_items', function (Blueprint $table) {
            $table->decimal('qty', 10, 2)->nullable(false)->change();
        });
    }
};
