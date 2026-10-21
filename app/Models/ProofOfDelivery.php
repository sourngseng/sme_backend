<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProofOfDelivery extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'proof_of_delivery';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'delivery_stop_id',
        'proof_type',
        'photo_url',
        'signature_url',
        'recipient_name',
        'otp_verified',
        'notes',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'otp_verified' => 'boolean',
            'captured_at' => 'datetime',
        ];
    }

    public function stop(): BelongsTo
    {
        return $this->belongsTo(DeliveryStop::class, 'delivery_stop_id');
    }
}
