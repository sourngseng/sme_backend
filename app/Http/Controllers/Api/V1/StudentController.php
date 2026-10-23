<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SearchStudentRequest;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class StudentController extends Controller
{
    /**
     * Search students with filters and object-level security.
     */
    public function index(SearchStudentRequest $request): JsonResponse
    {
        $user = $request->user();
        $query = Student::with(['school', 'shift', 'nutritionProfile']);

        // Object-Level Security: School admins can ONLY access students in their assigned school
        if ($user->hasRole('school_admin') && ! $user->hasRole('super_admin')) {
            if (! $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'School administrator is not assigned to any school.',
                    'error_code' => 'NO_ASSIGNED_SCHOOL',
                ], Response::HTTP_FORBIDDEN);
            }

            $query->where('school_id', $user->school_id);
        } elseif ($schoolId = $request->validated('school_id')) {
            $query->where('school_id', $schoolId);
        }

        // Apply filters
        if ($fullName = $request->validated('full_name')) {
            $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where('full_name', $like, "%{$fullName}%");
        }

        if ($gradeLevel = $request->validated('grade_level')) {
            $query->where('grade_level', $gradeLevel);
        }

        if ($className = $request->validated('class_name')) {
            $query->where('class_name', $className);
        }

        if ($status = $request->validated('status')) {
            $query->where('status', $status);
        }

        $perPage = $request->integer('per_page', 15);
        $students = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => StudentResource::collection($students),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Enforce school admin assignment
        if ($user->hasRole('school_admin') && ! $user->hasRole('super_admin')) {
            $validated['school_id'] = $user->school_id;
        }

        $student = DB::transaction(function () use ($validated) {
            return Student::create($validated);
        });

        $student->load(['school', 'shift', 'nutritionProfile']);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully.',
            'data' => new StudentResource($student),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): JsonResponse
    {
        $this->authorize('view', $student);

        $student->load(['school', 'shift', 'nutritionProfile']);

        return response()->json([
            'success' => true,
            'data' => new StudentResource($student),
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // Prevent school admin from moving student to another school
        if ($user->hasRole('school_admin') && ! $user->hasRole('super_admin')) {
            unset($validated['school_id']);
        }

        $student->update($validated);
        $student->load(['school', 'shift', 'nutritionProfile']);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully.',
            'data' => new StudentResource($student),
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): JsonResponse
    {
        $this->authorize('delete', $student);

        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student record deleted successfully.',
        ], Response::HTTP_OK);
    }
}
