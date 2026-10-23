<?php

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotency
{
    /**
     * Handle an incoming request with strict idempotency verification and response caching.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only state-modifying requests require idempotency
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        $key = $request->header('Idempotency-Key') ?? $request->header('X-Idempotency-Key');

        if (! $key || trim($key) === '') {
            return response()->json([
                'message' => 'The Idempotency-Key header is required.',
                'errors' => [
                    'idempotency_key' => ['The Idempotency-Key header is required for this operation.'],
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userId = $request->user()?->id;
        $requestHash = hash('sha256', $request->method() . '|' . $request->fullUrl() . '|' . $request->getContent());
        $now = Carbon::now();

        $existing = IdempotencyKey::find($key);

        if ($existing) {
            // Check if expired
            if ($existing->expires_at && $now->greaterThan($existing->expires_at)) {
                $existing->delete();
            } else {
                // Check if request payload/URL hash matches
                if ($existing->request_hash !== $requestHash) {
                    return response()->json([
                        'message' => 'Idempotency-Key was already used with a different request payload.',
                        'errors' => [
                            'idempotency_key' => ['Idempotency key payload mismatch.'],
                        ],
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                // If concurrent request is in-flight (response_code is null)
                if ($existing->response_code === null) {
                    return response()->json([
                        'message' => 'A concurrent request with the same Idempotency-Key is currently being processed.',
                    ], Response::HTTP_CONFLICT);
                }

                // Return cached response
                return response()->json($existing->response_body, $existing->response_code)
                    ->header('X-Idempotency-Hit', 'true');
            }
        }

        // Create pending key lock
        $idempotencyRecord = IdempotencyKey::create([
            'key' => $key,
            'user_id' => $userId,
            'request_hash' => $requestHash,
            'response_code' => null,
            'response_body' => null,
            'expires_at' => $now->copy()->addHours(24),
            'created_at' => $now,
        ]);

        try {
            $response = $next($request);

            if ($response instanceof JsonResponse) {
                $idempotencyRecord->update([
                    'response_code' => $response->getStatusCode(),
                    'response_body' => $response->getData(true),
                ]);
            } elseif ($response instanceof Response) {
                $decoded = json_decode($response->getContent(), true);
                $idempotencyRecord->update([
                    'response_code' => $response->getStatusCode(),
                    'response_body' => is_array($decoded) ? $decoded : ['content' => $response->getContent()],
                ]);
            }

            return $response;
        } catch (\Throwable $e) {
            // Remove in-flight key lock on exception so client can safely retry
            $idempotencyRecord->delete();
            throw $e;
        }
    }
}
