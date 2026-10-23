<?php

namespace App\Http\Requests\Guardian;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNutritionProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');
        return $this->user()->can('updateNutrition', $student);
    }

    public function rules(): array
    {
        return [
            'dietary_preferences' => ['nullable', 'array'],
            'dietary_preferences.*' => ['string', 'max:100'],
            'allergen_ids' => ['nullable', 'array'],
            'allergen_ids.*' => ['uuid', 'exists:allergens,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
