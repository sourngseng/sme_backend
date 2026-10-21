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
        // 1. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number', 40)->unique();
            $table->foreignUuid('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('status', 40)->default('pending'); // pending, confirmed, preparing, ready, assigned, delivered, cancelled, refunded, failed
            $table->bigInteger('subtotal_minor')->default(0);
            $table->bigInteger('delivery_fee_minor')->default(0);
            $table->bigInteger('discount_minor')->default(0);
            $table->bigInteger('commission_minor')->default(0);
            $table->bigInteger('total_minor')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->text('notes')->nullable();
            $table->timestampTz('placed_at')->nullable();
            $table->timestampsTz();

            $table->index(['student_id', 'created_at']);
            $table->index(['school_id', 'created_at']);
            $table->index(['restaurant_id', 'status']);
            $table->index('order_number');
        });

        // 2. Order Items (Snapshotting price and nutrition)
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('menu_version_id')->constrained('menu_versions')->cascadeOnDelete();
            $table->string('item_name_snapshot', 180);
            $table->bigInteger('unit_price_minor')->default(0);
            $table->integer('quantity')->default(1);
            $table->bigInteger('line_total_minor')->default(0);
            $table->decimal('calories_kcal_snapshot', 8, 2)->nullable();
            $table->jsonb('nutrition_snapshot')->nullable();
            $table->timestampsTz();
        });

        // 3. Order Schedules (1-7 day individual meal tracking)
        Schema::create('order_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignUuid('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->date('delivery_date');
            $table->string('shift', 30)->default('lunch');
            $table->foreignUuid('school_location_id')->nullable()->constrained('school_locations')->nullOnDelete();
            $table->string('status', 40)->default('scheduled'); // scheduled, prepared, delivered, skipped, cancelled
            $table->timestampTz('cutoff_at')->nullable();
            $table->timestampTz('delivered_at')->nullable();
            $table->timestampsTz();

            $table->index(['delivery_date', 'shift', 'status']);
            $table->index(['school_location_id', 'delivery_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_schedules');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
