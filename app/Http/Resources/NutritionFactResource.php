<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NutritionFactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'calories_kcal' => $this->calories_kcal ? (float) $this->calories_kcal : null,
            'protein_g' => $this->protein_g ? (float) $this->protein_g : null,
            'carbohydrates_g' => $this->carbohydrates_g ? (float) $this->carbohydrates_g : null,
            'fat_g' => $this->fat_g ? (float) $this->fat_g : null,
            'fiber_g' => $this->fiber_g ? (float) $this->fiber_g : null,
            'sugar_g' => $this->sugar_g ? (float) $this->sugar_g : null,
            'sodium_mg' => $this->sodium_mg ? (float) $this->sodium_mg : null,
            'nutrition_source' => $this->nutrition_source,
            'verified_at' => $this->verified_at?->toIso8601String(),
        ];
    }
}
