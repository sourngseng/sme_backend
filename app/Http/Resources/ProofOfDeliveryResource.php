<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ProofOfDelivery
 */
class ProofOfDeliveryResource extends JsonResource
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
            'delivery_stop_id' => $this->delivery_stop_id,
            'proof_type' => $this->proof_type,
            'photo_url' => $this->photo_url,
            'signature_url' => $this->signature_url,
            'recipient_name' => $this->recipient_name,
            'otp_verified' => (bool) $this->otp_verified,
            'notes' => $this->notes,
            'captured_at' => $this->captured_at?->toIso8601String(),
        ];
    }
}
