<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use Auditable, HasFactory, HasUuids;

    protected $table = 'settlements';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'restaurant_id',
        'period_start',
        'period_end',
        'gross_sales_minor',
        'commission_minor',
        'refund_minor',
        'adjustment_minor',
        'net_payable_minor',
        'status',
        'paid_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'gross_sales_minor' => 'integer',
            'commission_minor' => 'integer',
            'refund_minor' => 'integer',
            'adjustment_minor' => 'integer',
            'net_payable_minor' => 'integer',
            'paid_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SettlementItem::class, 'settlement_id');
    }
}
