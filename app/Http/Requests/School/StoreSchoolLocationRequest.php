<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $school = $this->route('school');
        if ($school) {
            return $this->user()->can('manageLocations', $school);
        }

        return $this->user()->can('create', \App\Models\SchoolLocation::class);
    }

    public function rules(): array
    {
        return [
            'school_id' => ['sometimes', 'uuid', 'exists:schools,id'],
            'name' => ['required', 'string', 'max:120'],
            'address' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'instructions' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
