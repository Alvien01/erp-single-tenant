<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add location columns to existing attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in');
            $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            $table->decimal('check_in_distance', 10, 2)->nullable()->after('check_in_longitude'); // meters from office
            $table->decimal('check_out_distance', 10, 2)->nullable()->after('check_out_longitude');
            $table->string('check_in_address')->nullable()->after('check_in_distance');
            $table->string('check_out_address')->nullable()->after('check_out_distance');
            $table->text('notes')->nullable()->after('status');
        });

        // Create attendance settings table
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('office_name')->default('Kantor Pusat');
            $table->decimal('office_latitude', 10, 7);
            $table->decimal('office_longitude', 10, 7);
            $table->integer('allowed_radius')->default(200); // meters
            $table->time('work_start_time')->default('08:00');
            $table->time('work_end_time')->default('17:00');
            $table->integer('late_tolerance_minutes')->default(15); // grace period
            $table->integer('early_checkin_minutes')->default(60); // can check in X min before start
            $table->boolean('require_location')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_latitude', 'check_in_longitude',
                'check_out_latitude', 'check_out_longitude',
                'check_in_distance', 'check_out_distance',
                'check_in_address', 'check_out_address',
                'notes',
            ]);
        });

        Schema::dropIfExists('attendance_settings');
    }
};
