<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('updateStatus', \App\Models\Restaurant::class);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:active,suspended,pending'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
