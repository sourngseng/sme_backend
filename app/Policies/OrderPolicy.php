<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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
     * Determine whether the user can view any orders.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('orders.view')
            || $user->hasRole(['parent', 'restaurant_staff', 'restaurant_admin', 'school_admin', 'support', 'finance']);
    }

    /**
     * Determine whether the user can view the specific order.
     */
    public function view(User $user, Order $order): bool
    {
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
     * Determine whether the user can create orders.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('orders.create')
            || $user->hasRole('parent')
            || $user->guardian !== null;
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->hasRole(['restaurant_admin', 'restaurant_staff', 'restaurant_owner']) || $user->ownedRestaurants()->exists()) {
            $restaurantId = $user->restaurant_id ?? $user->ownedRestaurants()->value('id');
            return $restaurantId && $restaurantId === $order->restaurant_id;
        }

        return $user->hasPermission('orders.update');
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        return $user->hasPermission('orders.delete');
    }
}
