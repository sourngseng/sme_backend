<?php

namespace App\Services;

use App\Models\NutritionConsumptionLog;
use App\Models\OrderSchedule;
use App\Models\Student;
use Carbon\Carbon;
use InvalidArgumentException;

class NutritionService
{
    /**
     * Log student meal consumption with auto-calculated caloric and protein intake.
     *
     * @param OrderSchedule $schedule
     * @param string $status ('consumed', 'partial', 'not_consumed', 'unknown')
     * @param float|null $customPct
     * @param string|null $notes
     * @return NutritionConsumptionLog
     */
    public function logConsumption(
        OrderSchedule $schedule,
        string $status,
        ?float $customPct = null,
        ?string $notes = null
    ): NutritionConsumptionLog {
        $validStatuses = ['consumed', 'partial', 'not_consumed', 'unknown'];
        if (!in_array($status, $validStatuses, true)) {
            throw new InvalidArgumentException("Invalid consumption status: {$status}");
        }

        // Determine estimated consumed percentage
        $pct = match ($status) {
            'consumed' => 100.00,
            'not_consumed' => 0.00,
            'partial' => $customPct !== null ? max(1.0, min(99.0, (float) $customPct)) : 50.00,
            'unknown' => null,
        };

        $orderItem = $schedule->orderItem;
        $order = $schedule->order;
        $studentId = $order->student_id;

        // Retrieve calories snapshot or fallback to menu version nutrition fact
        $caloriesTotal = 0.0;
        if ($orderItem && $orderItem->calories_kcal_snapshot !== null) {
            $caloriesTotal = (float) $orderItem->calories_kcal_snapshot;
        } elseif ($orderItem && $orderItem->menuVersion && $orderItem->menuVersion->nutritionFact) {
            $caloriesTotal = (float) $orderItem->menuVersion->nutritionFact->calories_kcal;
        }

        // Retrieve protein snapshot or fallback to menu version nutrition fact
        $proteinTotal = 0.0;
        if ($orderItem && !empty($orderItem->nutrition_snapshot) && isset($orderItem->nutrition_snapshot['protein_g'])) {
            $proteinTotal = (float) $orderItem->nutrition_snapshot['protein_g'];
        } elseif ($orderItem && $orderItem->menuVersion && $orderItem->menuVersion->nutritionFact) {
            $proteinTotal = (float) $orderItem->menuVersion->nutritionFact->protein_g;
        }

        $caloriesConsumed = $pct !== null ? round(($caloriesTotal * $pct) / 100, 2) : null;
        $proteinConsumed = $pct !== null ? round(($proteinTotal * $pct) / 100, 2) : null;

        return NutritionConsumptionLog::create([
            'order_schedule_id' => $schedule->id,
            'student_id' => $studentId,
            'consumption_status' => $status,
            'estimated_consumed_pct' => $pct,
            'calories_consumed_kcal' => $caloriesConsumed,
            'protein_consumed_g' => $proteinConsumed,
            'notes' => $notes,
            'recorded_at' => now(),
        ]);
    }

    /**
     * Aggregate actual consumed energy and macro intake for a student over time.
     *
     * @param Student $student
     * @param string|null $fromDate (Y-m-d)
     * @param string|null $toDate (Y-m-d)
     * @return array<string, mixed>
     */
    public function getStudentNutritionSummary(
        Student $student,
        ?string $fromDate = null,
        ?string $toDate = null
    ): array {
        $query = NutritionConsumptionLog::where('student_id', $student->id);

        if ($fromDate) {
            $query->whereDate('recorded_at', '>=', Carbon::parse($fromDate)->toDateString());
        }
        if ($toDate) {
            $query->whereDate('recorded_at', '<=', Carbon::parse($toDate)->toDateString());
        }

        $logs = $query->orderBy('recorded_at', 'asc')->get();

        $totalLoggedMeals = $logs->count();
        $consumedCount = 0;
        $partialCount = 0;
        $notConsumedCount = 0;
        $totalCalories = 0.0;
        $totalProtein = 0.0;
        $pctSum = 0.0;
        $validPctCount = 0;

        $dailyBreakdown = [];

        foreach ($logs as $log) {
            if ($log->consumption_status === 'consumed') {
                $consumedCount++;
            } elseif ($log->consumption_status === 'partial') {
                $partialCount++;
            } elseif ($log->consumption_status === 'not_consumed') {
                $notConsumedCount++;
            }

            if ($log->calories_consumed_kcal !== null) {
                $totalCalories += (float) $log->calories_consumed_kcal;
            }
            if ($log->protein_consumed_g !== null) {
                $totalProtein += (float) $log->protein_consumed_g;
            }
            if ($log->estimated_consumed_pct !== null) {
                $pctSum += (float) $log->estimated_consumed_pct;
                $validPctCount++;
            }

            $dateKey = Carbon::parse($log->recorded_at)->toDateString();
            if (!isset($dailyBreakdown[$dateKey])) {
                $dailyBreakdown[$dateKey] = [
                    'date' => $dateKey,
                    'calories_kcal' => 0.0,
                    'protein_g' => 0.0,
                    'meals_count' => 0,
                ];
            }
            $dailyBreakdown[$dateKey]['calories_kcal'] += (float) ($log->calories_consumed_kcal ?? 0);
            $dailyBreakdown[$dateKey]['protein_g'] += (float) ($log->protein_consumed_g ?? 0);
            $dailyBreakdown[$dateKey]['meals_count'] += 1;
        }

        return [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'totals' => [
                'logged_meals' => $totalLoggedMeals,
                'fully_consumed' => $consumedCount,
                'partially_consumed' => $partialCount,
                'not_consumed' => $notConsumedCount,
                'total_calories_kcal' => round($totalCalories, 2),
                'total_protein_g' => round($totalProtein, 2),
                'average_consumption_pct' => $validPctCount > 0 ? round($pctSum / $validPctCount, 2) : 0.0,
            ],
            'daily_breakdown' => array_values($dailyBreakdown),
        ];
    }
}
