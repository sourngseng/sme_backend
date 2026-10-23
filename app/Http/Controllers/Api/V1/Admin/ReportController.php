<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        protected ReportingService $reportingService
    ) {}

    /**
     * Operations Dashboard KPI endpoint.
     */
    public function operationsKpi(Request $request): JsonResponse
    {
        $kpi = $this->reportingService->getOperationsKpi($request->query('date'));

        return response()->json($kpi);
    }

    /**
     * Exportable Order Reports filtered by student/school/date/shift.
     */
    public function orders(Request $request): Response
    {
        $data = $this->reportingService->getOrderReport($request->all());

        if ($request->boolean('snapshot')) {
            $from = $request->query('from_date', now()->toDateString());
            $to = $request->query('to_date', now()->toDateString());
            $this->reportingService->storeSnapshot(
                reportType: 'orders_report',
                periodStart: $from,
                periodEnd: $to,
                filters: $request->all(),
                data: $data,
                userId: $request->user()?->id
            );
        }

        if ($request->query('export') === 'csv') {
            $headers = [
                'schedule_id', 'order_number', 'delivery_date', 'shift',
                'student_name', 'school_name', 'restaurant_name', 'item_name',
                'status', 'amount_minor',
            ];
            $csv = $this->reportingService->exportToCsv($headers, $data['records']);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="orders_report_' . now()->format('Ymd_His') . '.csv"',
            ]);
        }

        return response()->json($data);
    }

    /**
     * School Daily Delivery Reconciliation Report.
     */
    public function schoolReconciliation(Request $request): Response
    {
        $validated = $request->validate([
            'school_id' => ['required', 'uuid', 'exists:schools,id'],
            'date' => ['required', 'date'],
            'shift' => ['nullable', 'string'],
        ]);

        $data = $this->reportingService->getSchoolDeliveryReconciliation(
            schoolId: $validated['school_id'],
            date: $validated['date'],
            shift: $validated['shift'] ?? null
        );

        if ($request->boolean('snapshot')) {
            $this->reportingService->storeSnapshot(
                reportType: 'school_delivery_reconciliation',
                periodStart: $validated['date'],
                periodEnd: $validated['date'],
                filters: $validated,
                data: $data,
                userId: $request->user()?->id
            );
        }

        if ($request->query('export') === 'csv') {
            $headers = ['schedule_id', 'student_name', 'student_code', 'grade_level', 'location', 'item_name', 'status', 'delivered_at'];
            $csv = $this->reportingService->exportToCsv($headers, $data['items']);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="school_reconciliation_' . $validated['date'] . '.csv"',
            ]);
        }

        return response()->json($data);
    }

    /**
     * Restaurant Production Summary Report.
     */
    public function restaurantProduction(Request $request): Response
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'uuid', 'exists:restaurants,id'],
            'date' => ['required', 'date'],
        ]);

        $data = $this->reportingService->getRestaurantProductionSummary(
            restaurantId: $validated['restaurant_id'],
            date: $validated['date']
        );

        if ($request->boolean('snapshot')) {
            $this->reportingService->storeSnapshot(
                reportType: 'restaurant_production_summary',
                periodStart: $validated['date'],
                periodEnd: $validated['date'],
                filters: $validated,
                data: $data,
                userId: $request->user()?->id
            );
        }

        if ($request->query('export') === 'csv') {
            $headers = ['item_name', 'menu_version_id', 'quantity'];
            $csv = $this->reportingService->exportToCsv($headers, $data['items']);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="restaurant_production_' . $validated['date'] . '.csv"',
            ]);
        }

        return response()->json($data);
    }

    /**
     * Nutrition Coverage Report.
     */
    public function nutritionCoverage(Request $request): Response
    {
        $data = $this->reportingService->getNutritionCoverageReport();

        if ($request->boolean('snapshot')) {
            $this->reportingService->storeSnapshot(
                reportType: 'nutrition_coverage_report',
                periodStart: now()->toDateString(),
                periodEnd: now()->toDateString(),
                filters: [],
                data: $data,
                userId: $request->user()?->id
            );
        }

        if ($request->query('export') === 'csv') {
            $headers = ['menu_version_id', 'version_no', 'item_name', 'restaurant_name', 'has_nutrition_facts', 'is_verified', 'calories_kcal', 'protein_g'];
            $csv = $this->reportingService->exportToCsv($headers, $data['records']);

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="nutrition_coverage_' . now()->format('Ymd') . '.csv"',
            ]);
        }

        return response()->json($data);
    }
}
