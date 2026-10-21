<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'students';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_code',
        'school_id',
        'school_shift_id',
        'full_name',
        'date_of_birth',
        'gender',
        'grade_level',
        'class_name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'deleted_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(SchoolShift::class, 'school_shift_id');
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student', 'student_id', 'guardian_id')
            ->withPivot('is_primary', 'can_order', 'can_view_nutrition', 'created_at');
    }

    public function nutritionProfile(): HasOne
    {
        return $this->hasOne(StudentNutritionProfile::class, 'student_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'student_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(MealFeedback::class, 'student_id');
    }

    public function consumptionLogs(): HasMany
    {
        return $this->hasMany(NutritionConsumptionLog::class, 'student_id');
    }
}
