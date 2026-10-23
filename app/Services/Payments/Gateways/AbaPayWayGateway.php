<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;

class AbaPayWayGateway implements PaymentGatewayInterface
{
    public function getProviderName(): string
    {
        return 'ABA';
    }

    public function initiatePayment(Payment $payment, array $options = []): array
    {
        $reference = 'ABA-' . strtoupper(Str::random(12));
        $payment->update([
            'provider_reference' => $reference,
        ]);

        $merchantId = config('services.aba_payway.merchant_id', 'ec432100');
        $apiKey = config('services.aba_payway.api_key', 'aba_secret_key');
        $amountFormatted = number_format($payment->amount_minor / 100, 2, '.', '');

        // Standard ABA PayWay SHA512 hash
        $hashData = base64_encode(hash_hmac('sha512', $merchantId . $reference . $amountFormatted, $apiKey, true));

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount_minor' => $payment->amount_minor,
            'provider_transaction_id' => $reference,
            'status' => 'pending',
            'response_code' => 'CHECKOUT_INITIALIZED',
            'response_payload' => [
                'merchant_id' => $merchantId,
                'hash' => $hashData,
            ],
        ]);

        return [
            'payment_id' => $payment->id,
            'provider' => 'ABA',
            'method' => 'aba_payway',
            'status' => 'pending',
            'amount_minor' => $payment->amount_minor,
            'currency' => $payment->currency,
            'provider_reference' => $reference,
            'checkout_url' => 'https://checkout.payway.com.kh/api/payment-gateway/v1/payments/purchase',
            'hash' => $hashData,
        ];
    }

    public function verifySignature(string $rawPayload, string $signature, array $headers = []): bool
    {
        $apiKey = config('services.aba_payway.api_key', 'aba_secret_key');
        $expected = base64_encode(hash_hmac('sha512', $rawPayload, $apiKey, true));

        return hash_equals($expected, $signature) || hash_equals(hash_hmac('sha256', $rawPayload, $apiKey), $signature);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $statusRaw = strtoupper($payload['status'] ?? $payload['tran_status'] ?? 'UNKNOWN');
        $isSuccess = in_array($statusRaw, ['0', 'SUCCESS', 'PAID', 'APPROVED'], true);

        return [
            'event_id' => (string) ($payload['event_id'] ?? $payload['tran_id'] ?? Str::uuid()->toString()),
            'event_type' => (string) ($payload['event_type'] ?? 'payment.charge.success'),
            'provider_reference' => $payload['provider_reference'] ?? $payload['tran_id'] ?? null,
            'payment_id' => $payload['payment_id'] ?? null,
            'status' => $isSuccess ? 'paid' : 'failed',
            'amount_minor' => isset($payload['amount_minor']) ? (int) $payload['amount_minor'] : (isset($payload['amount']) ? (int) round($payload['amount'] * 100) : null),
            'currency' => $payload['currency'] ?? 'USD',
            'raw_status' => $statusRaw,
        ];
    }
}
