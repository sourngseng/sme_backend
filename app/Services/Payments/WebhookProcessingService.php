<?php

namespace App\Services\Payments;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Models\PaymentWebhook;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class WebhookProcessingService
{
    public function __construct(
        protected PaymentGatewayManager $gatewayManager
    ) {}

    /**
     * Process an incoming webhook callback from a payment provider with cryptographic signature verification.
     *
     * @return array<string, mixed>
     */
    public function processWebhook(string $providerName, Request $request): array
    {
        $gateway = $this->gatewayManager->resolve($providerName);
        $provider = $gateway->getProviderName();

        $rawPayload = $request->getContent();
        $payloadArray = $request->all();

        // Extract signature from standard headers
        $signature = $request->header('X-Signature')
            ?? $request->header('X-Webhook-Signature')
            ?? $request->header('Signature')
            ?? $request->header('X-PayWay-Signature')
            ?? '';

        $isSignatureValid = $gateway->verifySignature($rawPayload, $signature, $request->headers->all());

        if (! $isSignatureValid) {
            // Audit failed signature attempt
            PaymentWebhook::create([
                'provider' => $provider,
                'event_id' => 'sig_fail_' . uniqid(),
                'event_type' => 'signature_verification_failed',
                'payload' => $payloadArray ?: ['raw' => $rawPayload],
                'signature_valid' => false,
                'status' => 'failed',
                'processed_at' => now(),
            ]);

            throw new UnauthorizedHttpException('Webhook', 'Invalid webhook signature.');
        }

        // Parse normalized payload
        $eventData = $gateway->parseWebhookPayload($payloadArray);
        $eventId = $eventData['event_id'];

        // Enforce idempotency lock via unique (provider, event_id)
        try {
            $webhook = PaymentWebhook::firstOrCreate(
                [
                    'provider' => $provider,
                    'event_id' => $eventId,
                ],
                [
                    'event_type' => $eventData['event_type'],
                    'payload' => $payloadArray,
                    'signature_valid' => true,
                    'status' => 'received',
                ]
            );
        } catch (QueryException $e) {
            // Concurrent execution collision
            $webhook = PaymentWebhook::where('provider', $provider)
                ->where('event_id', $eventId)
                ->first();
        }

        if ($webhook && $webhook->status === 'processed') {
            return [
                'success' => true,
                'message' => 'Event already processed (Idempotent).',
                'event_id' => $eventId,
                'status' => 'already_processed',
            ];
        }

        // Process state transition inside database transaction
        return DB::transaction(function () use ($webhook, $eventData, $payloadArray) {
            // Find payment by payment_id or provider_reference
            $payment = null;
            if (! empty($eventData['payment_id'])) {
                $payment = Payment::with('order')->find($eventData['payment_id']);
            }
            if (! $payment && ! empty($eventData['provider_reference'])) {
                $payment = Payment::with('order')
                    ->where('provider_reference', $eventData['provider_reference'])
                    ->first();
            }

            if (! $payment) {
                if ($webhook) {
                    $webhook->update([
                        'status' => 'failed',
                        'processed_at' => now(),
                    ]);
                }

                return [
                    'success' => false,
                    'message' => 'Payment reference not found.',
                    'event_id' => $eventData['event_id'],
                ];
            }

            $order = $payment->order;
            $now = Carbon::now();

            if ($eventData['status'] === 'paid') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => $now,
                ]);

                // Record successful charge transaction
                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'transaction_type' => 'charge',
                    'amount_minor' => $eventData['amount_minor'] ?? $payment->amount_minor,
                    'provider_transaction_id' => $eventData['event_id'],
                    'status' => 'paid',
                    'response_code' => $eventData['raw_status'] ?? 'PAID',
                    'response_payload' => $payloadArray,
                    'created_at' => $now,
                ]);

                // Transition order status to confirmed
                if ($order && $order->status !== 'confirmed') {
                    $order->update([
                        'status' => 'confirmed',
                    ]);
                }

                if ($webhook) {
                    $webhook->update([
                        'status' => 'processed',
                        'processed_at' => $now,
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Payment processed successfully.',
                    'payment_id' => $payment->id,
                    'order_id' => $order?->id,
                    'status' => 'paid',
                ];
            } else {
                $payment->update([
                    'status' => 'failed',
                ]);

                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'transaction_type' => 'charge',
                    'amount_minor' => $eventData['amount_minor'] ?? $payment->amount_minor,
                    'provider_transaction_id' => $eventData['event_id'],
                    'status' => 'failed',
                    'response_code' => $eventData['raw_status'] ?? 'FAILED',
                    'response_payload' => $payloadArray,
                    'created_at' => $now,
                ]);

                if ($order && $order->status === 'pending') {
                    $order->update([
                        'status' => 'failed',
                    ]);
                }

                if ($webhook) {
                    $webhook->update([
                        'status' => 'processed',
                        'processed_at' => $now,
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Payment marked as failed.',
                    'payment_id' => $payment->id,
                    'status' => 'failed',
                ];
            }
        });
    }
}
