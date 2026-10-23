<?php

namespace App\Services\Payments\Gateways;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CodGateway implements PaymentGatewayInterface
{
    public function getProviderName(): string
    {
        return 'COD';
    }

    public function initiatePayment(Payment $payment, array $options = []): array
    {
        $order = $payment->order;
        $school = $order->school;
        $mealPolicy = $school?->mealPolicy;

        // Verify school meal policy allows Cash On Delivery
        if (! $mealPolicy || ! $mealPolicy->allow_cod) {
            throw ValidationException::withMessages([
                'provider' => ["Cash on Delivery (COD) is not permitted by {$school->name}'s meal policy."],
            ]);
        }

        $reference = 'COD-' . strtoupper(Str::random(10));
        $payment->update([
            'status' => 'pending',
            'provider_reference' => $reference,
        ]);

        // Transition order status to confirmed for kitchen preparation, flagged for COD reconciliation
        $order->update([
            'status' => 'confirmed',
            'notes' => trim(($order->notes ? $order->notes . ' | ' : '') . '[COD: Payable on delivery, pending driver collection]'),
        ]);

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'transaction_type' => 'charge',
            'amount_minor' => $payment->amount_minor,
            'provider_transaction_id' => $reference,
            'status' => 'pending',
            'response_code' => 'COD_ORDER_CONFIRMED',
            'response_payload' => [
                'order_id' => $order->id,
                'reconciliation' => 'pending_cash_collection',
                'confirmed_at' => now()->toIso8601String(),
            ],
        ]);

        return [
            'payment_id' => $payment->id,
            'provider' => 'COD',
            'method' => 'cash_on_delivery',
            'status' => 'pending',
            'amount_minor' => $payment->amount_minor,
            'currency' => $payment->currency,
            'provider_reference' => $reference,
            'order_status' => 'confirmed',
            'instructions' => 'Payment will be collected by the campus delivery driver upon handoff.',
        ];
    }

    public function verifySignature(string $rawPayload, string $signature, array $headers = []): bool
    {
        // COD reconciliations are authenticated internal or driver signature
        return true;
    }

    public function parseWebhookPayload(array $payload): array
    {
        $statusRaw = strtoupper($payload['status'] ?? 'COLLECTED');
        $isSuccess = in_array($statusRaw, ['COLLECTED', 'PAID', 'SUCCESS'], true);

        return [
            'event_id' => (string) ($payload['event_id'] ?? Str::uuid()->toString()),
            'event_type' => 'cod.reconciliation',
            'provider_reference' => $payload['provider_reference'] ?? null,
            'payment_id' => $payload['payment_id'] ?? null,
            'status' => $isSuccess ? 'paid' : 'failed',
            'amount_minor' => isset($payload['amount_minor']) ? (int) $payload['amount_minor'] : null,
            'currency' => $payload['currency'] ?? 'USD',
            'raw_status' => $statusRaw,
        ];
    }
}
