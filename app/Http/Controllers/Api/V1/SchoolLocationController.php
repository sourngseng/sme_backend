<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\StoreSchoolLocationRequest;
use App\Http\Requests\School\UpdateSchoolLocationRequest;
use App\Http\Resources\SchoolLocationResource;
use App\Models\School;
use App\Models\SchoolLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SchoolLocationController extends Controller
{
    /**
     * List delivery locations for a school.
     */
    public function index(Request $request, ?School $school = null): JsonResponse
    {
        $schoolId = $school?->id ?? $request->query('school_id');

        $query = SchoolLocation::query();
        $user = $request->user();

        // Enforce school admin constraint
        if ($user && $user->hasRole('school_admin') && ! $user->hasRole('super_admin')) {
            $schoolId = $user->school_id;
        }

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        $locations = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => SchoolLocationResource::collection($locations),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created delivery location.
     */
    public function store(StoreSchoolLocationRequest $request, ?School $school = null): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $schoolId = $school?->id
            ?? $validated['school_id']
            ?? ($user->hasRole('school_admin') ? $user->school_id : null);

        if (! $schoolId) {
            return response()->json([
                'success' => false,
                'message' => 'School ID is required to create a delivery location.',
                'error_code' => 'SCHOOL_REQUIRED',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $targetSchool = School::findOrFail($schoolId);
        $this->authorize('manageLocations', $targetSchool);

        $location = SchoolLocation::create(array_merge($validated, [
            'school_id' => $schoolId,
        ]));

        return response()->json([
            'success' => true,
            'message' => 'School location created successfully.',
            'data' => new SchoolLocationResource($location),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified delivery location.
     */
    public function show(SchoolLocation $location): JsonResponse
    {
        $this->authorize('view', $location);

        return response()->json([
            'success' => true,
            'data' => new SchoolLocationResource($location),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified delivery location.
     */
    public function update(UpdateSchoolLocationRequest $request, SchoolLocation $location): JsonResponse
    {
        $location->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'School location updated successfully.',
            'data' => new SchoolLocationResource($location),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified delivery location.
     */
    public function destroy(SchoolLocation $location): JsonResponse
    {
        $this->authorize('delete', $location);

        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'School location deleted successfully.',
        ], Response::HTTP_OK);
    }
}
