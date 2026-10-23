<?php

namespace App\Http\Controllers\Api\V1\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\CreateDeliveryBatchRequest;
use App\Http\Resources\DeliveryBatchResource;
use App\Models\DeliveryBatch;
use App\Models\DeliveryStop;
use App\Models\Driver;
use App\Models\OrderSchedule;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DeliveryDispatchController extends Controller
{
    /**
     * List delivery batches with filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DeliveryBatch::with(['restaurant', 'driver.user', 'stops.school', 'stops.location', 'stops.schedule.orderItem']);

        if ($date = $request->query('delivery_date')) {
            $query->whereDate('delivery_date', $date);
        }

        if ($shift = $request->query('shift')) {
            $query->where('shift', $shift);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($restaurantId = $request->query('restaurant_id')) {
            $query->where('restaurant_id', $restaurantId);
        }

        if ($driverId = $request->query('driver_id')) {
            $query->where('driver_id', $driverId);
        }

        $batches = $query->latest('delivery_date')->paginate(min((int) $request->query('per_page', 15), 100));

        return response()->json([
            'success' => true,
            'data' => DeliveryBatchResource::collection($batches),
            'meta' => [
                'current_page' => $batches->currentPage(),
                'last_page' => $batches->lastPage(),
                'per_page' => $batches->perPage(),
                'total' => $batches->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Create delivery batch and auto-sequence delivery stops for a date and shift.
     */
    public function store(CreateDeliveryBatchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $batch = DB::transaction(function () use ($validated) {
            $restaurant = Restaurant::findOrFail($validated['restaurant_id']);
            $driver = ! empty($validated['driver_id']) ? Driver::findOrFail($validated['driver_id']) : null;

            $batchStatus = $driver ? 'assigned' : 'planned';

            $deliveryBatch = DeliveryBatch::create([
                'restaurant_id' => $restaurant->id,
                'driver_id' => $driver?->id,
                'delivery_date' => $validated['delivery_date'],
                'shift' => $validated['shift'] ?? 'lunch',
                'status' => $batchStatus,
                'notes' => $validated['notes'] ?? null,
            ]);

            $schedules = OrderSchedule::with(['order.student', 'order.school'])
                ->whereIn('id', $validated['schedule_ids'])
                ->get();

            $sequence = 1;
            foreach ($schedules as $schedule) {
                DeliveryStop::create([
                    'delivery_batch_id' => $deliveryBatch->id,
                    'order_schedule_id' => $schedule->id,
                    'school_id' => $schedule->order->school_id,
                    'school_location_id' => $schedule->school_location_id,
                    'sequence_no' => $sequence++,
                    'status' => 'pending',
                ]);
            }

            return $deliveryBatch;
        });

        $batch->load(['restaurant', 'driver.user', 'stops.school', 'stops.location', 'stops.schedule.orderItem']);

        return response()->json([
            'success' => true,
            'message' => 'Delivery batch created and dispatched successfully.',
            'data' => new DeliveryBatchResource($batch),
        ], Response::HTTP_CREATED);
    }
}
