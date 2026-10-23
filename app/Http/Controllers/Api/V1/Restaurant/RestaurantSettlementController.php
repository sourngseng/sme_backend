<?php

namespace App\Http\Controllers\Api\V1\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantStaff;
use App\Models\Settlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantSettlementController extends Controller
{
    /**
     * Resolve the active restaurant ID for the authenticated user.
     */
    protected function resolveRestaurantId(Request $request): ?string
    {
        $user = $request->user();

        // 1. Direct owner
        $owned = Restaurant::where('owner_user_id', $user->id)->first();
        if ($owned) {
            return $owned->id;
        }

        // 2. Staff association
        $staff = RestaurantStaff::where('user_id', $user->id)->where('status', 'active')->first();
        if ($staff) {
            return $staff->restaurant_id;
        }

        return null;
    }

    /**
     * List settlements for the current merchant.
     */
    public function index(Request $request): JsonResponse
    {
        $restaurantId = $this->resolveRestaurantId($request);
        if (!$restaurantId) {
            return response()->json(['message' => 'No restaurant associated with this user.'], 403);
        }

        $query = Settlement::where('restaurant_id', $restaurantId)
            ->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $settlements = $query->orderBy('period_end', 'desc')->paginate($request->integer('per_page', 20));

        return response()->json($settlements);
    }

    /**
     * Show settlement details for the current merchant.
     */
    public function show(Request $request, Settlement $settlement): JsonResponse
    {
        $restaurantId = $this->resolveRestaurantId($request);
        if ($settlement->restaurant_id !== $restaurantId && !$request->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Unauthorized access to this settlement.'], 403);
        }

        $settlement->load(['items.order.student']);

        return response()->json([
            'settlement' => $settlement,
        ]);
    }
}
