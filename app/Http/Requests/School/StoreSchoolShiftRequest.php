<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $school = $this->route('school');
        if ($school) {
            return $this->user()->can('manageShifts', $school);
        }

        return $this->user()->can('create', \App\Models\SchoolShift::class);
    }

    public function rules(): array
    {
        return [
            'school_id' => ['sometimes', 'uuid', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:80'],
            'start_time' => ['required', 'date_format:H:i:s,H:i'],
            'end_time' => ['required', 'date_format:H:i:s,H:i'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
