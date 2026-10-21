<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryBatch extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'delivery_batches';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'restaurant_id',
        'driver_id',
        'delivery_date',
        'shift',
        'status',
        'pickup_at',
        'completed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'pickup_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function stops(): HasMany
    {
        return $this->hasMany(DeliveryStop::class, 'delivery_batch_id');
    }
}
