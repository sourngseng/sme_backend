<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class CreateDeliveryBatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['required', 'uuid', 'exists:restaurants,id'],
            'driver_id' => ['nullable', 'uuid', 'exists:drivers,id'],
            'delivery_date' => ['required', 'date', 'date_format:Y-m-d'],
            'shift' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:500'],
            'schedule_ids' => ['required', 'array', 'min:1'],
            'schedule_ids.*' => ['required', 'uuid', 'exists:order_schedules,id'],
        ];
    }
}
