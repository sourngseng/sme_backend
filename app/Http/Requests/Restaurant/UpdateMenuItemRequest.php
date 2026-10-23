<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('menu_item');
        return $this->user()->can('update', $item);
    }

    public function rules(): array
    {
        $itemId = $this->route('menu_item') instanceof \App\Models\MenuItem
            ? $this->route('menu_item')->id
            : $this->route('menu_item');

        return [
            'category_id' => ['sometimes', 'nullable', 'uuid', 'exists:menu_categories,id'],
            'sku' => ['sometimes', 'nullable', 'string', 'max:80', Rule::unique('menu_items', 'sku')->ignore($itemId)],
            'name' => ['sometimes', 'string', 'max:180'],
            'description' => ['sometimes', 'nullable', 'string'],
            'meal_type' => ['sometimes', 'string', 'in:breakfast,lunch,snack,dinner'],
            'status' => ['sometimes', 'string', 'in:draft,active,inactive'],

            // Version details (creates new immutable version if altered)
            'price_minor' => ['sometimes', 'integer', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'serving_size_g' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'available_from' => ['sometimes', 'nullable', 'date'],
            'available_to' => ['sometimes', 'nullable', 'date'],
            'cutoff_minutes' => ['sometimes', 'integer', 'min:0'],
            'max_daily_quantity' => ['sometimes', 'nullable', 'integer', 'min:1'],

            // Nutrition Facts
            'nutrition' => ['sometimes', 'nullable', 'array'],
            'nutrition.calories_kcal' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.protein_g' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.carbohydrates_g' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.fat_g' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.fiber_g' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.sugar_g' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.sodium_mg' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'nutrition.nutrition_source' => ['sometimes', 'nullable', 'string', 'max:100'],

            // Allergens
            'allergens' => ['sometimes', 'nullable', 'array'],
            'allergens.*' => ['uuid', 'exists:allergens,id'],
        ];
    }
}
