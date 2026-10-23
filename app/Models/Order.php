<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use Auditable, HasFactory, HasUuids;

    protected $table = 'orders';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_number',
        'guardian_id',
        'student_id',
        'school_id',
        'restaurant_id',
        'status',
        'subtotal_minor',
        'delivery_fee_minor',
        'discount_minor',
        'commission_minor',
        'total_minor',
        'currency',
        'notes',
        'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal_minor' => 'integer',
            'delivery_fee_minor' => 'integer',
            'discount_minor' => 'integer',
            'commission_minor' => 'integer',
            'total_minor' => 'integer',
            'placed_at' => 'datetime',
        ];
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(OrderSchedule::class, 'order_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class, 'order_id')->latestOfMany();
    }

    public function settlementItems(): HasMany
    {
        return $this->hasMany(SettlementItem::class, 'order_id');
    }
}
