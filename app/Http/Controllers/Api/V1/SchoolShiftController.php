<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreSchoolShiftRequest;
use App\Http\Requests\School\UpdateSchoolShiftRequest;
use App\Http\Resources\SchoolShiftResource;
use App\Models\School;
use App\Models\SchoolShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolShiftController extends Controller
{
    /**
     * List operating shifts for a school.
     */
    public function index(Request $request, ?School $school = null): JsonResponse
    {
        $schoolId = $school?->id ?? $request->query('school_id');

        $query = SchoolShift::query();
        $user = $request->user();

        // Enforce school admin constraint
        if ($user && $user->hasRole('school_admin') && ! $user->hasRole('super_admin')) {
            $schoolId = $user->school_id;
        }

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $shifts = $query->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'data' => SchoolShiftResource::collection($shifts),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created shift.
     */
    public function store(StoreSchoolShiftRequest $request, ?School $school = null): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $schoolId = $school?->id
            ?? $validated['school_id']
            ?? ($user->hasRole('school_admin') ? $user->school_id : null);

        if (! $schoolId) {
            return response()->json([
                'success' => false,
                'message' => 'School ID is required to create a shift.',
                'error_code' => 'SCHOOL_REQUIRED',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Verify authorization for target school
        $targetSchool = School::findOrFail($schoolId);
        $this->authorize('manageShifts', $targetSchool);

        $shift = SchoolShift::create(array_merge($validated, [
            'school_id' => $schoolId,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'School shift created successfully.',
            'data' => new SchoolShiftResource($shift),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified shift.
     */
    public function show(SchoolShift $shift): JsonResponse
    {
        $this->authorize('view', $shift);

        return response()->json([
            'success' => true,
            'data' => new SchoolShiftResource($shift),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified shift.
     */
    public function update(UpdateSchoolShiftRequest $request, SchoolShift $shift): JsonResponse
    {
        $shift->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'School shift updated successfully.',
            'data' => new SchoolShiftResource($shift),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified shift.
     */
    public function destroy(SchoolShift $shift): JsonResponse
    {
        $this->authorize('delete', $shift);

        $shift->delete();

        return response()->json([
            'success' => true,
            'message' => 'School shift deleted successfully.',
        ], Response::HTTP_OK);
    }
}
