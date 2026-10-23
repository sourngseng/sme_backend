<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');
        return $this->user()->can('update', $student);
    }

    public function rules(): array
    {
        $studentId = $this->route('student') instanceof \App\Models\Student
            ? $this->route('student')->id
            : $this->route('student');

        return [
            'student_code' => ['sometimes', 'string', 'max:80', Rule::unique('students', 'student_code')->ignore($studentId)],
            'school_id' => ['sometimes', 'uuid', 'exists:schools,id'],
            'school_shift_id' => ['sometimes', 'nullable', 'uuid', 'exists:school_shifts,id'],
            'full_name' => ['sometimes', 'string', 'max:180'],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today'],
            'gender' => ['sometimes', 'nullable', 'string', 'in:male,female,other'],
            'grade_level' => ['sometimes', 'nullable', 'string', 'max:50'],
            'class_name' => ['sometimes', 'nullable', 'string', 'max:80'],
            'status' => ['sometimes', 'string', 'in:active,inactive,graduated,transferred'],
        ];
    }
}
