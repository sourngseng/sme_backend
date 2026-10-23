<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\MenuItem
 */
class CatalogMealResource extends JsonResource
{
    /**
     * Optional student allergen IDs for conflict detection.
     *
     * @var array<int, string>
     */
    protected array $studentAllergenIds = [];

    /**
     * Create a new resource instance with optional student allergen context.
     *
     * @param mixed $resource
     * @param array<int, string> $studentAllergenIds
     */
    public function __construct($resource, array $studentAllergenIds = [])
    {
        parent::__construct($resource);
        $this->studentAllergenIds = $studentAllergenIds;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $version = $this->latestVersion ?? $this->versions->first();
        $versionAllergens = $version ? $version->allergens : collect([]);

        // Conflict evaluation
        $conflicts = [];
        if (! empty($this->studentAllergenIds) && $versionAllergens->isNotEmpty()) {
            foreach ($versionAllergens as $allergen) {
                if (in_array($allergen->id, $this->studentAllergenIds, true)) {
                    $conflicts[] = [
                        'id' => $allergen->id,
                        'code' => $allergen->code,
                        'name' => $allergen->name,
                        'severity' => $allergen->pivot->severity ?? 'moderate',
                    ];
                }
            }
        }

        $hasConflict = ! empty($conflicts);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'sku' => $this->sku,
            'meal_type' => $this->meal_type,
            'restaurant' => [
                'id' => $this->restaurant?->id,
                'name' => $this->restaurant?->name,
                'code' => $this->restaurant?->code,
            ],
            'active_version' => $version ? [
                'id' => $version->id,
                'version_no' => $version->version_no,
                'price_minor' => $version->price_minor,
                'price_formatted' => number_format($version->price_minor / 100, 2),
                'currency' => $version->currency,
                'serving_size_g' => $version->serving_size_g ? (float) $version->serving_size_g : null,
                'cutoff_minutes' => $version->cutoff_minutes,
                'nutrition_facts' => new NutritionFactResource($version->nutritionFact),
                'allergens' => AllergenResource::collection($versionAllergens),
            ] : null,
            'allergen_conflict' => $hasConflict,
            'conflicting_allergens' => $conflicts,
        ];
    }
}
