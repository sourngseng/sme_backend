<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuVersionResource extends JsonResource
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
            'menu_item_id' => $this->menu_item_id,
            'version_no' => $this->version_no,
            'price_minor' => $this->price_minor,
            'price_formatted' => number_format($this->price_minor / 100, 2),
            'currency' => $this->currency,
            'serving_size_g' => $this->serving_size_g ? (float) $this->serving_size_g : null,
            'available_from' => $this->available_from?->toIso8601String(),
            'available_to' => $this->available_to?->toIso8601String(),
            'cutoff_minutes' => $this->cutoff_minutes,
            'max_daily_quantity' => $this->max_daily_quantity,
            'status' => $this->status,
            'nutrition_facts' => new NutritionFactResource($this->whenLoaded('nutritionFact')),
            'allergens' => AllergenResource::collection($this->whenLoaded('allergens')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
