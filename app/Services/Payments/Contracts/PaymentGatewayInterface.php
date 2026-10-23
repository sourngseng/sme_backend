<?php

namespace App\Services\Payments\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Get provider name.
     */
    public function getProviderName(): string;

    /**
     * Initiate payment transaction with provider.
     *
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function initiatePayment(Payment $payment, array $options = []): array;

    /**
     * Verify webhook cryptographic signature.
     *
     * @param array<string, mixed> $headers
     */
    public function verifySignature(string $rawPayload, string $signature, array $headers = []): bool;

    /**
     * Normalize incoming webhook payload to standard platform schema.
     *
     * @param array<string, mixed> $payload
     * @return array{
     *     event_id: string,
     *     event_type: string,
     *     provider_reference: ?string,
     *     payment_id: ?string,
     *     status: string,
     *     amount_minor: ?int,
     *     currency: ?string,
     *     raw_status: ?string
     * }
     */
    public function parseWebhookPayload(array $payload): array;
}
