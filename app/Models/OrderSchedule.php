<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'order_schedules';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'delivery_date',
        'shift',
        'school_location_id',
        'status',
        'cutoff_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'cutoff_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(SchoolLocation::class, 'school_location_id');
    }

    public function deliveryStop(): HasOne
    {
        return $this->hasOne(DeliveryStop::class, 'order_schedule_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(MealFeedback::class, 'order_schedule_id');
    }

    public function consumptionLogs(): HasMany
    {
        return $this->hasMany(NutritionConsumptionLog::class, 'order_schedule_id');
    }
}
