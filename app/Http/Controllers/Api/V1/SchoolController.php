<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreSchoolRequest;
use App\Http\Requests\School\UpdateSchoolRequest;
use App\Http\Resources\SchoolResource;
use App\Models\School;
use App\Models\SchoolMealPolicy;
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
}
