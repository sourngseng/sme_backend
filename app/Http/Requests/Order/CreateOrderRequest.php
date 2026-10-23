<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'uuid', 'exists:students,id'],
            'restaurant_id' => ['required', 'uuid', 'exists:restaurants,id'],
            'school_location_id' => ['required', 'uuid', 'exists:school_locations,id'],
            'notes' => ['nullable', 'string', 'max:500'],
            'promotion_code' => ['nullable', 'string', 'max:50'],
            'acknowledge_allergen_warning' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.menu_version_id' => ['required', 'uuid', 'exists:menu_versions,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.delivery_date' => ['required', 'date', 'date_format:Y-m-d'],
            'items.*.shift' => ['nullable', 'string', 'max:50'],
        ];
    }
}
