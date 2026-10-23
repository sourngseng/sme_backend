<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_code' => $this->student_code,
            'school_id' => $this->school_id,
            'school_shift_id' => $this->school_shift_id,
            'full_name' => $this->full_name,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'grade_level' => $this->grade_level,
            'class_name' => $this->class_name,
            'status' => $this->status,
            'school' => new SchoolResource($this->whenLoaded('school')),
            'shift' => new SchoolShiftResource($this->whenLoaded('shift')),
            'nutrition_profile' => new StudentNutritionProfileResource($this->whenLoaded('nutritionProfile')),
            'guardian_permissions' => $this->when(isset($this->pivot), function () {
                return [
                    'is_primary' => (bool) ($this->pivot->is_primary ?? false),
                    'can_order' => (bool) ($this->pivot->can_order ?? true),
                    'can_view_nutrition' => (bool) ($this->pivot->can_view_nutrition ?? true),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
