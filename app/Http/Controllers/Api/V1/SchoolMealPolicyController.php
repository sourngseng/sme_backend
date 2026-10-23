<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\UpdateSchoolMealPolicyRequest;
use App\Http\Resources\SchoolMealPolicyResource;
use App\Models\School;
use App\Models\SchoolMealPolicy;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SchoolMealPolicyController extends Controller
{
    /**
     * Retrieve meal ordering and cutoff policies for a school.
     */
    public function show(School $school): JsonResponse
    {
        $this->authorize('view', $school);

        $policy = $school->mealPolicy ?? SchoolMealPolicy::firstOrCreate(
            ['school_id' => $school->id],
            [
                'default_cutoff_minutes' => 120,
                'allowed_meal_types' => ['breakfast', 'lunch', 'snack'],
                'allow_cod' => false,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => new SchoolMealPolicyResource($policy),
        ], Response::HTTP_OK);
    }

    /**
     * Update meal ordering and cutoff policies for a school.
     */
    public function update(UpdateSchoolMealPolicyRequest $request, School $school): JsonResponse
    {
        $policy = $school->mealPolicy ?? SchoolMealPolicy::create([
            'school_id' => $school->id,
            'default_cutoff_minutes' => 120,
            'allowed_meal_types' => ['breakfast', 'lunch', 'snack'],
            'allow_cod' => false,
        ]);

        $policy->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'School meal policy updated successfully.',
            'data' => new SchoolMealPolicyResource($policy),
        ], Response::HTTP_OK);
    }
}
