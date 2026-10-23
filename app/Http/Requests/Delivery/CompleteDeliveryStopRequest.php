<?php

namespace App\Http\Requests\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class CompleteDeliveryStopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'proof_type' => ['required', 'string', 'in:QR,photo,signature,OTP,school_confirmation'],
            'photo_url' => ['nullable', 'string', 'max:1000'],
            'signature_url' => ['nullable', 'string', 'max:1000'],
            'recipient_name' => ['nullable', 'string', 'max:150'],
            'otp_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
