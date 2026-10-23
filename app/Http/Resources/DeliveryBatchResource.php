<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\DeliveryBatch
 */
class DeliveryBatchResource extends JsonResource
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
            'restaurant_id' => $this->restaurant_id,
            'driver_id' => $this->driver_id,
            'delivery_date' => $this->delivery_date instanceof Carbon ? $this->delivery_date->toDateString() : (string) $this->delivery_date,
            'shift' => $this->shift,
            'status' => $this->status,
            'pickup_at' => $this->pickup_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'notes' => $this->notes,
            'restaurant' => $this->relationLoaded('restaurant') && $this->restaurant ? [
                'id' => $this->restaurant->id,
                'name' => $this->restaurant->name,
                'code' => $this->restaurant->code,
            ] : null,
            'driver' => $this->relationLoaded('driver') && $this->driver ? [
                'id' => $this->driver->id,
                'driver_code' => $this->driver->driver_code,
                'name' => $this->driver->user?->name,
                'phone' => $this->driver->phone,
                'vehicle_type' => $this->driver->vehicle_type,
                'vehicle_plate' => $this->driver->vehicle_plate,
                'self_delivery' => $this->driver->self_delivery,
            ] : null,
            'total_stops' => $this->relationLoaded('stops') ? $this->stops->count() : null,
            'stops' => DeliveryStopResource::collection($this->whenLoaded('stops')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
