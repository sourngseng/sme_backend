<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class OtpVerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone_or_email' => ['required', 'string', 'max:190'],
            'otp' => ['required', 'string', 'size:6'],
            'full_name' => ['nullable', 'string', 'max:150'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
