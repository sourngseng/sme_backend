<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use Auditable, HasFactory, HasUuids;

    protected $table = 'drivers';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'driver_code',
        'phone',
        'vehicle_type',
        'vehicle_plate',
        'license_no',
        'status',
        'self_delivery',
    ];

    protected function casts(): array
    {
        return [
            'self_delivery' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function deliveryBatches(): HasMany
    {
        return $this->hasMany(DeliveryBatch::class, 'driver_id');
    }
}
