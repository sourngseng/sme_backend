<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MealFeedback;
use App\Models\OrderSchedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MealFeedbackController extends Controller
{
    /**
     * Submit meal feedback for a completed schedule.
     */
    public function store(Request $request, OrderSchedule $schedule): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'taste_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'portion_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'quality_rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $schedule->load(['order.student']);
        $user = $request->user();

        // Authorization check: guardian of student or admin
        $guardian = $user->guardian;
        $isGuardian = $guardian && $schedule->order && $schedule->order->student
            && $schedule->order->student->guardians()->where('guardians.id', $guardian->id)->exists();

        if (!$isGuardian && !$user->hasRole(['super_admin', 'admin'])) {
            return response()->json(['message' => 'Only parents/guardians can submit feedback for this meal.'], 403);
        }

        $feedback = MealFeedback::updateOrCreate(
            [
                'order_schedule_id' => $schedule->id,
                'student_id' => $schedule->order->student_id,
            ],
            [
                'guardian_id' => $guardian?->id,
                'rating' => $validated['rating'],
                'taste_rating' => $validated['taste_rating'] ?? null,
                'portion_rating' => $validated['portion_rating'] ?? null,
                'quality_rating' => $validated['quality_rating'] ?? null,
                'comment' => $validated['comment'] ?? null,
                'created_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Meal feedback submitted successfully.',
            'feedback' => $feedback,
        ], 201);
    }

    /**
     * List meal feedback with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = MealFeedback::with(['schedule.order.restaurant', 'student', 'guardian.user']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->query('student_id'));
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->query('rating'));
        }
        if ($request->filled('restaurant_id')) {
            $query->whereHas('schedule.order', fn ($q) => $q->where('restaurant_id', $request->query('restaurant_id')));
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->paginate($request->integer('per_page', 20));

        return response()->json($feedbacks);
    }
}
