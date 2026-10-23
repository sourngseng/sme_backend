<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RestaurantPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Restaurant $restaurant): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('restaurants.create') || $user->hasRole('restaurant_owner');
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        if ($user->id === $restaurant->owner_user_id) {
            return true;
        }

        return $restaurant->staff()
            ->where('user_id', $user->id)
            ->whereIn('job_title', ['owner', 'manager'])
            ->where('status', 'active')
            ->exists();
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $user->hasPermission('restaurants.delete');
    }

    public function updateStatus(User $user, ?Restaurant $restaurant = null): bool
    {
        return $user->hasPermission('restaurants.approve') || $user->hasPermission('restaurants.suspend');
    }

    public function manageStaff(User $user, Restaurant $restaurant): bool
    {
        if ($user->id === $restaurant->owner_user_id) {
            return true;
        }

        return $restaurant->staff()
            ->where('user_id', $user->id)
            ->whereIn('job_title', ['owner', 'manager'])
            ->where('status', 'active')
            ->exists();
    }

    public function manageMenu(User $user, Restaurant $restaurant): bool
    {
        if ($user->id === $restaurant->owner_user_id) {
            return true;
        }

        return $restaurant->staff()
            ->where('user_id', $user->id)
            ->whereIn('job_title', ['owner', 'manager', 'kitchen_staff'])
            ->where('status', 'active')
            ->exists();
    }
}
