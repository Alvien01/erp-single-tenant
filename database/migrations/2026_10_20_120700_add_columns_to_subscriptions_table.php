<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'subscription_number')) {
                $table->string('subscription_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('subscriptions', 'customer_id')) {
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete()->after('subscription_number');
            }
            if (!Schema::hasColumn('subscriptions', 'plan_name')) {
                $table->string('plan_name')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('subscriptions', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('plan_name');
            }
            if (!Schema::hasColumn('subscriptions', 'billing_cycle')) {
                $table->string('billing_cycle')->default('monthly')->after('price'); // monthly, quarterly, yearly
            }
            if (!Schema::hasColumn('subscriptions', 'start_date')) {
                $table->date('start_date')->nullable()->after('billing_cycle');
            }
            if (!Schema::hasColumn('subscriptions', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('subscriptions', 'status')) {
                $table->string('status')->default('active')->after('end_date'); // active, paused, cancelled, expired
            }
            if (!Schema::hasColumn('subscriptions', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $cols = ['subscription_number', 'customer_id', 'plan_name', 'price', 'billing_cycle', 'start_date', 'end_date', 'status', 'notes'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('subscriptions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
