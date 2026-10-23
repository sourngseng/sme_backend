<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class SearchStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', \App\Models\Student::class);
    }

    public function rules(): array
    {
        return [
            'school_id' => ['sometimes', 'uuid', 'exists:schools,id'],
            'full_name' => ['sometimes', 'string', 'max:180'],
            'grade_level' => ['sometimes', 'string', 'max:50'],
            'class_name' => ['sometimes', 'string', 'max:80'],
            'status' => ['sometimes', 'string', 'in:active,inactive,graduated,transferred'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
