<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'production_items';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'production_batch_id',
        'menu_version_id',
        'total_quantity',
        'prepared_quantity',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_quantity' => 'integer',
            'prepared_quantity' => 'integer',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductionBatch::class, 'production_batch_id');
    }

    public function menuVersion(): BelongsTo
    {
        return $this->belongsTo(MenuVersion::class, 'menu_version_id');
    }
}
