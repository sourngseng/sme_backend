<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'restaurant_id' => ['sometimes', 'uuid', 'exists:restaurants,id'],
            'category_id' => ['nullable', 'uuid', 'exists:menu_categories,id'],
            'sku' => ['nullable', 'string', 'max:80', 'unique:menu_items,sku'],
            'name' => ['required', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'meal_type' => ['required', 'string', 'in:breakfast,lunch,snack,dinner'],
            'status' => ['nullable', 'string', 'in:draft,active,inactive'],

            // Version details
            'price_minor' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'serving_size_g' => ['nullable', 'numeric', 'min:0'],
            'available_from' => ['nullable', 'date'],
            'available_to' => ['nullable', 'date', 'after_or_equal:available_from'],
            'cutoff_minutes' => ['nullable', 'integer', 'min:0'],
            'max_daily_quantity' => ['nullable', 'integer', 'min:1'],

            // Nutrition Facts
            'nutrition' => ['nullable', 'array'],
            'nutrition.calories_kcal' => ['nullable', 'numeric', 'min:0'],
            'nutrition.protein_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.carbohydrates_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.fat_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.fiber_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.sugar_g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.sodium_mg' => ['nullable', 'numeric', 'min:0'],
            'nutrition.nutrition_source' => ['nullable', 'string', 'max:100'],

            // Allergens
            'allergens' => ['nullable', 'array'],
            'allergens.*' => ['uuid', 'exists:allergens,id'],
        ];
    }
}
