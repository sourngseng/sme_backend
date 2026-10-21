<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'order_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'menu_version_id',
        'item_name_snapshot',
        'unit_price_minor',
        'quantity',
        'line_total_minor',
        'calories_kcal_snapshot',
        'nutrition_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_minor' => 'integer',
            'quantity' => 'integer',
            'line_total_minor' => 'integer',
            'calories_kcal_snapshot' => 'decimal:2',
            'nutrition_snapshot' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menuVersion(): BelongsTo
    {
        return $this->belongsTo(MenuVersion::class, 'menu_version_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(OrderSchedule::class, 'order_item_id');
    }
}
