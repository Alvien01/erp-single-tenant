<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->string('module_type'); // e.g. PurchaseRequest, SalesOrder, Leave, Payroll
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable();
            $table->enum('role_required', ['admin', 'manager', 'hr', 'warehouse', 'finance', 'user']);
            $table->integer('sequence')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_rule_id')->constrained()->cascadeOnDelete();
            $table->string('reference_type'); // Model class, e.g. App\Models\PurchaseRequest
            $table->unsignedBigInteger('reference_id');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // User who approved/rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('approval_rules');
    }
};
