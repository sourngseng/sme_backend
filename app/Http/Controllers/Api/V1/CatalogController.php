<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\BrowseCatalogRequest;
use App\Http\Resources\AllergenResource;
use App\Http\Resources\CatalogMealResource;
use App\Models\Allergen;
use App\Models\MenuItem;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CatalogController extends Controller
{
    /**
     * Browse meal catalog with active filtering and student allergen conflict detection.
     */
    public function meals(BrowseCatalogRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = MenuItem::query()
            ->where('status', 'active')
            // Active restaurant constraint (hides items if restaurant is suspended or pending)
            ->whereHas('restaurant', function ($q) {
                $q->where('status', 'active');
            })
            // Only items with an active version
            ->whereHas('versions', function ($q) use ($validated) {
                $q->where('status', 'active');

                if (! empty($validated['delivery_date'])) {
                    $date = Carbon::parse($validated['delivery_date'])->startOfDay();
                    $q->where(function ($sub) use ($date) {
                        $sub->whereNull('available_from')->orWhere('available_from', '<=', $date);
                    })->where(function ($sub) use ($date) {
                        $sub->whereNull('available_to')->orWhere('available_to', '>=', $date);
                    });
                }
            })
            ->with([
                'restaurant',
                'latestVersion' => function ($q) {
                    $q->where('status', 'active')->with(['nutritionFact', 'allergens']);
                },
            ]);

        if (! empty($validated['meal_type'])) {
            $query->where('meal_type', $validated['meal_type']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
            $query->where(function ($q) use ($search, $like) {
                $q->where('name', $like, "%{$search}%")
                    ->orWhere('description', $like, "%{$search}%");
            });
        }

        $perPage = (int) ($validated['per_page'] ?? 20);
        $items = $query->paginate($perPage);

        // Student allergen conflict screening
        $studentAllergenIds = [];
        if (! empty($validated['student_id'])) {
            $student = Student::with('nutritionProfile')->find($validated['student_id']);
            if ($student && $student->nutritionProfile) {
                $studentAllergenIds = $student->nutritionProfile->allergen_ids ?? [];
            }
        }

        $transformed = $items->getCollection()->map(function ($item) use ($studentAllergenIds) {
            return new CatalogMealResource($item, $studentAllergenIds);
        });

        return response()->json([
            'success' => true,
            'data' => $transformed,
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total(),
                'student_allergen_screening_applied' => ! empty($studentAllergenIds),
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Retrieve the master list of registered allergens.
     */
    public function allergens(): JsonResponse
    {
        $allergens = Allergen::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => AllergenResource::collection($allergens),
        ], Response::HTTP_OK);
    }
}
