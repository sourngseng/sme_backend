<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'promotions';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'campaign_id',
        'code',
        'type',
        'value',
        'max_discount_minor',
        'usage_limit',
        'used_count',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'max_discount_minor' => 'integer',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }
}
