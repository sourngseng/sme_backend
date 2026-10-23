<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\NutritionFact;
use Illuminate\Support\Facades\DB;

class MenuVersioningService
{
    /**
     * Create the initial version (v1) for a newly registered menu item.
     *
     * @param array<string, mixed> $versionData
     * @param array<string, mixed>|null $nutritionData
     * @param array<int, string|array<string, mixed>> $allergens
     */
    public function createInitialVersion(
        MenuItem $item,
        array $versionData,
        ?array $nutritionData = null,
        array $allergens = []
    ): MenuVersion {
        return DB::transaction(function () use ($item, $versionData, $nutritionData, $allergens) {
            $version = MenuVersion::create([
                'menu_item_id' => $item->id,
                'version_no' => 1,
                'price_minor' => $versionData['price_minor'] ?? 0,
                'currency' => $versionData['currency'] ?? 'USD',
                'serving_size_g' => $versionData['serving_size_g'] ?? null,
                'available_from' => $versionData['available_from'] ?? null,
                'available_to' => $versionData['available_to'] ?? null,
                'cutoff_minutes' => $versionData['cutoff_minutes'] ?? 0,
                'max_daily_quantity' => $versionData['max_daily_quantity'] ?? null,
                'status' => $versionData['status'] ?? 'active',
                'created_at' => now(),
            ]);

            if ($nutritionData) {
                NutritionFact::create(array_merge($nutritionData, [
                    'menu_version_id' => $version->id,
                    'verified_at' => now(),
                ]));
            }

            $this->syncAllergens($version, $allergens);

            return $version;
        });
    }

    /**
     * Create an immutable new version (vN+1), retiring the previous active version.
     *
     * @param array<string, mixed> $newVersionData
     * @param array<string, mixed>|null $newNutritionData
     * @param array<int, string|array<string, mixed>>|null $newAllergens
     */
    public function createNewVersion(
        MenuItem $item,
        array $newVersionData,
        ?array $newNutritionData = null,
        ?array $newAllergens = null
    ): MenuVersion {
        return DB::transaction(function () use ($item, $newVersionData, $newNutritionData, $newAllergens) {
            // Find currently active or latest version
            $currentVersion = $item->latestVersion()->with(['nutritionFact', 'allergens'])->first();

            $nextVersionNo = $currentVersion ? ($currentVersion->version_no + 1) : 1;

            // Mark previous active version as retired
            if ($currentVersion && $currentVersion->status === 'active') {
                $currentVersion->update(['status' => 'retired']);
            }

            // Create new immutable version
            $mergedVersionData = array_merge([
                'price_minor' => $currentVersion?->price_minor ?? 0,
                'currency' => $currentVersion?->currency ?? 'USD',
                'serving_size_g' => $currentVersion?->serving_size_g,
                'available_from' => $currentVersion?->available_from,
                'available_to' => $currentVersion?->available_to,
                'cutoff_minutes' => $currentVersion?->cutoff_minutes ?? 0,
                'max_daily_quantity' => $currentVersion?->max_daily_quantity,
                'status' => 'active',
            ], $newVersionData);

            $newVersion = MenuVersion::create([
                'menu_item_id' => $item->id,
                'version_no' => $nextVersionNo,
                'price_minor' => $mergedVersionData['price_minor'],
                'currency' => $mergedVersionData['currency'],
                'serving_size_g' => $mergedVersionData['serving_size_g'],
                'available_from' => $mergedVersionData['available_from'],
                'available_to' => $mergedVersionData['available_to'],
                'cutoff_minutes' => $mergedVersionData['cutoff_minutes'],
                'max_daily_quantity' => $mergedVersionData['max_daily_quantity'],
                'status' => $mergedVersionData['status'],
                'created_at' => now(),
            ]);

            // Handle nutrition facts: clone existing if not updated, or set new
            $nutritionPayload = $newNutritionData;
            if ($nutritionPayload === null && $currentVersion && $currentVersion->nutritionFact) {
                $nutritionPayload = collect($currentVersion->nutritionFact->toArray())
                    ->except(['id', 'menu_version_id', 'created_at', 'updated_at'])
                    ->all();
            }

            if ($nutritionPayload) {
                NutritionFact::create(array_merge($nutritionPayload, [
                    'menu_version_id' => $newVersion->id,
                    'verified_at' => now(),
                ]));
            }

            // Handle allergens: clone existing if not updated, or set new
            if ($newAllergens !== null) {
                $this->syncAllergens($newVersion, $newAllergens);
            } elseif ($currentVersion) {
                $existingAllergens = $currentVersion->allergens->mapWithKeys(function ($allergen) {
                    return [$allergen->id => [
                        'severity' => $allergen->pivot->severity,
                        'notes' => $allergen->pivot->notes,
                    ]];
                })->all();
                $newVersion->allergens()->sync($existingAllergens);
            }

            return $newVersion;
        });
    }

    /**
     * Check if requested update contains version-altering fields.
     *
     * @param array<string, mixed> $input
     */
    public function requiresNewVersion(array $input): bool
    {
        $versionFields = [
            'price_minor',
            'currency',
            'serving_size_g',
            'available_from',
            'available_to',
            'cutoff_minutes',
            'max_daily_quantity',
            'nutrition',
            'allergens',
        ];

        foreach ($versionFields as $field) {
            if (array_key_exists($field, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sync allergens pivot with support for array of UUIDs or structured pivot attributes.
     *
     * @param array<int, string|array<string, mixed>> $allergens
     */
    protected function syncAllergens(MenuVersion $version, array $allergens): void
    {
        $pivotData = [];

        foreach ($allergens as $item) {
            if (is_string($item)) {
                $pivotData[$item] = ['severity' => 'moderate', 'notes' => null];
            } elseif (is_array($item) && isset($item['allergen_id'])) {
                $pivotData[$item['allergen_id']] = [
                    'severity' => $item['severity'] ?? 'moderate',
                    'notes' => $item['notes'] ?? null,
                ];
            }
        }

        $version->allergens()->sync($pivotData);
    }
}
