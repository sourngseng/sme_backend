<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\SkipScheduleRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderScheduleResource;
use App\Models\Order;
use App\Models\OrderSchedule;
use App\Services\OrderCreationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a paginated listing of orders.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $user = $request->user();
        $query = Order::with(['items', 'schedules.location', 'student', 'school', 'restaurant']);

        // Object-level filtering by user role
        if (! $user->hasRole('super_admin')) {
            if ($user->hasRole('parent') || $user->guardian) {
                $guardianId = $user->guardian?->id;
                $query->where('guardian_id', $guardianId);
            } elseif ($user->hasRole(['restaurant_admin', 'restaurant_staff', 'restaurant_owner']) || $user->ownedRestaurants()->exists()) {
                $restaurantId = $user->restaurant_id ?? $user->ownedRestaurants()->value('id');
                $query->where('restaurant_id', $restaurantId);
            } elseif ($user->hasRole('school_admin')) {
                $query->where('school_id', $user->school_id);
            }
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($studentId = $request->query('student_id')) {
            $query->where('student_id', $studentId);
        }

        if ($restaurantId = $request->query('restaurant_id')) {
            $query->where('restaurant_id', $restaurantId);
        }

        if ($schoolId = $request->query('school_id')) {
            $query->where('school_id', $schoolId);
        }

        $perPage = min((int) $request->query('per_page', 15), 100);
        $orders = $query->latest('placed_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created meal order with 1-7 day schedules.
     */
    public function store(CreateOrderRequest $request, OrderCreationService $service): JsonResponse
    {
        $this->authorize('create', Order::class);

        $order = $service->createOrder($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully.',
            'data' => new OrderResource($order),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified order with snapshots and schedules.
     */
    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['items', 'schedules.location', 'student', 'school', 'restaurant', 'guardian.user']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ], Response::HTTP_OK);
    }

    /**
     * Skip an individual day meal schedule prior to its cutoff.
     */
    public function skipSchedule(SkipScheduleRequest $request, OrderSchedule $schedule, OrderCreationService $service): JsonResponse
    {
        $this->authorize('skip', $schedule);

        $updated = $service->updateScheduleStatus($schedule, 'skipped', $request->input('reason'));

        return response()->json([
            'success' => true,
            'message' => 'Meal schedule skipped successfully.',
            'data' => new OrderScheduleResource($updated->fresh(['location', 'orderItem'])),
        ], Response::HTTP_OK);
    }

    /**
     * Cancel an individual day meal schedule prior to its cutoff.
     */
    public function cancelSchedule(SkipScheduleRequest $request, OrderSchedule $schedule, OrderCreationService $service): JsonResponse
    {
        $this->authorize('cancel', $schedule);

        $updated = $service->updateScheduleStatus($schedule, 'cancelled', $request->input('reason'));

        return response()->json([
            'success' => true,
            'message' => 'Meal schedule cancelled successfully.',
            'data' => new OrderScheduleResource($updated->fresh(['location', 'orderItem'])),
        ], Response::HTTP_OK);
    }
}
