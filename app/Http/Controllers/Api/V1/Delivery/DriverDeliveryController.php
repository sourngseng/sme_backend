<?php

namespace App\Http\Controllers\Api\V1\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\CompleteDeliveryStopRequest;
use App\Http\Requests\Delivery\DeliveryExceptionRequest;
use App\Http\Resources\DeliveryBatchResource;
use App\Http\Resources\DeliveryStopResource;
use App\Models\DeliveryBatch;
use App\Models\DeliveryStop;
use App\Models\Driver;
use App\Models\ProofOfDelivery;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class DriverDeliveryController extends Controller
{
    /**
     * Get active delivery batches assigned to the authenticated driver for today.
     */
    public function todayBatches(Request $request): JsonResponse
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();

        if (! $driver && ! $user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'User is not registered as an active platform driver.',
            ], Response::HTTP_FORBIDDEN);
        }

        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $today = Carbon::now($tz)->toDateString();

        $query = DeliveryBatch::with([
            'restaurant',
            'driver.user',
            'stops.school',
            'stops.location',
            'stops.schedule.order.student',
            'stops.schedule.orderItem',
            'stops.proof',
        ])
            ->whereDate('delivery_date', $today);

        if ($driver) {
            $query->where('driver_id', $driver->id);
        }

        $batches = $query->orderBy('shift')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'delivery_date' => $today,
                'total_batches' => $batches->count(),
                'batches' => DeliveryBatchResource::collection($batches),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Mark delivery batch as picked up from kitchen.
     */
    public function pickupBatch(Request $request, DeliveryBatch $batch): JsonResponse
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();

        if ($driver && $batch->driver_id !== $driver->id && ! $user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'This delivery batch is assigned to another driver.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (in_array($batch->status, ['completed', 'cancelled'], true)) {
            throw ValidationException::withMessages([
                'batch' => ["Cannot pick up a batch that is already {$batch->status}."],
            ]);
        }

        DB::transaction(function () use ($batch) {
            $now = now();
            $batch->update([
                'status' => 'picked_up',
                'pickup_at' => $now,
            ]);

            $batch->stops()->where('status', 'pending')->update([
                'status' => 'picked_up',
            ]);
        });

        $batch->load(['restaurant', 'driver.user', 'stops.school', 'stops.location']);

        return response()->json([
            'success' => true,
            'message' => 'Delivery batch picked up successfully.',
            'data' => new DeliveryBatchResource($batch),
        ], Response::HTTP_OK);
    }

    /**
     * Verify meal package barcode/QR scan before handover.
     */
    public function verifyScan(Request $request, DeliveryStop $stop): JsonResponse
    {
        $code = trim((string) $request->input('package_code'));

        if ($code === '') {
            throw ValidationException::withMessages([
                'package_code' => ['Package scan code is required.'],
            ]);
        }

        if ($stop->status === 'delivered') {
            throw ValidationException::withMessages([
                'package_code' => ['This meal package has already been delivered.'],
            ]);
        }

        $stop->load(['schedule.order']);

        $expectedScheduleId = $stop->order_schedule_id;
        $expectedOrderNumber = $stop->schedule?->order?->order_number;

        $matches = strcasecmp($code, $expectedScheduleId) === 0
            || ($expectedOrderNumber && strcasecmp($code, $expectedOrderNumber) === 0)
            || stripos($code, $expectedScheduleId) !== false;

        if (! $matches) {
            throw ValidationException::withMessages([
                'package_code' => ['Scanned package code does not match this delivery stop.'],
            ]);
        }

        return response()->json([
            'success' => true,
            'verified' => true,
            'message' => 'Package verified successfully.',
            'data' => [
                'stop_id' => $stop->id,
                'order_schedule_id' => $stop->order_schedule_id,
                'order_number' => $expectedOrderNumber,
                'status' => $stop->status,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Complete delivery stop with proof of delivery (Delivered != Consumed business rule).
     */
    public function completeStop(CompleteDeliveryStopRequest $request, DeliveryStop $stop): JsonResponse
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();
        $batch = $stop->batch;

        if ($driver && $batch && $batch->driver_id !== $driver->id && ! $user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'This delivery stop is assigned to another driver.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($stop->status === 'delivered') {
            throw ValidationException::withMessages([
                'stop' => ['This stop has already been marked as delivered.'],
            ]);
        }

        $validated = $request->validated();

        $completedStop = DB::transaction(function () use ($stop, $validated, $batch) {
            $now = now();

            // 1. Create or update Proof of Delivery
            ProofOfDelivery::updateOrCreate(
                ['delivery_stop_id' => $stop->id],
                [
                    'proof_type' => $validated['proof_type'],
                    'photo_url' => $validated['photo_url'] ?? null,
                    'signature_url' => $validated['signature_url'] ?? null,
                    'recipient_name' => $validated['recipient_name'] ?? null,
                    'otp_verified' => ! empty($validated['otp_code']) || $validated['proof_type'] === 'OTP',
                    'notes' => $validated['notes'] ?? null,
                    'captured_at' => $now,
                ]
            );

            // 2. Update Delivery Stop status
            $stop->update([
                'status' => 'delivered',
                'completed_at' => $now,
            ]);

            // 3. Update Order Schedule status
            $schedule = $stop->schedule;
            if ($schedule) {
                $schedule->update([
                    'status' => 'delivered',
                    'delivered_at' => $now,
                ]);

                // 4. Synchronize parent Order status if all schedules delivered
                $order = $schedule->order;
                if ($order) {
                    $allDelivered = $order->schedules()->whereNotIn('status', ['delivered', 'cancelled', 'skipped'])->count() === 0;
                    if ($allDelivered) {
                        $order->update(['status' => 'delivered']);
                    }
                }
            }

            // Note: Delivered != Consumed. Do NOT automatically create nutrition_consumption_logs.

            // 5. Complete Delivery Batch if all stops are done
            if ($batch) {
                $pendingStops = $batch->stops()->whereNotIn('status', ['delivered', 'failed', 'returned'])->count();
                if ($pendingStops === 0) {
                    $batch->update([
                        'status' => 'completed',
                        'completed_at' => $now,
                    ]);
                }
            }

            return $stop->fresh(['proof', 'school', 'location', 'schedule.orderItem']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery stop completed and proof captured successfully.',
            'data' => new DeliveryStopResource($completedStop),
        ], Response::HTTP_OK);
    }

    /**
     * Record delivery exception for a stop (e.g., student absent, school closed).
     */
    public function recordException(DeliveryExceptionRequest $request, DeliveryStop $stop): JsonResponse
    {
        $user = $request->user();
        $driver = Driver::where('user_id', $user->id)->first();
        $batch = $stop->batch;

        if ($driver && $batch && $batch->driver_id !== $driver->id && ! $user->hasRole('super_admin')) {
            return response()->json([
                'message' => 'This delivery stop is assigned to another driver.',
            ], Response::HTTP_FORBIDDEN);
        }

        $validated = $request->validated();
        $action = $validated['action'] ?? 'failed';

        $updatedStop = DB::transaction(function () use ($stop, $validated, $action, $batch) {
            $now = now();

            $stop->update([
                'status' => $action,
                'exception_code' => $validated['exception_code'],
                'exception_note' => $validated['exception_note'],
                'completed_at' => $now,
            ]);

            // Update schedule
            $schedule = $stop->schedule;
            if ($schedule) {
                $schedule->update([
                    'status' => $action === 'returned' ? 'cancelled' : 'failed',
                ]);
            }

            // Trigger operational log / alert
            Log::warning("Delivery exception on Stop #{$stop->id}: Code {$validated['exception_code']}. Note: {$validated['exception_note']}");

            // Check if batch is completed
            if ($batch) {
                $pendingStops = $batch->stops()->whereNotIn('status', ['delivered', 'failed', 'returned'])->count();
                if ($pendingStops === 0) {
                    $batch->update([
                        'status' => 'completed',
                        'completed_at' => $now,
                    ]);
                }
            }

            return $stop->fresh(['school', 'location', 'schedule.orderItem']);
        });

        return response()->json([
            'success' => true,
            'message' => "Delivery stop marked as {$action} with exception logged.",
            'data' => new DeliveryStopResource($updatedStop),
        ], Response::HTTP_OK);
    }
}
