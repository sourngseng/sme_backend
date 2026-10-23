<?php

namespace App\Policies;

use App\Models\OrderSchedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderSchedulePolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the schedule.
     */
    public function view(User $user, OrderSchedule $schedule): bool
    {
        $order = $schedule->order;
        if (! $order) {
            return false;
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $order->guardian && $order->guardian->user_id === $user->id;
        }

        if ($user->hasRole(['restaurant_admin', 'restaurant_staff', 'restaurant_owner']) || $user->ownedRestaurants()->exists()) {
            $restaurantId = $user->restaurant_id ?? $user->ownedRestaurants()->value('id');
            return $restaurantId && $restaurantId === $order->restaurant_id;
        }

        if ($user->hasRole('school_admin')) {
            return $user->school_id && $user->school_id === $order->school_id;
        }

        return $user->hasPermission('orders.view');
    }

    /**
     * Determine whether the user can skip the schedule.
     */
    public function skip(User $user, OrderSchedule $schedule): bool
    {
        $order = $schedule->order;
        if (! $order) {
            return false;
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $order->guardian && $order->guardian->user_id === $user->id;
        }

        if ($user->hasRole('school_admin')) {
            return $user->school_id && $user->school_id === $order->school_id;
        }

        return $user->hasPermission('orders.update');
    }

    /**
     * Determine whether the user can cancel the schedule.
     */
    public function cancel(User $user, OrderSchedule $schedule): bool
    {
        return $this->skip($user, $schedule);
    }
}
