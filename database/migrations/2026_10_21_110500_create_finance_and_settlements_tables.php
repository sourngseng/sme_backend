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
        // 1. Commission Rules
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignUuid('menu_version_id')->nullable()->constrained('menu_versions')->nullOnDelete();
            $table->decimal('commission_pct', 5, 2)->default(0);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestampTz('created_at')->useCurrent();
        });

        // 2. Merchant Settlements
        Schema::create('settlements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->bigInteger('gross_sales_minor')->default(0);
            $table->bigInteger('commission_minor')->default(0);
            $table->bigInteger('refund_minor')->default(0);
            $table->bigInteger('adjustment_minor')->default(0);
            $table->bigInteger('net_payable_minor')->default(0);
            $table->string('status', 30)->default('draft'); // draft, approved, paid, disputed
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['restaurant_id', 'period_start', 'period_end']);
        });

        // 3. Settlement Items
        Schema::create('settlement_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('settlement_id')->constrained('settlements')->cascadeOnDelete();
            $table->foreignUuid('order_id')->constrained('orders')->cascadeOnDelete();
            $table->bigInteger('gross_minor')->default(0);
            $table->bigInteger('commission_minor')->default(0);
            $table->bigInteger('net_minor')->default(0);
        });

        // 4. Platform Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('category', 80);
            $table->text('description');
            $table->bigInteger('amount_minor')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->date('expense_date');
            $table->string('status', 30)->default('recorded'); // recorded, approved, rejected
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestampTz('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('settlement_items');
        Schema::dropIfExists('settlements');
        Schema::dropIfExists('commission_rules');
    }
};
