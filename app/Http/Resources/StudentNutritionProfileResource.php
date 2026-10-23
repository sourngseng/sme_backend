<?php

namespace App\Http\Resources;

use App\Models\Allergen;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentNutritionProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $allergens = ! empty($this->allergen_ids)
            ? Allergen::whereIn('id', $this->allergen_ids)->get(['id', 'code', 'name', 'description'])
            : collect([]);

        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'dietary_preferences' => $this->dietary_preferences ?? [],
            'allergen_ids' => $this->allergen_ids ?? [],
            'allergens' => $allergens,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
