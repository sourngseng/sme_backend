<?php

namespace App\Policies;

use App\Models\SchoolLocation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolLocationPolicy
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

    public function view(User $user, SchoolLocation $location): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $location->school_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('school_admin') || $user->hasPermission('schools.locations.manage');
    }

    public function update(User $user, SchoolLocation $location): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $location->school_id && $user->hasPermission('schools.locations.manage');
        }

        return $user->hasPermission('schools.locations.manage');
    }

    public function delete(User $user, SchoolLocation $location): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $location->school_id && $user->hasPermission('schools.locations.manage');
        }

        return $user->hasPermission('schools.locations.manage');
    }
}
