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
        // 1. School Grades table (Multilingual Support)
        Schema::create('school_grades', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 50); // e.g. G1, G2, G3, G4, G5, G6, K1
            $table->string('name', 150); // Latin: e.g. "Grade 1"
            $table->string('name_km', 150)->nullable(); // Khmer: e.g. "ថ្នាក់ទី ១"
            $table->integer('level_number')->default(1); // 1, 2, 3, 4, 5, 6
            $table->string('academic_stage', 80)->default('Primary'); // Early Primary, Primary, Lower Secondary
            $table->string('academic_stage_km', 120)->nullable(); // e.g. "បឋមសិក្សា"
            $table->text('description')->nullable();
            $table->text('description_km')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'status']);
        });

        // 2. School Classes / Sections table (Multilingual Support)
        Schema::create('school_classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignUuid('school_grade_id')->nullable()->constrained('school_grades')->nullOnDelete();
            $table->string('code', 50); // e.g. 1A, 2B, 4A
            $table->string('name', 150); // Latin: e.g. "Class 4A"
            $table->string('name_km', 150)->nullable(); // Khmer: e.g. "ថ្នាក់ ៤A"
            $table->string('room_number', 100)->nullable(); // e.g. "Room 204 - Building B"
            $table->string('room_number_km', 100)->nullable(); // e.g. "បន្ទប់ ២០៤ - អគារ B"
            $table->string('teacher_name', 180)->nullable(); // Latin: "Chanthy Dara"
            $table->string('teacher_name_km', 180)->nullable(); // Khmer: "ចាន់ធី ដារ៉ា"
            $table->string('teacher_phone', 50)->nullable();
            $table->integer('capacity')->default(40);
            $table->foreignUuid('school_shift_id')->nullable()->constrained('school_shifts')->nullOnDelete();
            $table->text('description')->nullable();
            $table->text('description_km')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_classes');
        Schema::dropIfExists('school_grades');
    }
};
