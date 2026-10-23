<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OrderSchedule
 */
class OrderScheduleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $now = Carbon::now($tz);

        $canModify = $this->status === 'scheduled'
            && (! $this->cutoff_at || $now->lessThanOrEqualTo($this->cutoff_at));

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_item_id' => $this->order_item_id,
            'delivery_date' => $this->delivery_date instanceof Carbon ? $this->delivery_date->toDateString() : (string) $this->delivery_date,
            'shift' => $this->shift,
            'status' => $this->status,
            'cutoff_at' => $this->cutoff_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
            'can_skip_or_cancel' => $canModify,
            'location' => $this->relationLoaded('location') && $this->location ? [
                'id' => $this->location->id,
                'name' => $this->location->name,
                'building' => $this->location->building,
                'floor' => $this->location->floor,
                'room' => $this->location->room,
            ] : null,
            'item' => $this->relationLoaded('orderItem') && $this->orderItem ? [
                'name' => $this->orderItem->item_name_snapshot,
                'unit_price_minor' => $this->orderItem->unit_price_minor,
                'calories_kcal' => $this->orderItem->calories_kcal_snapshot,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
