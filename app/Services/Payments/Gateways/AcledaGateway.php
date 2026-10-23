<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;

class AcledaGateway implements PaymentGatewayInterface
{
    public function getProviderName(): string
    {
        return 'ACLEDA';
    }

    public function initiatePayment(Payment $payment, array $options = []): array
    {
        $reference = 'ACL-' . strtoupper(Str::random(12));
        $payment->update([
            'provider_reference' => $reference,
        ]);

        $loginId = config('services.acleda.login_id', 'acleda_merchant');
        $secret = config('services.acleda.secret', 'acleda_secret_key');
        $amountFormatted = number_format($payment->amount_minor / 100, 2, '.', '');

        $token = hash_hmac('sha256', $loginId . $reference . $amountFormatted, $secret);

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount_minor' => $payment->amount_minor,
            'provider_transaction_id' => $reference,
            'status' => 'pending',
            'response_code' => 'TOKEN_GENERATED',
            'response_payload' => [
                'login_id' => $loginId,
                'token' => $token,
            ],
        ]);

        return [
            'payment_id' => $payment->id,
            'provider' => 'ACLEDA',
            'method' => 'acleda_xpay',
            'status' => 'pending',
            'amount_minor' => $payment->amount_minor,
            'currency' => $payment->currency,
            'provider_reference' => $reference,
            'xpay_token' => $token,
            'deep_link' => 'acledabank://xpay?token=' . $token,
        ];
    }

    public function verifySignature(string $rawPayload, string $signature, array $headers = []): bool
    {
        $secret = config('services.acleda.secret', 'acleda_secret_key');
        $expected = hash_hmac('sha256', $rawPayload, $secret);

        return hash_equals($expected, $signature);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $statusRaw = strtoupper($payload['status'] ?? $payload['payment_status'] ?? 'UNKNOWN');
        $isSuccess = in_array($statusRaw, ['SUCCESS', 'PAID', 'OK'], true);

        return [
            'event_id' => (string) ($payload['event_id'] ?? $payload['xpay_id'] ?? Str::uuid()->toString()),
            'event_type' => (string) ($payload['event_type'] ?? 'payment.xpay.completed'),
            'provider_reference' => $payload['provider_reference'] ?? $payload['invoice_no'] ?? null,
            'payment_id' => $payload['payment_id'] ?? null,
            'status' => $isSuccess ? 'paid' : 'failed',
            'amount_minor' => isset($payload['amount_minor']) ? (int) $payload['amount_minor'] : (isset($payload['amount']) ? (int) round($payload['amount'] * 100) : null),
            'currency' => $payload['currency'] ?? 'USD',
            'raw_status' => $statusRaw,
        ];
    }
}
