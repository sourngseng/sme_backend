<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
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
     * Determine whether the user can view any students.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('students.view')
            || $user->hasRole(['school_admin', 'parent', 'support']);
    }

    /**
     * Determine whether the user can view the student.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $student->school_id;
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $student->guardians()
                ->where('user_id', $user->id)
                ->exists();
        }

        return $user->hasPermission('students.view');
    }

    /**
     * Determine whether the user can create students.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->hasPermission('students.create');
        }

        return $user->hasRole('parent') || $user->hasPermission('students.create');
    }

    /**
     * Determine whether the user can update the student.
     */
    public function update(User $user, Student $student): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $student->school_id && $user->hasPermission('students.update');
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $student->guardians()
                ->where('user_id', $user->id)
                ->wherePivot('is_primary', true)
                ->exists();
        }

        return $user->hasPermission('students.update');
    }

    /**
     * Determine whether the user can delete the student.
     */
    public function delete(User $user, Student $student): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $student->school_id && $user->hasPermission('students.delete');
        }

        return $user->hasPermission('students.delete');
    }

    /**
     * Determine whether the guardian can place orders for this student.
     */
    public function order(User $user, Student $student): bool
    {
        return $student->guardians()
            ->where('user_id', $user->id)
            ->wherePivot('can_order', true)
            ->exists();
    }

    /**
     * Determine whether the user can view the student's nutrition profile.
     */
    public function viewNutrition(User $user, Student $student): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $student->school_id;
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $student->guardians()
                ->where('user_id', $user->id)
                ->wherePivot('can_view_nutrition', true)
                ->exists();
        }

        return $user->hasPermission('nutrition_profiles.view');
    }

    /**
     * Determine whether the user can manage the student's nutrition profile.
     */
    public function updateNutrition(User $user, Student $student): bool
    {
        if ($user->hasRole('school_admin')) {
            return $user->school_id === $student->school_id && $user->hasPermission('nutrition_profiles.update');
        }

        if ($user->hasRole('parent') || $user->guardian) {
            return $student->guardians()
                ->where('user_id', $user->id)
                ->wherePivot('can_view_nutrition', true)
                ->exists();
        }

        return $user->hasPermission('nutrition_profiles.update');
    }
}
