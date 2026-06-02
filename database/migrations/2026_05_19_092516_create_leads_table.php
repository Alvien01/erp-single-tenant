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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('contact_name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->decimal('expected_revenue', 15, 2)->default(0);
            $table->integer('probability')->default(10); // percentage
            $table->enum('status', ['new', 'qualified', 'proposition', 'won', 'lost'])->default('new');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Salesperson assigned
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
