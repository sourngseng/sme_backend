<?php

namespace App\Http\Controllers\Api\V1\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\AddRestaurantStaffRequest;
use App\Http\Resources\RestaurantStaffResource;
use App\Models\Restaurant;
use App\Models\RestaurantStaff;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestaurantStaffController extends Controller
{
    /**
     * List staff for a restaurant.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $restaurantId = $request->query('restaurant_id');

        if (! $restaurantId) {
            $restaurant = $user->ownedRestaurants()->first();
            $restaurantId = $restaurant?->id;
        }

        if (! $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant ID is required or user does not own any restaurant.',
                'error_code' => 'RESTAURANT_REQUIRED',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorize('manageStaff', $restaurant);

        $staff = $restaurant->staff()->with('user')->get();

        return response()->json([
            'success' => true,
            'data' => RestaurantStaffResource::collection($staff),
        ], Response::HTTP_OK);
    }

    /**
     * Add or invite a staff member to the restaurant.
     */
    public function store(AddRestaurantStaffRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        $restaurantId = $validated['restaurant_id'] ?? null;
        if (! $restaurantId) {
            $restaurant = $user->ownedRestaurants()->first();
            $restaurantId = $restaurant?->id;
        }

        if (! $restaurantId) {
            return response()->json([
                'success' => false,
                'message' => 'Target restaurant ID is required.',
                'error_code' => 'RESTAURANT_REQUIRED',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorize('manageStaff', $restaurant);

        // Resolve target user
        $targetUserId = $validated['user_id'] ?? null;
        if (! $targetUserId && ! empty($validated['email'])) {
            $targetUser = User::where('email', $validated['email'])->firstOrFail();
            $targetUserId = $targetUser->id;
        }

        // Attach staff record
        $staff = RestaurantStaff::updateOrCreate(
            [
                'restaurant_id' => $restaurant->id,
                'user_id' => $targetUserId,
            ],
            [
                'job_title' => $validated['job_title'],
                'status' => 'active',
                'created_at' => now(),
            ]
        );

        $staff->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Restaurant staff member added successfully.',
            'data' => new RestaurantStaffResource($staff),
        ], Response::HTTP_CREATED);
    }

    /**
     * Remove or deactivate a staff member.
     */
    public function destroy(RestaurantStaff $staff): JsonResponse
    {
        $this->authorize('manageStaff', $staff->restaurant);

        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member removed successfully.',
        ], Response::HTTP_OK);
    }
}
