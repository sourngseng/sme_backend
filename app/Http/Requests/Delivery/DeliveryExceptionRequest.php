<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryExceptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'exception_code' => ['required', 'string', 'in:student_absent,school_closed,refused,damaged,wrong_location,other'],
            'exception_note' => ['required', 'string', 'max:500'],
            'action' => ['nullable', 'string', 'in:failed,returned'],
        ];
    }
}
