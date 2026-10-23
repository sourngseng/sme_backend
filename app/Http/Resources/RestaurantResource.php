<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'owner_user_id' => $this->owner_user_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'commission_default_pct' => $this->commission_default_pct ? (float) $this->commission_default_pct : 0,
            'address' => $this->address,
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'owner' => new UserResource($this->whenLoaded('owner')),
            'staff' => RestaurantStaffResource::collection($this->whenLoaded('staff')),
            'menu_items_count' => $this->whenCounted('menuItems'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
