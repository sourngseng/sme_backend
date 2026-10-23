<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Payments\WebhookProcessingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming signed payment webhooks from bank and payment providers.
     */
    public function handle(string $provider, Request $request, WebhookProcessingService $service): JsonResponse
    {
        $result = $service->processWebhook($provider, $request);

        return response()->json($result, Response::HTTP_OK);
    }
}
