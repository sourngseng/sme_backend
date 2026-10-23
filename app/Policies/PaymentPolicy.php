<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, Payment $payment): bool
    {
        $order = $payment->order;
        if (! $order) {
            return false;
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $order->guardian && $order->guardian->user_id === $user->id;
        }

        if ($user->hasRole(['restaurant_admin', 'restaurant_staff'])) {
            return $user->restaurant_id && $user->restaurant_id === $order->restaurant_id;
        }

        if ($user->hasRole('school_admin')) {
            return $user->school_id && $user->school_id === $order->school_id;
        }

        return $user->hasPermission('orders.view');
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }
}
