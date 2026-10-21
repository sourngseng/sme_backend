<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealFeedback extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'meal_feedback';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_schedule_id',
        'student_id',
        'guardian_id',
        'rating',
        'taste_rating',
        'portion_rating',
        'quality_rating',
        'comment',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'taste_rating' => 'integer',
            'portion_rating' => 'integer',
            'quality_rating' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(OrderSchedule::class, 'order_schedule_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }
}
