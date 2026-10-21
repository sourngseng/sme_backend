<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MenuVersion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'menu_versions';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'menu_item_id',
        'version_no',
        'price_minor',
        'currency',
        'serving_size_g',
        'available_from',
        'available_to',
        'cutoff_minutes',
        'max_daily_quantity',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'version_no' => 'integer',
            'price_minor' => 'integer',
            'serving_size_g' => 'decimal:2',
            'available_from' => 'datetime',
            'available_to' => 'datetime',
            'cutoff_minutes' => 'integer',
            'max_daily_quantity' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'menu_item_id');
    }

    public function nutritionFact(): HasOne
    {
        return $this->hasOne(NutritionFact::class, 'menu_version_id');
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class, 'menu_allergens', 'menu_version_id', 'allergen_id')
            ->withPivot('severity', 'notes');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'menu_version_id');
    }

    public function productionItems(): HasMany
    {
        return $this->hasMany(ProductionItem::class, 'menu_version_id');
    }
}
