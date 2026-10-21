<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolMealPolicy extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'school_meal_policies';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'school_id',
        'default_cutoff_minutes',
        'allowed_meal_types',
        'allow_cod',
    ];

    protected function casts(): array
    {
        return [
            'default_cutoff_minutes' => 'integer',
            'allowed_meal_types' => 'array',
            'allow_cod' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
