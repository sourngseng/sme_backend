<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllergenResource extends JsonResource
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
            'description' => $this->description,
            'severity' => $this->whenPivotLoaded('menu_allergens', fn () => $this->pivot->severity),
            'notes' => $this->whenPivotLoaded('menu_allergens', fn () => $this->pivot->notes),
        ];
    }
}
