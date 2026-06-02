<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleets', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate')->unique();
            $table->string('model');
            $table->foreignId('driver_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->enum('status', ['active', 'in_service', 'out_of_service', 'sold'])->default('active');
            $table->decimal('odometer', 10, 2)->default(0);
            $table->date('acquisition_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleets');
    }
};
