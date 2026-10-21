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
        // 1. Restaurants
        Schema::create('restaurants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->foreignUuid('owner_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone', 30)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('status', 30)->default('pending'); // pending, active, suspended
            $table->decimal('commission_default_pct', 5, 2)->default(0);
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // 2. Restaurant Staff
        Schema::create('restaurant_staff', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('job_title', 80)->nullable(); // owner, manager, kitchen_staff, dispatcher
            $table->string('status', 30)->default('active');
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['restaurant_id', 'user_id']);
        });

        // 3. Restaurant Locations
        Schema::create('restaurant_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name', 120);
            $table->text('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('instructions')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
        });

        // 4. Menu Categories
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
        });

        // 5. Menu Items
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignUuid('category_id')->nullable()->constrained('menu_categories')->nullOnDelete();
            $table->string('sku', 80)->nullable()->unique();
            $table->string('name', 180);
            $table->text('description')->nullable();
            $table->string('meal_type', 40)->nullable(); // breakfast, lunch, snack, dinner
            $table->string('status', 30)->default('draft'); // draft, active, inactive
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // 6. Menu Versions (Immutable versioning)
        Schema::create('menu_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_item_id')->constrained('menu_items')->cascadeOnDelete();
            $table->integer('version_no')->default(1);
            $table->bigInteger('price_minor')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->decimal('serving_size_g', 8, 2)->nullable();
            $table->timestampTz('available_from')->nullable();
            $table->timestampTz('available_to')->nullable();
            $table->integer('cutoff_minutes')->default(0);
            $table->integer('max_daily_quantity')->nullable();
            $table->string('status', 30)->default('draft'); // draft, active, retired
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['menu_item_id', 'status']);
            $table->index(['available_from', 'available_to']);
        });

        // 7. Nutrition Facts
        Schema::create('nutrition_facts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_version_id')->unique()->constrained('menu_versions')->cascadeOnDelete();
            $table->decimal('calories_kcal', 8, 2)->nullable();
            $table->decimal('protein_g', 8, 2)->nullable();
            $table->decimal('carbohydrates_g', 8, 2)->nullable();
            $table->decimal('fat_g', 8, 2)->nullable();
            $table->decimal('fiber_g', 8, 2)->nullable();
            $table->decimal('sugar_g', 8, 2)->nullable();
            $table->decimal('sodium_mg', 10, 2)->nullable();
            $table->string('nutrition_source', 100)->nullable();
            $table->timestampTz('verified_at')->nullable();
            $table->timestampsTz();
        });

        // 8. Allergens
        Schema::create('allergens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 120)->unique();
            $table->text('description')->nullable();
            $table->timestampsTz();
        });

        // 9. Menu Allergens pivot
        Schema::create('menu_allergens', function (Blueprint $table) {
            $table->foreignUuid('menu_version_id')->constrained('menu_versions')->cascadeOnDelete();
            $table->foreignUuid('allergen_id')->constrained('allergens')->cascadeOnDelete();
            $table->string('severity', 30)->nullable();
            $table->text('notes')->nullable();

            $table->primary(['menu_version_id', 'allergen_id']);
        });

        // 10. Media Assets (Polymorphic)
        Schema::create('media_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('owner_type', 80);
            $table->uuid('owner_id');
            $table->text('file_url');
            $table->string('mime_type', 120)->nullable();
            $table->bigInteger('file_size_bytes')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['owner_type', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
        Schema::dropIfExists('menu_allergens');
        Schema::dropIfExists('allergens');
        Schema::dropIfExists('nutrition_facts');
        Schema::dropIfExists('menu_versions');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('restaurant_locations');
        Schema::dropIfExists('restaurant_staff');
        Schema::dropIfExists('restaurants');
    }
};
