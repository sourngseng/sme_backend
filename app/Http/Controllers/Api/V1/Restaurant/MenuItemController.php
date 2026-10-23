<?php

namespace App\Http\Controllers\Api\V1\Restaurant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Restaurant\StoreMenuItemRequest;
use App\Http\Requests\Restaurant\UpdateMenuItemRequest;
use App\Http\Resources\MenuItemResource;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Services\MenuVersioningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class MenuItemController extends Controller
{
    protected MenuVersioningService $versioningService;

    public function __construct(MenuVersioningService $versioningService)
    {
        $this->versioningService = $versioningService;
    }

    /**
     * List menu items for a restaurant with current versions.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $restaurantId = $request->query('restaurant_id');

        if (! $restaurantId) {
            $restaurant = $user->ownedRestaurants()->first();
            $restaurantId = $restaurant?->id;
        }

        $query = MenuItem::with(['latestVersion.nutritionFact', 'latestVersion.allergens', 'category']);

        if ($restaurantId) {
            $query->where('restaurant_id', $restaurantId);
        }

        if ($mealType = $request->query('meal_type')) {
            $query->where('meal_type', $mealType);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) $request->query('per_page', 15);
        $items = $query->latest()->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => MenuItemResource::collection($items),
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Create a new menu item and initialize its v1 immutable version.
     */
    public function store(StoreMenuItemRequest $request): JsonResponse
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
                'message' => 'Restaurant ID is required.',
                'error_code' => 'RESTAURANT_REQUIRED',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $restaurant = Restaurant::findOrFail($restaurantId);
        $this->authorize('manageMenu', $restaurant);

        $item = DB::transaction(function () use ($validated, $restaurantId) {
            $item = MenuItem::create([
                'restaurant_id' => $restaurantId,
                'category_id' => $validated['category_id'] ?? null,
                'sku' => $validated['sku'] ?? null,
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'meal_type' => $validated['meal_type'],
                'status' => $validated['status'] ?? 'active',
            ]);

            // Version 1 data
            $versionData = [
                'price_minor' => $validated['price_minor'],
                'currency' => $validated['currency'] ?? 'USD',
                'serving_size_g' => $validated['serving_size_g'] ?? null,
                'available_from' => $validated['available_from'] ?? null,
                'available_to' => $validated['available_to'] ?? null,
                'cutoff_minutes' => $validated['cutoff_minutes'] ?? 0,
                'max_daily_quantity' => $validated['max_daily_quantity'] ?? null,
                'status' => 'active',
            ];

            $this->versioningService->createInitialVersion(
                $item,
                $versionData,
                $validated['nutrition'] ?? null,
                $validated['allergens'] ?? []
            );

            return $item;
        });

        $item->load(['latestVersion.nutritionFact', 'latestVersion.allergens', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Menu item created with initial version 1.',
            'data' => new MenuItemResource($item),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified menu item with version history.
     */
    public function show(MenuItem $menuItem): JsonResponse
    {
        $this->authorize('view', $menuItem);

        $menuItem->load([
            'versions.nutritionFact',
            'versions.allergens',
            'category',
            'restaurant',
        ]);

        return response()->json([
            'success' => true,
            'data' => new MenuItemResource($menuItem),
        ], Response::HTTP_OK);
    }

    /**
     * Update menu item.
     *
     * If version-altering fields (price, serving size, nutrition, allergens) change,
     * this automatically produces an immutable new version (vN+1) leaving historical orders untouched.
     */
    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem): JsonResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($menuItem, $validated) {
            // 1. Update basic item metadata if present
            $itemUpdates = array_intersect_key($validated, array_flip([
                'category_id', 'sku', 'name', 'description', 'meal_type', 'status',
            ]));

            if (! empty($itemUpdates)) {
                $menuItem->update($itemUpdates);
            }

            // 2. Check if versioned fields changed
            if ($this->versioningService->requiresNewVersion($validated)) {
                $versionData = array_intersect_key($validated, array_flip([
                    'price_minor', 'currency', 'serving_size_g', 'available_from',
                    'available_to', 'cutoff_minutes', 'max_daily_quantity',
                ]));

                $this->versioningService->createNewVersion(
                    $menuItem,
                    $versionData,
                    $validated['nutrition'] ?? null,
                    $validated['allergens'] ?? null
                );
            }
        });

        $menuItem->load(['latestVersion.nutritionFact', 'latestVersion.allergens', 'category']);

        return response()->json([
            'success' => true,
            'message' => 'Menu item updated successfully.',
            'data' => new MenuItemResource($menuItem),
        ], Response::HTTP_OK);
    }

    /**
     * Soft-delete a menu item.
     */
    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $this->authorize('delete', $menuItem);

        $menuItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully.',
        ], Response::HTTP_OK);
    }
}
