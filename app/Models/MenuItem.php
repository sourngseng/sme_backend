<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'menu_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'sku',
        'name',
        'description',
        'meal_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(MenuVersion::class, 'menu_item_id');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(MenuVersion::class, 'menu_item_id')->latestOfMany('version_no');
    }

    public function media(): MorphMany
    {
        return $this->morphMany(MediaAsset::class, 'owner');
    }
}
