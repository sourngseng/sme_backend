<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\OrderSchedule;
use App\Models\Student;
use App\Services\NutritionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NutritionController extends Controller
{
    public function __construct(
        protected NutritionService $nutritionService
    ) {}

    /**
     * Log nutrition consumption for a delivered order schedule.
     */
    public function storeLog(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_schedule_id' => ['required', 'uuid', 'exists:order_schedules,id'],
            'consumption_status' => ['required', 'string', 'in:consumed,partial,not_consumed,unknown'],
            'estimated_consumed_pct' => ['nullable', 'numeric', 'min:1', 'max:99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $schedule = OrderSchedule::with(['order.student', 'orderItem.menuVersion.nutritionFact'])->findOrFail($validated['order_schedule_id']);
        $user = $request->user();

        // Authorization: super_admin, school_admin/staff for this school, or primary/linked guardian
        $authorized = false;
        if ($user->hasRole(['super_admin', 'admin', 'school_admin', 'school_staff', 'teacher'])) {
            $authorized = true;
        } elseif ($schedule->order && $schedule->order->student) {
            $guardian = $user->guardian;
            if ($guardian && $schedule->order->student->guardians()->where('guardians.id', $guardian->id)->exists()) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            return response()->json(['message' => 'You are not authorized to log consumption for this student.'], 403);
        }

        $log = $this->nutritionService->logConsumption(
            schedule: $schedule,
            status: $validated['consumption_status'],
            customPct: isset($validated['estimated_consumed_pct']) ? (float) $validated['estimated_consumed_pct'] : null,
            notes: $validated['notes'] ?? null
        );

        return response()->json([
            'message' => 'Nutrition consumption logged successfully.',
            'log' => $log,
        ], 201);
    }

    /**
     * Parent & school staff nutrition intake summary for a student.
     */
    public function studentSummary(Request $request, Student $student): JsonResponse
    {
        $user = $request->user();

        // Authorization check
        $authorized = false;
        if ($user->hasRole(['super_admin', 'admin', 'school_admin', 'school_staff'])) {
            $authorized = true;
        } else {
            $guardian = $user->guardian;
            if ($guardian && $student->guardians()->where('guardians.id', $guardian->id)->exists()) {
                $authorized = true;
            }
        }

        if (!$authorized) {
            return response()->json(['message' => 'Unauthorized access to student nutrition metrics.'], 403);
        }

        $from = $request->query('from');
        $to = $request->query('to');

        $summary = $this->nutritionService->getStudentNutritionSummary($student, $from, $to);

        return response()->json($summary);
    }
}
