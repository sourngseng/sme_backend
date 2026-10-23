<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\InitiatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Initiate a payment transaction for an existing pending order.
     */
    public function initiate(InitiatePaymentRequest $request, PaymentGatewayManager $gatewayManager): JsonResponse
    {
        $validated = $request->validated();
        $order = Order::with(['school.mealPolicy'])->findOrFail($validated['order_id']);

        $this->authorize('view', $order);

        if (! in_array($order->status, ['pending', 'failed'], true)) {
            throw ValidationException::withMessages([
                'order_id' => ["Order #{$order->order_number} is already {$order->status} and cannot accept new payments."],
            ]);
        }

        $provider = strtoupper($validated['provider']);

        // Create initial pending payment record
        $payment = Payment::create([
            'order_id' => $order->id,
            'provider' => $provider,
            'method' => $validated['method'] ?? strtolower($provider),
            'status' => 'pending',
            'amount_minor' => $order->total_minor,
            'currency' => $order->currency,
        ]);

        $gateway = $gatewayManager->resolve($provider);
        $initiationData = $gateway->initiatePayment($payment);

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated successfully.',
            'data' => array_merge($initiationData, [
                'payment' => new PaymentResource($payment->fresh(['order'])),
            ]),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified payment record.
     */
    public function show(Payment $payment): JsonResponse
    {
        $this->authorize('view', $payment);

        $payment->load(['order', 'transactions']);

        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment),
        ], Response::HTTP_OK);
    }
}
