<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Payment
 */
class PaymentResource extends JsonResource
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
            'provider' => $this->provider,
            'method' => $this->method,
            'status' => $this->status,
            'amount_minor' => $this->amount_minor,
            'amount_formatted' => number_format($this->amount_minor / 100, 2),
            'currency' => $this->currency,
            'provider_reference' => $this->provider_reference,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'order' => $this->relationLoaded('order') && $this->order ? [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
                'status' => $this->order->status,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
