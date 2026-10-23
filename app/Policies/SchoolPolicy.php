<?php

namespace App\Policies;

use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
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
     * Determine whether the user can view any schools.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('schools.view')
            || $user->hasRole(['school_admin', 'parent', 'support', 'finance']);
    }

    /**
     * Determine whether the user can view the specific school.
     */
    public function view(User $user, School $school): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $school->id;
        }

        return $user->hasPermission('schools.view') || $user->hasRole('parent');
    }

    /**
     * Determine whether the user can create schools.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('schools.create');
    }

    /**
     * Determine whether the user can update the school.
     */
    public function update(User $user, School $school): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $school->id && $user->hasPermission('schools.update');
        }

        return $user->hasPermission('schools.update');
    }

    /**
     * Determine whether the user can delete the school.
     */
    public function delete(User $user, School $school): bool
    {
        return $user->hasPermission('schools.delete');
    }

    /**
     * Determine whether the user can manage shifts for this school.
     */
    public function manageShifts(User $user, School $school): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $school->id && $user->hasPermission('schools.shifts.manage');
        }

        return $user->hasPermission('schools.shifts.manage');
    }

    /**
     * Determine whether the user can manage locations for this school.
     */
    public function manageLocations(User $user, School $school): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $school->id && $user->hasPermission('schools.locations.manage');
        }

        return $user->hasPermission('schools.locations.manage');
    }

    /**
     * Determine whether the user can manage meal policies for this school.
     */
    public function manageMealPolicy(User $user, School $school): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $school->id && $user->hasPermission('schools.policies.manage');
        }

        return $user->hasPermission('schools.policies.manage');
    }
}
