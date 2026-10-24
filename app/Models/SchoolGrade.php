<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolGrade extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'school_grades';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'name_km',
        'level_number',
        'academic_stage',
        'academic_stage_km',
        'description',
        'description_km',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'school_grade_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'grade_level', 'name');
    }
}
