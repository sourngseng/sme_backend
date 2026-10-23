<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        $school = $this->route('school');
        return $this->user()->can('update', $school);
    }

    public function rules(): array
    {
        $schoolId = $this->route('school') instanceof \App\Models\School
            ? $this->route('school')->id
            : $this->route('school');

        return [
            'code' => ['sometimes', 'string', 'max:50', Rule::unique('schools', 'code')->ignore($schoolId)],
            'name' => ['sometimes', 'string', 'max:200'],
            'level' => ['sometimes', 'string', 'in:primary,secondary,high_school,university'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:190'],
            'address' => ['sometimes', 'nullable', 'string'],
            'latitude' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
