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
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_or_email', 190)->index();
            $table->string('otp_hash', 255);
            $table->timestampTz('expires_at')->index();
            $table->smallInteger('attempts')->default(0);
            $table->timestampTz('verified_at')->nullable();
            $table->timestampsTz();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100)->index();
            $table->string('auditable_type', 80);
            $table->uuid('auditable_id');
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestampTz('created_at')->useCurrent()->index();

            $table->index(['auditable_type', 'auditable_id']);
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('token');
            $table->string('platform', 30); // ios, android, web
            $table->timestampsTz();

            $table->index(['user_id', 'platform']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('otp_requests');
    }
};
