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
        // 1. Kitchen Production Batches
        Schema::create('production_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->date('production_date');
            $table->string('shift', 30);
            $table->string('status', 30)->default('planned'); // planned, preparing, ready, dispatched
            $table->timestampsTz();
        });

        // 2. Kitchen Production Items
        Schema::create('production_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('production_batch_id')->constrained('production_batches')->cascadeOnDelete();
            $table->foreignUuid('menu_version_id')->constrained('menu_versions')->cascadeOnDelete();
            $table->integer('total_quantity');
            $table->integer('prepared_quantity')->default(0);
            $table->string('status', 30)->default('pending');
            $table->timestampsTz();
        });

        // 3. Drivers
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('driver_code', 50)->unique();
            $table->string('phone', 30)->nullable();
            $table->string('vehicle_type', 50)->nullable();
            $table->string('vehicle_plate', 30)->nullable();
            $table->string('license_no', 100)->nullable();
            $table->string('status', 30)->default('active'); // active, inactive, suspended
            $table->boolean('self_delivery')->default(false);
            $table->timestampsTz();
        });

        // 4. Delivery Batches
        Schema::create('delivery_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignUuid('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->date('delivery_date');
            $table->string('shift', 30)->default('lunch');
            $table->string('status', 40)->default('planned'); // planned, assigned, picked_up, in_transit, completed, cancelled
            $table->timestampTz('pickup_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestampsTz();

            $table->index(['delivery_date', 'shift', 'status']);
        });

        // 5. Delivery Stops
        Schema::create('delivery_stops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('delivery_batch_id')->constrained('delivery_batches')->cascadeOnDelete();
            $table->foreignUuid('order_schedule_id')->constrained('order_schedules')->cascadeOnDelete();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('school_location_id')->nullable()->constrained('school_locations')->nullOnDelete();
            $table->integer('sequence_no');
            $table->string('status', 40)->default('pending'); // pending, picked_up, delivered, failed, returned
            $table->timestampTz('arrived_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->string('exception_code', 80)->nullable();
            $table->text('exception_note')->nullable();
            $table->timestampsTz();

            $table->index(['delivery_batch_id', 'sequence_no']);
        });

        // 6. Proof of Delivery
        Schema::create('proof_of_delivery', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('delivery_stop_id')->unique()->constrained('delivery_stops')->cascadeOnDelete();
            $table->string('proof_type', 40); // QR, photo, signature, OTP, school_confirmation
            $table->text('photo_url')->nullable();
            $table->text('signature_url')->nullable();
            $table->string('recipient_name', 150)->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->text('notes')->nullable();
            $table->timestampTz('captured_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proof_of_delivery');
        Schema::dropIfExists('delivery_stops');
        Schema::dropIfExists('delivery_batches');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('production_items');
        Schema::dropIfExists('production_batches');
    }
};
