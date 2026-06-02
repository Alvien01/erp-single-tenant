<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================
        // 1. MULTI-COMPANY — add company_id to users table
        // =====================================================
        Schema::table('companies', function (Blueprint $table) {
            $table->string('code')->nullable()->unique()->after('id');
            $table->string('logo')->nullable()->after('tax_number');
            $table->string('currency_code')->default('IDR')->after('logo');
            $table->foreignId('parent_company_id')->nullable()->constrained('companies')->nullOnDelete()->after('id');
            $table->boolean('is_active')->default(true)->after('currency_code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->after('role');
        });

        Schema::create('inter_company_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('target_company_id')->constrained('companies')->cascadeOnDelete();
            $table->enum('rule_type', ['purchase_to_sale', 'sale_to_purchase', 'transfer']);
            $table->boolean('auto_create')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =====================================================
        // 2. WEBSITE, CMS, BLOG, APPOINTMENTS, EVENTS, eLEARNING
        // =====================================================
        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->string('featured_image')->nullable();
            $table->string('category')->nullable();
            $table->string('tags')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('location')->nullable();
            $table->enum('status', ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('scheduled');
            $table->enum('type', ['meeting', 'consultation', 'service', 'other'])->default('meeting');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['assigned_to', 'start_time'], 'idx_appointments_user_time');
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('venue')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('type', ['conference', 'seminar', 'workshop', 'webinar', 'exhibition'])->default('seminar');
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->integer('max_attendees')->nullable();
            $table->decimal('ticket_price', 15, 2)->default(0);
            $table->foreignId('organizer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('event_attendees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->enum('status', ['registered', 'confirmed', 'attended', 'cancelled'])->default('registered');
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->integer('duration_hours')->default(0);
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->enum('type', ['video', 'article', 'quiz', 'assignment'])->default('article');
            $table->integer('duration_minutes')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('progress', 5, 2)->default(0);
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'dropped'])->default('enrolled');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });

        // =====================================================
        // 3. ADVANCED LOGISTICS — Dropship, Carriers, Cross-Dock
        // =====================================================
        Schema::create('delivery_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('provider')->nullable(); // jne, sicepat, jnt, fedex
            $table->string('tracking_url')->nullable();
            $table->decimal('default_cost', 15, 2)->default(0);
            $table->decimal('margin_percent', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('dropship_orders', function (Blueprint $table) {
            $table->id();
            $table->string('dropship_number')->unique();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'confirmed', 'shipped', 'delivered', 'cancelled'])->default('draft');
            $table->string('tracking_number')->nullable();
            $table->foreignId('carrier_id')->nullable()->constrained('delivery_carriers')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('dropship_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dropship_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });

        // =====================================================
        // 4. ADVANCED MANUFACTURING — Routings, Subcontracting, ECO, MPS
        // =====================================================
        Schema::create('mrp_routings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('bom_id')->constrained('bom')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('routing_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('routing_id')->constrained('mrp_routings')->cascadeOnDelete();
            $table->foreignId('work_center_id')->constrained('work_centers')->cascadeOnDelete();
            $table->string('name');
            $table->integer('sequence')->default(10);
            $table->decimal('time_cycle_minutes', 10, 2)->default(0);
            $table->decimal('setup_time_minutes', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('subcontracting_orders', function (Blueprint $table) {
            $table->id();
            $table->string('subcontract_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bom_id')->nullable()->constrained('bom')->nullOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->date('send_date')->nullable();
            $table->date('expected_return_date')->nullable();
            $table->date('actual_return_date')->nullable();
            $table->decimal('cost_per_unit', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->enum('status', ['draft', 'sent', 'in_progress', 'received', 'cancelled'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('eco_requests', function (Blueprint $table) {
            $table->id();
            $table->string('eco_number')->unique();
            $table->foreignId('bom_id')->constrained('bom')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('reason')->nullable();
            $table->string('old_bom_version')->nullable();
            $table->string('new_bom_version')->nullable();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected', 'applied'])->default('draft');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('production_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('period'); // YYYY-MM
            $table->decimal('forecast_demand', 10, 2)->default(0);
            $table->decimal('actual_demand', 10, 2)->default(0);
            $table->decimal('planned_production', 10, 2)->default(0);
            $table->decimal('actual_production', 10, 2)->default(0);
            $table->decimal('opening_stock', 10, 2)->default(0);
            $table->decimal('closing_stock', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'period'], 'idx_mps_product_period');
        });

        // =====================================================
        // 5. ADVANCED ACCOUNTING — Deferred Rev/Exp, Payment Gateway
        // =====================================================
        Schema::create('deferred_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['revenue', 'expense']);
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recognition_account_id')->constrained('accounts')->cascadeOnDelete();
            $table->decimal('total_amount', 15, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('periods'); // number of months
            $table->decimal('amount_per_period', 15, 2);
            $table->decimal('recognized_amount', 15, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // midtrans, stripe, xendit
            $table->string('provider_type')->nullable(); // bank_transfer, ewallet, credit_card
            $table->json('credentials')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_test_mode')->default(true);
            $table->decimal('fee_percent', 5, 2)->default(0);
            $table->decimal('fee_fixed', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('payment_provider_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'refunded', 'expired'])->default('pending');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('external_id')->nullable(); // ID dari payment gateway
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id'], 'idx_payment_tx_ref');
        });

        // =====================================================
        // 6. MARKETING AUTOMATION & SURVEYS
        // =====================================================
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['manual', 'on_create', 'on_update', 'scheduled', 'email_opened', 'form_submit']);
            $table->string('trigger_model')->nullable(); // e.g. Lead, Customer
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_id')->constrained('automation_workflows')->cascadeOnDelete();
            $table->integer('sequence')->default(1);
            $table->enum('action_type', ['send_email', 'send_sms', 'wait', 'condition', 'assign_user', 'create_task', 'update_field']);
            $table->json('action_config')->nullable();
            $table->integer('wait_hours')->default(0);
            $table->string('condition_field')->nullable();
            $table->string('condition_operator')->nullable();
            $table->string('condition_value')->nullable();
            $table->timestamps();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['survey', 'quiz', 'feedback', 'appraisal', 'certification'])->default('survey');
            $table->enum('status', ['draft', 'open', 'closed'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('open_at')->nullable();
            $table->timestamp('close_at')->nullable();
            $table->timestamps();
        });

        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->enum('type', ['text', 'textarea', 'radio', 'checkbox', 'select', 'rating', 'date'])->default('text');
            $table->json('options')->nullable(); // for radio/checkbox/select
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('respondent_email')->nullable();
            $table->text('answer')->nullable();
            $table->timestamps();
        });

        // =====================================================
        // 7. FIELD SERVICE MANAGEMENT (FSM)
        // =====================================================
        Schema::create('field_service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('fsm_number')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['draft', 'scheduled', 'in_progress', 'completed', 'invoiced', 'cancelled'])->default('draft');
            $table->dateTime('scheduled_date')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('location_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->text('customer_signature')->nullable(); // base64
            $table->timestamps();

            $table->index(['assigned_to', 'scheduled_date'], 'idx_fsm_user_date');
        });

        Schema::create('fsm_worksheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_service_order_id')->constrained()->cascadeOnDelete();
            $table->text('work_performed')->nullable();
            $table->text('materials_used')->nullable();
            $table->decimal('labor_hours', 8, 2)->default(0);
            $table->decimal('labor_cost', 15, 2)->default(0);
            $table->decimal('material_cost', 15, 2)->default(0);
            $table->text('technician_notes')->nullable();
            $table->text('customer_feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('fsm_parts_used', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_service_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fsm_parts_used');
        Schema::dropIfExists('fsm_worksheets');
        Schema::dropIfExists('field_service_orders');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('workflow_steps');
        Schema::dropIfExists('automation_workflows');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_providers');
        Schema::dropIfExists('deferred_entries');
        Schema::dropIfExists('production_schedules');
        Schema::dropIfExists('eco_requests');
        Schema::dropIfExists('subcontracting_orders');
        Schema::dropIfExists('routing_operations');
        Schema::dropIfExists('mrp_routings');
        Schema::dropIfExists('dropship_order_items');
        Schema::dropIfExists('dropship_orders');
        Schema::dropIfExists('delivery_carriers');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('event_attendees');
        Schema::dropIfExists('events');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('inter_company_rules');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['parent_company_id']);
            $table->dropColumn(['code', 'logo', 'currency_code', 'parent_company_id', 'is_active']);
        });
    }
};
