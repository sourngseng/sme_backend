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
        // 1. Schools
        Schema::create('schools', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 200);
            $table->string('level', 30); // primary, secondary, high_school, university
            $table->string('status', 30)->default('active');
            $table->string('phone', 30)->nullable();
            $table->string('email', 190)->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        // 2. School Shifts
        Schema::create('school_shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 80); // e.g. Morning, Lunch, Afternoon
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
        });

        // 3. School Locations (e.g. gate, canteen counter, building)
        Schema::create('school_locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 120);
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('instructions')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
        });

        // 4. School Meal Policies
        Schema::create('school_meal_policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->unique()->constrained('schools')->cascadeOnDelete();
            $table->integer('default_cutoff_minutes')->default(120);
            $table->jsonb('allowed_meal_types')->nullable();
            $table->boolean('allow_cod')->default(false);
            $table->timestampsTz();
        });

        // 5. Students
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('student_code', 80)->unique();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('school_shift_id')->nullable()->constrained('school_shifts')->nullOnDelete();
            $table->string('full_name', 180);
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 30)->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->string('class_name', 80)->nullable();
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'full_name']);
        });

        // 6. Guardians
        Schema::create('guardians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('relationship', 50)->nullable();
            $table->timestampsTz();
        });

        // 7. Guardian Student pivot
        Schema::create('guardian_student', function (Blueprint $table) {
            $table->foreignUuid('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->boolean('can_order')->default(true);
            $table->boolean('can_view_nutrition')->default(true);
            $table->timestampTz('created_at')->useCurrent();

            $table->primary(['guardian_id', 'student_id']);
        });

        // 8. Student Nutrition Profiles
        Schema::create('student_nutrition_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->jsonb('dietary_preferences')->nullable();
            $table->jsonb('allergen_ids')->nullable();
            $table->text('notes')->nullable();
            $table->timestampsTz();
        });

        // 9. Addresses
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('label', 80)->nullable(); // Home, Office, Dorm
            $table->string('recipient_name', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('address_line1');
            $table->text('address_line2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('student_nutrition_profiles');
        Schema::dropIfExists('guardian_student');
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('students');
        Schema::dropIfExists('school_meal_policies');
        Schema::dropIfExists('school_locations');
        Schema::dropIfExists('school_shifts');
        Schema::dropIfExists('schools');
    }
};
