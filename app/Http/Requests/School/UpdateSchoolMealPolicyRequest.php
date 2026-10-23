<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolMealPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $school = $this->route('school');
        return $this->user()->can('manageMealPolicy', $school);
    }

    public function rules(): array
    {
        return [
            'default_cutoff_minutes' => ['sometimes', 'integer', 'min:0', 'max:1440'],
            'allowed_meal_types' => ['sometimes', 'array'],
            'allowed_meal_types.*' => ['string', 'in:breakfast,lunch,snack,dinner'],
            'allow_cod' => ['sometimes', 'boolean'],
        ];
    }
}
