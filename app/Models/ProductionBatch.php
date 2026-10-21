<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionBatch extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'production_batches';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'restaurant_id',
        'production_date',
        'shift',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'production_date' => 'date',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductionItem::class, 'production_batch_id');
    }
}
