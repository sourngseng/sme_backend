<?php

namespace App\Policies;

use App\Models\SchoolShift;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolShiftPolicy
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

    public function view(User $user, SchoolShift $shift): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $shift->school_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('school_admin') || $user->hasPermission('schools.shifts.manage');
    }

    public function update(User $user, SchoolShift $shift): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $shift->school_id && $user->hasPermission('schools.shifts.manage');
        }

        return $user->hasPermission('schools.shifts.manage');
    }

    public function delete(User $user, SchoolShift $shift): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $shift->school_id && $user->hasPermission('schools.shifts.manage');
        }

        return $user->hasPermission('schools.shifts.manage');
    }
}
