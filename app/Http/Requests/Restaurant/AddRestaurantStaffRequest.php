<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class AddRestaurantStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['sometimes', 'uuid', 'exists:restaurants,id'],
            'user_id' => ['required_without:email', 'nullable', 'uuid', 'exists:users,id'],
            'email' => ['required_without:user_id', 'nullable', 'email', 'exists:users,email'],
            'job_title' => ['required', 'string', 'in:owner,manager,kitchen_staff,dispatcher'],
        ];
    }
}
