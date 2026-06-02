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
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quality_checkpoint_id')->constrained('quality_checkpoints')->onDelete('cascade');
            $table->string('reference_type'); // e.g. GoodReceipt, ProductionOrder
            $table->unsignedBigInteger('reference_id');
            $table->foreignId('checked_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['passed', 'failed'])->default('passed');
            $table->text('notes')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_checks');
    }
};
