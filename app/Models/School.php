<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'schools';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'level',
        'status',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'deleted_at' => 'datetime',
        ];
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(SchoolShift::class, 'school_id');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(SchoolLocation::class, 'school_id');
    }

    public function mealPolicy(): HasOne
    {
        return $this->hasOne(SchoolMealPolicy::class, 'school_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'school_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'school_id');
    }
}
