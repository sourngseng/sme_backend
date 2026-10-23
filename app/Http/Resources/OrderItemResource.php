<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\OrderItem
 */
class OrderItemResource extends JsonResource
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
            'order_id' => $this->order_id,
            'menu_version_id' => $this->menu_version_id,
            'item_name_snapshot' => $this->item_name_snapshot,
            'unit_price_minor' => $this->unit_price_minor,
            'unit_price_formatted' => number_format($this->unit_price_minor / 100, 2),
            'quantity' => $this->quantity,
            'line_total_minor' => $this->line_total_minor,
            'line_total_formatted' => number_format($this->line_total_minor / 100, 2),
            'calories_kcal_snapshot' => $this->calories_kcal_snapshot ? (float) $this->calories_kcal_snapshot : null,
            'nutrition_snapshot' => $this->nutrition_snapshot,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
