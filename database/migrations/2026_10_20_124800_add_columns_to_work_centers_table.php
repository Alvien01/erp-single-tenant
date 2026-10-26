<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_centers', function (Blueprint $table) {
            if (!Schema::hasColumn('work_centers', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('work_centers', 'name')) {
                $table->string('name')->nullable()->after('code');
            }
            if (!Schema::hasColumn('work_centers', 'cost_per_hour')) {
                $table->decimal('cost_per_hour', 15, 2)->default(0)->after('name');
            }
            if (!Schema::hasColumn('work_centers', 'capacity')) {
                $table->decimal('capacity', 10, 2)->default(1)->after('cost_per_hour');
            }
            if (!Schema::hasColumn('work_centers', 'status')) {
                $table->string('status')->default('active')->after('capacity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_centers', function (Blueprint $table) {
            $cols = ['code', 'name', 'cost_per_hour', 'capacity', 'status'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('work_centers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
