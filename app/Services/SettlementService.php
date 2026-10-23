<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Settlement;
use App\Models\SettlementItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SettlementService
{
    /**
     * Generate a merchant settlement for a given restaurant and date period.
     *
     * @param string $restaurantId
     * @param string $periodStart (Y-m-d)
     * @param string $periodEnd (Y-m-d)
     * @param int $adjustmentMinor
     * @return Settlement
     * @throws InvalidArgumentException
     */
    public function generateSettlement(
        string $restaurantId,
        string $periodStart,
        string $periodEnd,
        int $adjustmentMinor = 0
    ): Settlement {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $startDate = Carbon::parse($periodStart)->startOfDay();
        $endDate = Carbon::parse($periodEnd)->endOfDay();

        if ($startDate->isAfter($endDate)) {
            throw new InvalidArgumentException('Period start date must be before or equal to period end date.');
        }

        return DB::transaction(function () use ($restaurant, $startDate, $endDate, $periodStart, $periodEnd, $adjustmentMinor) {
            // Find orders placed for this restaurant within the period that are confirmed or completed
            // and have not been previously settled in an active settlement
            $settledOrderIds = SettlementItem::query()
                ->whereHas('settlement', function ($q) {
                    $q->whereIn('status', ['draft', 'approved', 'paid']);
                })
                ->pluck('order_id');

            $orders = Order::query()
                ->where('restaurant_id', $restaurant->id)
                ->whereIn('status', ['confirmed', 'processing', 'completed'])
                ->whereBetween('placed_at', [$startDate, $endDate])
                ->whereNotIn('id', $settledOrderIds)
                ->get();

            $grossSalesMinor = 0;
            $commissionMinor = 0;
            $refundMinor = 0;

            foreach ($orders as $order) {
                // Must use snapshotted order totals and snapshotted commission_minor (PB-058)
                $grossSalesMinor += (int) $order->total_minor;
                $commissionMinor += (int) $order->commission_minor;
            }

            $netPayableMinor = max(0, $grossSalesMinor - $commissionMinor - $refundMinor + $adjustmentMinor);

            $settlement = Settlement::create([
                'restaurant_id' => $restaurant->id,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'gross_sales_minor' => $grossSalesMinor,
                'commission_minor' => $commissionMinor,
                'refund_minor' => $refundMinor,
                'adjustment_minor' => $adjustmentMinor,
                'net_payable_minor' => $netPayableMinor,
                'status' => 'draft',
            ]);

            foreach ($orders as $order) {
                $gross = (int) $order->total_minor;
                $comm = (int) $order->commission_minor;
                $net = max(0, $gross - $comm);

                SettlementItem::create([
                    'settlement_id' => $settlement->id,
                    'order_id' => $order->id,
                    'gross_minor' => $gross,
                    'commission_minor' => $comm,
                    'net_minor' => $net,
                ]);
            }

            return $settlement->load(['restaurant', 'items.order']);
        });
    }

    /**
     * Transition a settlement's status.
     */
    public function updateStatus(Settlement $settlement, string $newStatus): Settlement
    {
        $validStatuses = ['draft', 'approved', 'paid', 'disputed'];
        if (!in_array($newStatus, $validStatuses, true)) {
            throw new InvalidArgumentException("Invalid settlement status: {$newStatus}");
        }

        $attributes = ['status' => $newStatus];
        if ($newStatus === 'paid' && !$settlement->paid_at) {
            $attributes['paid_at'] = now();
        }

        $settlement->update($attributes);

        return $settlement->fresh(['restaurant', 'items']);
    }
}
