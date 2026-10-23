<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Restaurant extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'restaurants';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'owner_user_id',
        'phone',
        'email',
        'status',
        'commission_default_pct',
        'address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'commission_default_pct' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'deleted_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(RestaurantStaff::class, 'restaurant_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(RestaurantLocation::class, 'restaurant_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(MenuCategory::class, 'restaurant_id');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'restaurant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'restaurant_id');
    }

    public function productionBatches(): HasMany
    {
        return $this->hasMany(ProductionBatch::class, 'restaurant_id');
    }

    public function deliveryBatches(): HasMany
    {
        return $this->hasMany(DeliveryBatch::class, 'restaurant_id');
    }

    public function commissionRules(): HasMany
    {
        return $this->hasMany(CommissionRule::class, 'restaurant_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class, 'restaurant_id');
    }
}
