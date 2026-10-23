<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class BrowseCatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_id' => ['sometimes', 'uuid', 'exists:schools,id'],
            'delivery_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'shift' => ['sometimes', 'string', 'max:80'],
            'student_id' => ['sometimes', 'uuid', 'exists:students,id'],
            'meal_type' => ['sometimes', 'string', 'in:breakfast,lunch,snack,dinner'],
            'search' => ['sometimes', 'string', 'max:100'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
