<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionRule extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'commission_rules';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'restaurant_id',
        'menu_version_id',
        'commission_pct',
        'effective_from',
        'effective_to',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'commission_pct' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function menuVersion(): BelongsTo
    {
        return $this->belongsTo(MenuVersion::class, 'menu_version_id');
    }
}
