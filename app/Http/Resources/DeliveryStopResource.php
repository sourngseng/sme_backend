<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\DeliveryStop
 */
class DeliveryStopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $student = $this->relationLoaded('schedule') && $this->schedule && $this->schedule->order
            ? $this->schedule->order->student
            : null;

        $orderItem = $this->relationLoaded('schedule') && $this->schedule
            ? $this->schedule->orderItem
            : null;

        return [
            'id' => $this->id,
            'delivery_batch_id' => $this->delivery_batch_id,
            'order_schedule_id' => $this->order_schedule_id,
            'school_id' => $this->school_id,
            'school_location_id' => $this->school_location_id,
            'sequence_no' => $this->sequence_no,
            'status' => $this->status,
            'arrived_at' => $this->arrived_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'exception_code' => $this->exception_code,
            'exception_note' => $this->exception_note,
            'school' => $this->relationLoaded('school') && $this->school ? [
                'id' => $this->school->id,
                'name' => $this->school->name,
                'code' => $this->school->code,
                'address' => $this->school->address,
            ] : null,
            'location' => $this->relationLoaded('location') && $this->location ? [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'building' => $this->location->building,
                'floor' => $this->location->floor,
                'room' => $this->location->room,
            ] : null,
            'package' => [
                'order_id' => $this->schedule?->order_id,
                'order_number' => $this->schedule?->order?->order_number,
                'student_name' => $student?->full_name,
                'student_code' => $student?->student_code,
                'grade_level' => $student?->grade_level,
                'class_name' => $student?->class_name,
                'meal_name' => $orderItem?->item_name_snapshot,
            ],
            'proof' => new ProofOfDeliveryResource($this->whenLoaded('proof')),
        ];
    }
}
