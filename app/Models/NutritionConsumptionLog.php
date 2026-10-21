<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NutritionConsumptionLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'nutrition_consumption_logs';
    public $timestamps = false;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'order_schedule_id',
        'student_id',
        'consumption_status',
        'estimated_consumed_pct',
        'calories_consumed_kcal',
        'protein_consumed_g',
        'notes',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_consumed_pct' => 'decimal:2',
            'calories_consumed_kcal' => 'decimal:2',
            'protein_consumed_g' => 'decimal:2',
            'recorded_at' => 'datetime',
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
}
