<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Student::class);
    }

    public function rules(): array
    {
        return [
            'student_code' => ['required', 'string', 'max:80', 'unique:students,student_code'],
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'school_shift_id' => ['nullable', 'uuid', 'exists:school_shifts,id'],
            'full_name' => ['required', 'string', 'max:180'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'class_name' => ['nullable', 'string', 'max:80'],
            'status' => ['nullable', 'string', 'in:active,inactive,graduated,transferred'],
        ];
    }
}
