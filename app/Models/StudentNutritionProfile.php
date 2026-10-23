<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNutritionProfile extends Model
{
    use Auditable, HasFactory, HasUuids;

    protected $table = 'student_nutrition_profiles';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'dietary_preferences',
        'allergen_ids',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'dietary_preferences' => 'array',
            'allergen_ids' => 'array',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
