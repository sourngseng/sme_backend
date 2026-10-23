<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Services\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function __construct(
        protected SettlementService $settlementService
    ) {}

    /**
     * List settlements with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Settlement::with(['restaurant'])
            ->withCount('items');

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->query('restaurant_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }
        if ($request->filled('from')) {
            $query->whereDate('period_start', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('period_end', '<=', $request->query('to'));
        }

        $settlements = $query->orderBy('period_end', 'desc')->paginate($request->integer('per_page', 20));

        return response()->json($settlements);
    }

    /**
     * Generate merchant settlement for a period.
     */
    public function generate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'uuid', 'exists:restaurants,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'adjustment_minor' => ['nullable', 'integer'],
        ]);

        $settlement = $this->settlementService->generateSettlement(
            restaurantId: $validated['restaurant_id'],
            periodStart: $validated['period_start'],
            periodEnd: $validated['period_end'],
            adjustmentMinor: (int) ($validated['adjustment_minor'] ?? 0)
        );

        return response()->json([
            'message' => 'Settlement generated successfully.',
            'settlement' => $settlement,
        ], 201);
    }

    /**
     * Show settlement details with line items.
     */
    public function show(Settlement $settlement): JsonResponse
    {
        $settlement->load(['restaurant', 'items.order.student', 'items.order.school']);

        return response()->json([
            'settlement' => $settlement,
        ]);
    }

    /**
     * Update settlement status (e.g. approve, mark paid, dispute).
     */
    public function updateStatus(Request $request, Settlement $settlement): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:draft,approved,paid,disputed'],
        ]);

        $updated = $this->settlementService->updateStatus($settlement, $validated['status']);

        return response()->json([
            'message' => "Settlement status updated to {$validated['status']}.",
            'settlement' => $updated,
        ]);
    }
}
