<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fleet_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fleet_id')->constrained('fleets')->cascadeOnDelete();
            $table->date('service_date');
            $table->string('description');
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('provider')->nullable();
            $table->enum('status', ['planned', 'in_progress', 'completed', 'canceled'])->default('planned');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fleet_services');
    }
};
