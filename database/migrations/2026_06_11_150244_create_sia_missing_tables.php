<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter journals table to add journal_type
        Schema::table('journals', function (Blueprint $table) {
            $table->enum('journal_type', ['general', 'adjustment', 'closing'])->default('general')->after('type');
        });

        // 2. Alter customers table to add credit limit fields
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('credit_limit', 15, 2)->default(0)->after('type');
            $table->decimal('credit_used', 15, 2)->default(0)->after('credit_limit');
        });

        // 3. bank_accounts
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('bank_name');
            $table->string('account_number');
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });

        // 4. cash_transactions
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['in', 'out']);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->timestamps();
        });

        // 5. cash_transfers
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('from_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('to_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 6. payment_receipts
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('Cash'); // Cash, Transfer, Cheque, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. payment_disbursements
        Schema::create('payment_disbursements', function (Blueprint $table) {
            $table->id();
            $table->string('disbursement_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->nullOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('Cash');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. payment_schedules
        Schema::create('payment_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
            $table->foreignId('purchase_id')->constrained('purchases')->cascadeOnDelete();
            $table->date('due_date');
            $table->decimal('planned_amount', 15, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // 9. tax_invoices
        Schema::create('tax_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->enum('type', ['masukan', 'keluaran']);
            $table->date('date');
            $table->decimal('dpp', 15, 2);
            $table->decimal('ppn', 15, 2);
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft');
            $table->timestamps();
        });

        // 10. withholding_taxes
        Schema::create('withholding_taxes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pph21', 'pph22', 'pph23', 'pph25', 'pph29']);
            $table->decimal('amount', 15, 2);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });

        // 11. payroll_components
        Schema::create('payroll_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['allowance', 'deduction']);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });

        // 12. sales_commissions
        Schema::create('sales_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });

        // 13. period_closings
        Schema::create('period_closings', function (Blueprint $table) {
            $table->id();
            $table->date('closing_date');
            $table->foreignId('closed_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['active', 'closed'])->default('closed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 14. cost_centers
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_centers');
        Schema::dropIfExists('period_closings');
        Schema::dropIfExists('sales_commissions');
        Schema::dropIfExists('payroll_components');
        Schema::dropIfExists('withholding_taxes');
        Schema::dropIfExists('tax_invoices');
        Schema::dropIfExists('payment_schedules');
        Schema::dropIfExists('payment_disbursements');
        Schema::dropIfExists('payment_receipts');
        Schema::dropIfExists('cash_transfers');
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('bank_accounts');

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['credit_limit', 'credit_used']);
        });

        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('journal_type');
        });
    }
};
