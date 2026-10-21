<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionFact extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'nutrition_facts';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'menu_version_id',
        'calories_kcal',
        'protein_g',
        'carbohydrates_g',
        'fat_g',
        'fiber_g',
        'sugar_g',
        'sodium_mg',
        'nutrition_source',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'calories_kcal' => 'decimal:2',
            'protein_g' => 'decimal:2',
            'carbohydrates_g' => 'decimal:2',
            'fat_g' => 'decimal:2',
            'fiber_g' => 'decimal:2',
            'sugar_g' => 'decimal:2',
            'sodium_mg' => 'decimal:2',
            'verified_at' => 'datetime',
        ];
    }

    public function menuVersion(): BelongsTo
    {
        return $this->belongsTo(MenuVersion::class, 'menu_version_id');
    }
}
