<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaAsset extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'media_assets';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'owner_type',
        'owner_id',
        'file_url',
        'mime_type',
        'file_size_bytes',
        'alt_text',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'file_size_bytes' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
