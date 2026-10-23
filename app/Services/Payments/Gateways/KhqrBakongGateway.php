<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;

class KhqrBakongGateway implements PaymentGatewayInterface
{
    public function getProviderName(): string
    {
        return 'KHQR';
    }

    public function initiatePayment(Payment $payment, array $options = []): array
    {
        $reference = 'KHQR-' . strtoupper(Str::random(12));
        $payment->update([
            'provider_reference' => $reference,
        ]);

        // Generate standardized Bakong EMVCo payload
        $merchantAccount = config('services.khqr.merchant_account', 'school_meals@bakong');
        $amountFormatted = number_format($payment->amount_minor / 100, 2, '.', '');
        $qrPayload = sprintf(
            '00020101021229370016%s520458125303840540%s5802KH5915%s6010Phnom Penh62200116%s6304',
            $merchantAccount,
            $amountFormatted,
            'SCHOOL MEAL APP',
            $reference
        );

        // Record initial charge intent transaction
        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount_minor' => $payment->amount_minor,
            'provider_transaction_id' => $reference,
            'status' => 'pending',
            'response_code' => 'QR_GENERATED',
            'response_payload' => [
                'qr_string' => $qrPayload,
                'md5' => md5($qrPayload),
            ],
        ]);

        return [
            'payment_id' => $payment->id,
            'provider' => 'KHQR',
            'method' => 'bakong_qr',
            'status' => 'pending',
            'amount_minor' => $payment->amount_minor,
            'currency' => $payment->currency,
            'provider_reference' => $reference,
            'qr_payload' => $qrPayload,
            'deep_link' => 'bakong://qr?data=' . urlencode($qrPayload),
        ];
    }

    public function verifySignature(string $rawPayload, string $signature, array $headers = []): bool
    {
        $secret = config('services.khqr.secret', 'khqr_webhook_secret_key');
        $expected = hash_hmac('sha256', $rawPayload, $secret);

        return hash_equals($expected, $signature);
    }

    public function parseWebhookPayload(array $payload): array
    {
        $statusRaw = strtoupper($payload['status'] ?? $payload['transaction_status'] ?? 'UNKNOWN');
        $isSuccess = in_array($statusRaw, ['SUCCESS', 'PAID', 'COMPLETED'], true);

        return [
            'event_id' => (string) ($payload['event_id'] ?? $payload['txn_id'] ?? Str::uuid()->toString()),
            'event_type' => (string) ($payload['event_type'] ?? 'payment.completed'),
            'provider_reference' => $payload['provider_reference'] ?? $payload['bill_number'] ?? null,
            'payment_id' => $payload['payment_id'] ?? null,
            'status' => $isSuccess ? 'paid' : 'failed',
            'amount_minor' => isset($payload['amount_minor']) ? (int) $payload['amount_minor'] : (isset($payload['amount']) ? (int) round($payload['amount'] * 100) : null),
            'currency' => $payload['currency'] ?? 'USD',
            'raw_status' => $statusRaw,
        ];
    }
}
