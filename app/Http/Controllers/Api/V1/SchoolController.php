<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreSchoolRequest;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Models\OrderSchedule;
use App\Models\School;
use App\Models\SchoolMealPolicy;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SchoolController extends Controller
{
    /**
     * List schools with filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', School::class);

        $query = School::with(['shifts', 'locations', 'mealPolicy']);
        $user = $request->user();

        // Enforce object-level restriction for school admins if configured
        if ($user && $user->hasRole('school_admin') && ! $user->hasRole('super_admin')) {
            if ($user->school_id) {
                $query->where('id', $user->school_id);
            }
        }

        if ($search = $request->query('search')) {
            $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('name', $like, "%{$search}%")
                    ->orWhere('code', $like, "%{$search}%")
                    ->orWhere('address', $like, "%{$search}%");
            });
        }

        if ($level = $request->query('level')) {
            $query->where('level', $level);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->query('per_page', 15);
        $schools = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => SchoolResource::collection($schools),
            'meta' => [
                'current_page' => $schools->currentPage(),
                'last_page' => $schools->lastPage(),
                'per_page' => $schools->perPage(),
                'total' => $schools->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created school.
     */
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $school = DB::transaction(function () use ($validated) {
            $school = School::create($validated);

            // Auto-provision initial meal policy with platform defaults
            SchoolMealPolicy::firstOrCreate(
                ['school_id' => $school->id],
                [
                    'default_cutoff_minutes' => 120,
                    'allowed_meal_types' => ['breakfast', 'lunch', 'snack'],
                    'allow_cod' => false,
                ]
            );

            return $school;
        });

        $school->load(['shifts', 'locations', 'mealPolicy']);

        return response()->json([
            'success' => true,
            'message' => 'School created successfully.',
            'data' => new SchoolResource($school),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified school.
     */
    public function show(School $school): JsonResponse
    {
        $this->authorize('view', $school);

        $school->load(['shifts', 'locations', 'mealPolicy']);

        return response()->json([
            'success' => true,
            'data' => new SchoolResource($school),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified school.
     */
    public function update(UpdateSchoolRequest $request, School $school): JsonResponse
    {
        $validated = $request->validated();
        $school->update($validated);

        $school->load(['shifts', 'locations', 'mealPolicy']);

        return response()->json([
            'success' => true,
            'message' => 'School updated successfully.',
            'data' => new SchoolResource($school),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified school.
     */
    public function destroy(School $school): JsonResponse
    {
        $this->authorize('delete', $school);

        $school->delete();

        return response()->json([
            'success' => true,
            'message' => 'School deleted successfully.',
        ], Response::HTTP_OK);
    }

    /**
     * Get expected meals roster for the school grouped by delivery_date, shift, grade_level, class_name, and location.
     */
    public function mealRoster(Request $request, School $school): JsonResponse
    {
        $this->authorize('viewMealRoster', $school);

        $tz = config('app.timezone', 'Asia/Phnom_Penh');
        $deliveryDate = $request->query('delivery_date', Carbon::now($tz)->toDateString());
        $shift = $request->query('shift');
        $locationId = $request->query('school_location_id');
        $gradeLevel = $request->query('grade_level');
        $className = $request->query('class_name');
        $status = $request->query('status');

        $query = OrderSchedule::with([
            'order.student',
            'orderItem',
            'location',
        ])
            ->whereHas('order', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            });

        if ($deliveryDate) {
            $query->whereDate('delivery_date', $deliveryDate);
        }

        if ($shift) {
            $query->where('shift', $shift);
        }

        if ($locationId) {
            $query->where('school_location_id', $locationId);
        }

        if ($gradeLevel) {
            $query->whereHas('order.student', function ($q) use ($gradeLevel) {
                $q->where('grade_level', $gradeLevel);
            });
        }

        if ($className) {
            $query->whereHas('order.student', function ($q) use ($className) {
                $q->where('class_name', $className);
            });
        }

        if ($status) {
            $query->where('status', $status);
        } else {
            $query->whereNotIn('status', ['cancelled', 'skipped']);
        }

        $schedules = $query->orderBy('delivery_date')
            ->orderBy('shift')
            ->get();

        $grouped = $schedules->groupBy(function ($item) {
            $d = $item->delivery_date instanceof Carbon ? $item->delivery_date->toDateString() : (string) $item->delivery_date;
            $s = $item->shift ?? 'lunch';
            $g = $item->order?->student?->grade_level ?? 'N/A';
            $c = $item->order?->student?->class_name ?? 'N/A';
            $l = $item->school_location_id ?? 'N/A';

            return "{$d}|{$s}|{$g}|{$c}|{$l}";
        })->map(function ($items) {
            $first = $items->first();
            $student = $first->order?->student;
            $location = $first->location;

            return [
                'delivery_date' => $first->delivery_date instanceof Carbon ? $first->delivery_date->toDateString() : (string) $first->delivery_date,
                'shift' => $first->shift,
                'grade_level' => $student?->grade_level,
                'class_name' => $student?->class_name,
                'school_location_id' => $first->school_location_id,
                'location' => $location ? [
                    'id' => $location->id,
                    'name' => $location->name,
                    'building' => $location->building,
                    'floor' => $location->floor,
                    'room' => $location->room,
                ] : null,
                'total_meals' => $items->count(),
                'roster_items' => $items->map(function ($schedule) {
                    return [
                        'schedule_id' => $schedule->id,
                        'order_id' => $schedule->order_id,
                        'order_number' => $schedule->order?->order_number,
                        'student_id' => $schedule->order?->student_id,
                        'student_code' => $schedule->order?->student?->student_code,
                        'student_name' => $schedule->order?->student?->full_name,
                        'meal_name' => $schedule->orderItem?->item_name_snapshot,
                        'status' => $schedule->status,
                        'cutoff_at' => $schedule->cutoff_at?->toIso8601String(),
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'school' => [
                    'id' => $school->id,
                    'code' => $school->code,
                    'name' => $school->name,
                ],
                'delivery_date' => $deliveryDate,
                'total_groups' => $grouped->count(),
                'total_meals' => $schedules->count(),
                'roster' => $grouped,
            ],
        ], Response::HTTP_OK);
    }
}
