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
        // 1. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('provider', 50); // KHQR, ABA, ACLEDA, COD
            $table->string('method', 50);
            $table->string('status', 40)->default('pending'); // pending, authorized, paid, failed, refunded, cancelled
            $table->bigInteger('amount_minor')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->string('provider_reference', 150)->nullable()->unique();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampsTz();

            $table->index(['order_id', 'status']);
            $table->index(['provider', 'provider_reference']);
        });

        // 2. Payment Transactions
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('transaction_type', 40)->default('charge'); // charge, refund, adjustment
            $table->bigInteger('amount_minor')->default(0);
            $table->string('provider_transaction_id', 150)->nullable();
            $table->string('status', 40)->default('pending');
            $table->string('response_code', 80)->nullable();
            $table->jsonb('response_payload')->nullable();
            $table->timestampTz('created_at')->useCurrent();
        });

        // 3. Payment Webhooks (Idempotent webhook callbacks)
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider', 50);
            $table->string('event_id', 150);
            $table->string('event_type', 100);
            $table->jsonb('payload');
            $table->boolean('signature_valid')->default(false);
            $table->timestampTz('processed_at')->nullable();
            $table->string('status', 30)->default('received'); // received, processed, failed
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['provider', 'event_id']);
        });

        // 4. Idempotency Keys (API idempotency support)
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->string('key', 120)->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('request_hash', 64);
            $table->smallInteger('response_code')->nullable();
            $table->jsonb('response_body')->nullable();
            $table->timestampTz('expires_at')->index();
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payments');
    }
};
