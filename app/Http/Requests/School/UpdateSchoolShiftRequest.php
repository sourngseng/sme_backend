<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $shift = $this->route('shift');
        return $this->user()->can('update', $shift);
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:80'],
            'start_time' => ['sometimes', 'date_format:H:i:s,H:i'],
            'end_time' => ['sometimes', 'date_format:H:i:s,H:i'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
        ];
    }
}
