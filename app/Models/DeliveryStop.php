<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DeliveryStop extends Model
{
    use Auditable, HasFactory, HasUuids;

    protected $table = 'delivery_stops';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'delivery_batch_id',
        'order_schedule_id',
        'school_id',
        'school_location_id',
        'sequence_no',
        'status',
        'arrived_at',
        'completed_at',
        'exception_code',
        'exception_note',
    ];

    protected function casts(): array
    {
        return [
            'sequence_no' => 'integer',
            'arrived_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(DeliveryBatch::class, 'delivery_batch_id');
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(OrderSchedule::class, 'order_schedule_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(SchoolLocation::class, 'school_location_id');
    }

    public function proof(): HasOne
    {
        return $this->hasOne(ProofOfDelivery::class, 'delivery_stop_id');
    }
}
