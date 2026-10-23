<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'uuid', 'exists:orders,id'],
            'provider' => ['required', 'string', 'in:KHQR,khqr,ABA,aba,ACLEDA,acleda,COD,cod'],
            'method' => ['nullable', 'string', 'max:50'],
        ];
    }
}
