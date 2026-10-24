<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use Auditable, HasFactory, HasUuids, SoftDeletes;

    protected $table = 'school_classes';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'school_id',
        'school_grade_id',
        'code',
        'name',
        'name_km',
        'room_number',
        'room_number_km',
        'teacher_name',
        'teacher_name_km',
        'teacher_phone',
        'capacity',
        'school_shift_id',
        'description',
        'description_km',
        'status',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(SchoolGrade::class, 'school_grade_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(SchoolShift::class, 'school_shift_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_name', 'code');
    }
}
