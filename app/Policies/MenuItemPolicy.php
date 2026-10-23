<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuItemPolicy
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

    public function view(User $user, MenuItem $menuItem): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['restaurant_owner', 'restaurant_staff'])
            || $user->hasPermission('menu_items.create');
    }

    public function update(User $user, MenuItem $menuItem): bool
    {
        $restaurant = $menuItem->restaurant;
        if (! $restaurant) {
            return false;
        }

        if ($user->id === $restaurant->owner_user_id) {
            return true;
        }

        return $restaurant->staff()
            ->where('user_id', $user->id)
            ->whereIn('job_title', ['owner', 'manager', 'kitchen_staff'])
            ->where('status', 'active')
            ->exists();
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        $restaurant = $menuItem->restaurant;
        if (! $restaurant) {
            return false;
        }

        if ($user->id === $restaurant->owner_user_id) {
            return true;
        }

        return $restaurant->staff()
            ->where('user_id', $user->id)
            ->whereIn('job_title', ['owner', 'manager'])
            ->where('status', 'active')
            ->exists();
    }
}
