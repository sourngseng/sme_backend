<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Order
 */
class OrderResource extends JsonResource
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
            'order_number' => $this->order_number,
            'status' => $this->status,
            'subtotal_minor' => $this->subtotal_minor,
            'subtotal_formatted' => number_format($this->subtotal_minor / 100, 2),
            'delivery_fee_minor' => $this->delivery_fee_minor,
            'delivery_fee_formatted' => number_format($this->delivery_fee_minor / 100, 2),
            'discount_minor' => $this->discount_minor,
            'discount_formatted' => number_format($this->discount_minor / 100, 2),
            'commission_minor' => $this->commission_minor,
            'commission_formatted' => number_format($this->commission_minor / 100, 2),
            'total_minor' => $this->total_minor,
            'total_formatted' => number_format($this->total_minor / 100, 2),
            'currency' => $this->currency,
            'notes' => $this->notes,
            'placed_at' => $this->placed_at?->toIso8601String(),
            'guardian' => $this->relationLoaded('guardian') && $this->guardian ? [
                'id' => $this->guardian->id,
                'name' => $this->guardian->user?->name,
                'phone' => $this->guardian->user?->phone,
            ] : null,
            'student' => $this->relationLoaded('student') && $this->student ? [
                'id' => $this->student->id,
                'student_code' => $this->student->student_code,
                'full_name' => $this->student->full_name,
                'grade_level' => $this->student->grade_level,
                'class_name' => $this->student->class_name,
            ] : null,
            'school' => $this->relationLoaded('school') && $this->school ? [
                'id' => $this->school->id,
                'code' => $this->school->code,
                'name' => $this->school->name,
            ] : null,
            'restaurant' => $this->relationLoaded('restaurant') && $this->restaurant ? [
                'id' => $this->restaurant->id,
                'code' => $this->restaurant->code,
                'name' => $this->restaurant->name,
            ] : null,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'schedules' => OrderScheduleResource::collection($this->whenLoaded('schedules')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
