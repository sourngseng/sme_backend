<?php

namespace App\Services;

use App\Models\DeliveryBatch;
use App\Models\MenuItem;
use App\Models\MenuVersion;
use App\Models\Order;
use App\Models\OrderSchedule;
use App\Models\Payment;
use App\Models\ReportSnapshot;
use App\Models\School;
use Carbon\Carbon;

class ReportingService
{
    /**
     * Get Operations Dashboard KPI metrics for today (or specified date).
     *
     * @param string|null $date (Y-m-d)
     * @return array<string, mixed>
     */
    public function getOperationsKpi(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();
        $startOfDay = Carbon::parse($targetDate)->startOfDay();
        $endOfDay = Carbon::parse($targetDate)->endOfDay();

        // 1. Orders today
        $ordersQuery = Order::whereBetween('placed_at', [$startOfDay, $endOfDay]);
        $totalOrders = $ordersQuery->count();
        $totalVolumeMinor = (int) $ordersQuery->sum('total_minor');
        $ordersByStatus = Order::whereBetween('placed_at', [$startOfDay, $endOfDay])
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        // 2. Kitchen Queue Progress
        $schedulesQuery = OrderSchedule::whereDate('delivery_date', $targetDate);
        $totalSchedules = $schedulesQuery->count();
        $schedulesByStatus = OrderSchedule::whereDate('delivery_date', $targetDate)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        // 3. Delivery Batches Completion Rate
        $batchesQuery = DeliveryBatch::whereDate('delivery_date', $targetDate);
        $totalBatches = $batchesQuery->count();
        $completedBatches = DeliveryBatch::whereDate('delivery_date', $targetDate)
            ->where('status', 'completed')
            ->count();
        $batchCompletionRate = $totalBatches > 0 ? round(($completedBatches / $totalBatches) * 100, 2) : 100.00;

        // 4. Payment Success Rate
        $paymentsQuery = Payment::whereBetween('created_at', [$startOfDay, $endOfDay]);
        $totalPayments = $paymentsQuery->count();
        $successfulPayments = Payment::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->where('status', 'successful')
            ->count();
        $paymentSuccessRate = $totalPayments > 0 ? round(($successfulPayments / $totalPayments) * 100, 2) : 100.00;

        return [
            'date' => $targetDate,
            'orders' => [
                'total_count' => $totalOrders,
                'total_volume_minor' => $totalVolumeMinor,
                'by_status' => $ordersByStatus,
            ],
            'kitchen_queue' => [
                'total_meals' => $totalSchedules,
                'pending' => $schedulesByStatus['pending'] ?? 0,
                'preparing' => $schedulesByStatus['preparing'] ?? 0,
                'ready' => $schedulesByStatus['ready'] ?? 0,
                'dispatched' => $schedulesByStatus['dispatched'] ?? 0,
                'delivered' => $schedulesByStatus['delivered'] ?? 0,
                'cancelled' => $schedulesByStatus['cancelled'] ?? 0,
            ],
            'deliveries' => [
                'total_batches' => $totalBatches,
                'completed_batches' => $completedBatches,
                'completion_rate_pct' => $batchCompletionRate,
            ],
            'payments' => [
                'total_attempts' => $totalPayments,
                'successful_count' => $successfulPayments,
                'success_rate_pct' => $paymentSuccessRate,
            ],
        ];
    }

    /**
     * Generate Order Report filtered by student, school, shift, date range, etc.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function getOrderReport(array $filters): array
    {
        $query = OrderSchedule::with(['order.student', 'order.school', 'order.restaurant', 'orderItem']);

        if (!empty($filters['school_id'])) {
            $query->whereHas('order', fn ($q) => $q->where('school_id', $filters['school_id']));
        }
        if (!empty($filters['student_id'])) {
            $query->whereHas('order', fn ($q) => $q->where('student_id', $filters['student_id']));
        }
        if (!empty($filters['restaurant_id'])) {
            $query->whereHas('order', fn ($q) => $q->where('restaurant_id', $filters['restaurant_id']));
        }
        if (!empty($filters['shift'])) {
            $query->where('shift', $filters['shift']);
        }
        if (!empty($filters['from_date'])) {
            $query->whereDate('delivery_date', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('delivery_date', '<=', $filters['to_date']);
        }

        $schedules = $query->orderBy('delivery_date', 'asc')->get();

        $rows = [];
        $totalMinor = 0;

        foreach ($schedules as $s) {
            $priceMinor = (int) ($s->orderItem->line_total_minor ?? 0);
            $totalMinor += $priceMinor;

            $rows[] = [
                'schedule_id' => $s->id,
                'order_number' => $s->order->order_number ?? 'N/A',
                'delivery_date' => Carbon::parse($s->delivery_date)->toDateString(),
                'shift' => $s->shift,
                'student_name' => $s->order->student->full_name ?? 'N/A',
                'school_name' => $s->order->school->name ?? 'N/A',
                'restaurant_name' => $s->order->restaurant->name ?? 'N/A',
                'item_name' => $s->orderItem->item_name_snapshot ?? 'N/A',
                'status' => $s->status,
                'amount_minor' => $priceMinor,
            ];
        }

        return [
            'filters' => $filters,
            'summary' => [
                'total_records' => count($rows),
                'total_amount_minor' => $totalMinor,
            ],
            'records' => $rows,
        ];
    }

    /**
     * School Daily Delivery Reconciliation Report.
     */
    public function getSchoolDeliveryReconciliation(string $schoolId, string $date, ?string $shift = null): array
    {
        $school = School::findOrFail($schoolId);

        $query = OrderSchedule::with(['order.student', 'orderItem', 'location'])
            ->whereHas('order', fn ($q) => $q->where('school_id', $school->id))
            ->whereDate('delivery_date', $date);

        if ($shift) {
            $query->where('shift', $shift);
        }

        $schedules = $query->get();

        $totalExpected = $schedules->count();
        $deliveredCount = $schedules->where('status', 'delivered')->count();
        $pendingCount = $schedules->whereIn('status', ['pending', 'preparing', 'ready', 'dispatched'])->count();
        $exceptionCount = $schedules->whereIn('status', ['cancelled', 'failed'])->count();

        $completionRate = $totalExpected > 0 ? round(($deliveredCount / $totalExpected) * 100, 2) : 100.00;

        $items = $schedules->map(fn ($s) => [
            'schedule_id' => $s->id,
            'student_name' => $s->order->student->full_name ?? 'N/A',
            'student_code' => $s->order->student->student_code ?? 'N/A',
            'grade_level' => $s->order->student->grade_level ?? 'N/A',
            'location' => $s->location->name ?? 'Default',
            'item_name' => $s->orderItem->item_name_snapshot ?? 'N/A',
            'status' => $s->status,
            'delivered_at' => $s->delivered_at ? Carbon::parse($s->delivered_at)->toIso8601String() : null,
        ])->all();

        return [
            'school_id' => $school->id,
            'school_name' => $school->name,
            'date' => $date,
            'shift' => $shift ?? 'all',
            'summary' => [
                'total_expected' => $totalExpected,
                'delivered' => $deliveredCount,
                'pending' => $pendingCount,
                'exceptions' => $exceptionCount,
                'completion_rate_pct' => $completionRate,
            ],
            'items' => $items,
        ];
    }

    /**
     * Restaurant Production Summary Report.
     */
    public function getRestaurantProductionSummary(string $restaurantId, string $date): array
    {
        $schedules = OrderSchedule::with(['orderItem'])
            ->whereHas('order', fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->whereDate('delivery_date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->get();

        $productionMap = [];
        $totalItems = 0;

        foreach ($schedules as $s) {
            $itemName = $s->orderItem->item_name_snapshot ?? 'Unknown Item';
            $versionId = $s->orderItem->menu_version_id ?? 'unknown';

            $key = $versionId . '-' . $itemName;
            if (!isset($productionMap[$key])) {
                $productionMap[$key] = [
                    'item_name' => $itemName,
                    'menu_version_id' => $versionId,
                    'quantity' => 0,
                    'shift_breakdown' => [
                        'morning' => 0,
                        'afternoon' => 0,
                    ],
                ];
            }

            $productionMap[$key]['quantity'] += (int) ($s->orderItem->quantity ?? 1);
            $shift = strtolower($s->shift);
            if (isset($productionMap[$key]['shift_breakdown'][$shift])) {
                $productionMap[$key]['shift_breakdown'][$shift] += (int) ($s->orderItem->quantity ?? 1);
            }
            $totalItems += (int) ($s->orderItem->quantity ?? 1);
        }

        return [
            'restaurant_id' => $restaurantId,
            'date' => $date,
            'total_production_count' => $totalItems,
            'items' => array_values($productionMap),
        ];
    }

    /**
     * Nutrition Coverage Report: Flags active menu versions missing verified nutrition facts.
     */
    public function getNutritionCoverageReport(): array
    {
        $activeVersions = MenuVersion::with(['menuItem.restaurant', 'nutritionFact'])
            ->where('status', 'active')
            ->get();

        $totalActive = $activeVersions->count();
        $verifiedCount = 0;
        $unverifiedCount = 0;
        $missingFactCount = 0;

        $records = [];

        foreach ($activeVersions as $v) {
            $hasFact = $v->nutritionFact !== null;
            $isVerified = $hasFact && $v->nutritionFact->verified_at !== null;

            if ($isVerified) {
                $verifiedCount++;
            } else {
                $unverifiedCount++;
                if (!$hasFact) {
                    $missingFactCount++;
                }
            }

            $records[] = [
                'menu_version_id' => $v->id,
                'version_no' => $v->version_no,
                'menu_item_id' => $v->menu_item_id,
                'item_name' => $v->menuItem->name ?? 'N/A',
                'restaurant_name' => $v->menuItem->restaurant->name ?? 'N/A',
                'has_nutrition_facts' => $hasFact,
                'is_verified' => $isVerified,
                'verified_at' => $v->nutritionFact?->verified_at ? Carbon::parse($v->nutritionFact->verified_at)->toIso8601String() : null,
                'calories_kcal' => $v->nutritionFact?->calories_kcal ? (float) $v->nutritionFact->calories_kcal : null,
                'protein_g' => $v->nutritionFact?->protein_g ? (float) $v->nutritionFact->protein_g : null,
            ];
        }

        $coverageRate = $totalActive > 0 ? round(($verifiedCount / $totalActive) * 100, 2) : 100.00;

        return [
            'summary' => [
                'total_active_menu_versions' => $totalActive,
                'verified_count' => $verifiedCount,
                'unverified_or_missing_count' => $unverifiedCount,
                'missing_fact_table_count' => $missingFactCount,
                'coverage_rate_pct' => $coverageRate,
            ],
            'records' => $records,
        ];
    }

    /**
     * Cache report payload into report_snapshots table.
     */
    public function storeSnapshot(
        string $reportType,
        string $periodStart,
        string $periodEnd,
        array $filters,
        array $data,
        ?string $userId = null
    ): ReportSnapshot {
        return ReportSnapshot::create([
            'report_type' => $reportType,
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'filters' => $filters,
            'data' => $data,
            'generated_by' => $userId,
            'generated_at' => now(),
        ]);
    }

    /**
     * Convert an associative array or list of objects into CSV format.
     *
     * @param array<int, string> $headers
     * @param array<int, array<string, mixed>> $rows
     * @return string
     */
    public function exportToCsv(array $headers, array $rows): string
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $header) {
                $val = $row[$header] ?? '';
                if (is_array($val)) {
                    $val = json_encode($val);
                }
                $line[] = (string) $val;
            }
            fputcsv($output, $line);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv ?: '';
    }
}
