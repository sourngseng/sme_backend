<?php

namespace App\Http\Requests\Guardian;

use Illuminate\Foundation\Http\FormRequest;

class RegisterGuardianStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // Option A: Link by existing student identifier
            'student_id' => ['nullable', 'uuid', 'exists:students,id'],
            'student_code' => ['nullable', 'string', 'max:80'],

            // Option B: Register new student directly
            'school_id' => ['required_without_all:student_id,student_code', 'nullable', 'uuid', 'exists:schools,id'],
            'school_shift_id' => ['nullable', 'uuid', 'exists:school_shifts,id'],
            'full_name' => ['required_without_all:student_id,student_code', 'nullable', 'string', 'max:180'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'class_name' => ['nullable', 'string', 'max:80'],

            // Guardian relationship controls
            'relationship' => ['nullable', 'string', 'max:50'],
            'is_primary' => ['nullable', 'boolean'],
            'can_order' => ['nullable', 'boolean'],
            'can_view_nutrition' => ['nullable', 'boolean'],

            // Initial optional dietary preferences
            'dietary_preferences' => ['nullable', 'array'],
            'dietary_preferences.*' => ['string', 'max:100'],
            'allergen_ids' => ['nullable', 'array'],
            'allergen_ids.*' => ['uuid', 'exists:allergens,id'],
            'nutrition_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
