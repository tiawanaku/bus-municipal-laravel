<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conductor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConductorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any conductores.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_conductor');
    }

    /**
     * Determine whether the user can view the conductor.
     */
    public function view(User $user, Conductor $conductor): bool
    {
        return $user->can('view_conductor');
    }

    /**
     * Determine whether the user can create conductores.
     */
    public function create(User $user): bool
    {
        return $user->can('create_conductor');
    }

    /**
     * Determine whether the user can update the conductor.
     */
    public function update(User $user, Conductor $conductor): bool
    {
        return $user->can('update_conductor');
    }

    /**
     * Determine whether the user can delete the conductor.
     */
    public function delete(User $user, Conductor $conductor): bool
    {
        return $user->can('delete_conductor');
    }

    /**
     * Determine whether the user can delete multiple conductores.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_conductor');
    }

    /**
     * Determine whether the user can restore the conductor.
     */
    public function restore(User $user, Conductor $conductor): bool
    {
        return $user->can('restore_conductor');
    }

    /**
     * Determine whether the user can replicate the conductor.
     */
    public function replicate(User $user, Conductor $conductor): bool
    {
        return $user->can('replicate_conductor');
    }

    /**
     * Determine whether the user can force delete the conductor.
     */
    public function forceDelete(User $user, Conductor $conductor): bool
    {
        return $user->can('force_delete_conductor');
    }
}
