<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guardian\RegisterGuardianStudentRequest;
use App\Http\Requests\Guardian\UpdateNutritionProfileRequest;
use App\Http\Resources\StudentNutritionProfileResource;
use App\Http\Resources\StudentResource;
use App\Models\Guardian;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentNutritionProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GuardianStudentController extends Controller
{
    /**
     * List all children/students linked to the authenticated parent/guardian.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $guardian = Guardian::firstOrCreate(['user_id' => $user->id]);

        $students = $guardian->students()
            ->with(['school', 'shift', 'nutritionProfile'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => StudentResource::collection($students),
        ], Response::HTTP_OK);
    }

    /**
     * Register or link a student to the authenticated guardian.
     */
    public function store(RegisterGuardianStudentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // 1. Ensure Guardian profile exists
        $guardian = Guardian::firstOrCreate(
            ['user_id' => $user->id],
            ['relationship' => $validated['relationship'] ?? 'Guardian']
        );

        $student = DB::transaction(function () use ($validated, $guardian) {
            $student = null;

            // Option A: Link existing student by ID or code
            if (! empty($validated['student_id'])) {
                $student = Student::findOrFail($validated['student_id']);
            } elseif (! empty($validated['student_code'])) {
                $student = Student::where('student_code', $validated['student_code'])->first();
                if (! $student) {
                    abort(Response::HTTP_NOT_FOUND, "Student with code {$validated['student_code']} not found.");
                }
            } else {
                // Option B: Register new student
                $school = School::findOrFail($validated['school_id']);
                $code = 'STU-' . strtoupper($school->code) . '-' . strtoupper(Str::random(6));

                $student = Student::create([
                    'student_code' => $code,
                    'school_id' => $validated['school_id'],
                    'school_shift_id' => $validated['school_shift_id'] ?? null,
                    'full_name' => $validated['full_name'],
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'grade_level' => $validated['grade_level'] ?? null,
                    'class_name' => $validated['class_name'] ?? null,
                    'status' => 'active',
                ]);
            }

            // Check if any primary guardian is already assigned
            $hasPrimary = $student->guardians()->wherePivot('is_primary', true)->exists();
            $isPrimary = $validated['is_primary'] ?? (! $hasPrimary);

            // Sync or attach guardian-student relationship with flags
            $guardian->students()->syncWithoutDetaching([
                $student->id => [
                    'is_primary' => (bool) $isPrimary,
                    'can_order' => (bool) ($validated['can_order'] ?? true),
                    'can_view_nutrition' => (bool) ($validated['can_view_nutrition'] ?? true),
                    'created_at' => now(),
                ],
            ]);

            // Optional initial nutrition profile
            if (isset($validated['dietary_preferences']) || isset($validated['allergen_ids']) || isset($validated['nutrition_notes'])) {
                StudentNutritionProfile::updateOrCreate(
                    ['student_id' => $student->id],
                    [
                        'dietary_preferences' => $validated['dietary_preferences'] ?? [],
                        'allergen_ids' => $validated['allergen_ids'] ?? [],
                        'notes' => $validated['nutrition_notes'] ?? null,
                    ]
                );
            }

            return $student;
        });

        $student->load(['school', 'shift', 'nutritionProfile']);

        // Fetch student through the guardian relationship to include pivot attributes
        $linkedStudent = $guardian->students()
            ->with(['school', 'shift', 'nutritionProfile'])
            ->where('students.id', $student->id)
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Child registered and linked successfully.',
            'data' => new StudentResource($linkedStudent ?? $student),
        ], Response::HTTP_CREATED);
    }

    /**
     * View details of a linked child.
     */
    public function show(Request $request, Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $user = $request->user();
        $guardian = $user->guardian;

        $linkedStudent = $guardian
            ? $guardian->students()->with(['school', 'shift', 'nutritionProfile'])->where('students.id', $student->id)->first()
            : null;

        return response()->json([
            'success' => true,
            'data' => new StudentResource($linkedStudent ?? $student->load(['school', 'shift', 'nutritionProfile'])),
        ], Response::HTTP_OK);
    }

    /**
     * Update child's dietary preferences and food allergies.
     */
    public function updateNutritionProfile(UpdateNutritionProfileRequest $request, Student $student): JsonResponse
    {
        $validated = $request->validated();

        $profile = StudentNutritionProfile::updateOrCreate(
            ['student_id' => $student->id],
            [
                'dietary_preferences' => $validated['dietary_preferences'] ?? [],
                'allergen_ids' => $validated['allergen_ids'] ?? [],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Student nutrition profile updated successfully.',
            'data' => new StudentNutritionProfileResource($profile),
        ], Response::HTTP_OK);
    }
}
