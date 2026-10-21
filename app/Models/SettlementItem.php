<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettlementItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'settlement_items';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'settlement_id',
        'order_id',
        'gross_minor',
        'commission_minor',
        'net_minor',
    ];

    protected function casts(): array
    {
        return [
            'gross_minor' => 'integer',
            'commission_minor' => 'integer',
            'net_minor' => 'integer',
        ];
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
