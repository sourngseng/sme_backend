<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\UpdateRestaurantStatusRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class RestaurantManagementController extends Controller
{
    /**
     * List all merchant restaurants with status filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Restaurant::with(['owner', 'staff.user'])->withCount('menuItems');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('name', $like, "%{$search}%")
                    ->orWhere('code', $like, "%{$search}%")
                    ->orWhere('email', $like, "%{$search}%");
            });
        }

        $perPage = (int) $request->query('per_page', 15);
        $restaurants = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => RestaurantResource::collection($restaurants),
            'meta' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Change merchant status (approve, suspend, reactivate).
     *
     * Suspending a restaurant immediately prevents its menu items from appearing in parent catalog browsing.
     */
    public function updateStatus(UpdateRestaurantStatusRequest $request, Restaurant $restaurant): JsonResponse
    {
        $validated = $request->validated();
        $previousStatus = $restaurant->status;

        $restaurant->update([
            'status' => $validated['status'],
        ]);

        $restaurant->load(['owner', 'staff.user']);

        return response()->json([
            'success' => true,
            'message' => "Restaurant status updated from {$previousStatus} to {$restaurant->status}.",
            'data' => new RestaurantResource($restaurant),
        ], Response::HTTP_OK);
    }
}
