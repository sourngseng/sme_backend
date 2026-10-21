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
        // 1. Marketing Campaigns
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 180);
            $table->text('description')->nullable();
            $table->timestampTz('start_at');
            $table->timestampTz('end_at');
            $table->string('status', 30)->default('draft'); // draft, scheduled, active, ended
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestampTz('created_at')->useCurrent();
        });

        // 2. Promotions & Coupons
        Schema::create('promotions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            $table->string('code', 60)->nullable()->unique();
            $table->string('type', 40); // percentage, fixed, free_delivery
            $table->decimal('value', 10, 2)->default(0);
            $table->bigInteger('max_discount_minor')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->string('status', 30)->default('active');
            $table->timestampTz('created_at')->useCurrent();
        });

        // 3. Meal Feedback
        Schema::create('meal_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_schedule_id')->constrained('order_schedules')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('guardian_id')->nullable()->constrained('guardians')->nullOnDelete();
            $table->smallInteger('rating')->nullable(); // 1-5
            $table->smallInteger('taste_rating')->nullable();
            $table->smallInteger('portion_rating')->nullable();
            $table->smallInteger('quality_rating')->nullable();
            $table->text('comment')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['student_id', 'created_at']);
        });

        // 4. Nutrition Consumption Logs
        Schema::create('nutrition_consumption_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_schedule_id')->constrained('order_schedules')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('consumption_status', 40)->default('unknown'); // consumed, partial, not_consumed, unknown
            $table->decimal('estimated_consumed_pct', 5, 2)->nullable();
            $table->decimal('calories_consumed_kcal', 8, 2)->nullable();
            $table->decimal('protein_consumed_g', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('recorded_at')->useCurrent();

            $table->index(['student_id', 'recorded_at']);
        });

        // 5. Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number', 40)->unique();
            $table->foreignUuid('opened_by')->constrained('users')->cascadeOnDelete();
            $table->string('category', 80);
            $table->string('priority', 30)->default('normal'); // low, normal, high, urgent
            $table->string('status', 30)->default('open'); // open, pending, resolved, closed
            $table->string('subject', 200);
            $table->text('description');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
        });

        // 6. Support Messages
        Schema::create('support_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_id')->constrained('support_tickets')->cascadeOnDelete();
            $table->foreignUuid('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->text('attachment_url')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        // 7. Report Snapshots
        Schema::create('report_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('report_type', 80);
            $table->date('period_start');
            $table->date('period_end');
            $table->jsonb('filters')->nullable();
            $table->jsonb('data');
            $table->foreignUuid('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('generated_at')->useCurrent();
        });

        // 8. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type', 80);
            $table->string('title', 200);
            $table->text('body');
            $table->jsonb('data')->nullable();
            $table->timestampTz('read_at')->nullable();
            $table->timestampsTz();

            $table->index(['user_id', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('report_snapshots');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('nutrition_consumption_logs');
        Schema::dropIfExists('meal_feedback');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('campaigns');
    }
};
