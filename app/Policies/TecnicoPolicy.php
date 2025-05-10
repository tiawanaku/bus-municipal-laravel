<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tecnico;
use Illuminate\Auth\Access\HandlesAuthorization;

class TecnicoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tecnicos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tecnico');
    }

    /**
     * Determine whether the user can view a specific tecnico.
     */
    public function view(User $user, Tecnico $tecnico): bool
    {
        return $user->can('view_tecnico');
    }

    /**
     * Determine whether the user can create tecnicos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tecnico');
    }

    /**
     * Determine whether the user can update the tecnico.
     */
    public function update(User $user, Tecnico $tecnico): bool
    {
        return $user->can('update_tecnico');
    }

    /**
     * Determine whether the user can delete the tecnico.
     */
    public function delete(User $user, Tecnico $tecnico): bool
    {
        return $user->can('delete_tecnico');
    }

    /**
     * Determine whether the user can delete multiple tecnicos.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tecnico');
    }

    /**
     * Determine whether the user can restore the tecnico.
     */
    public function restore(User $user, Tecnico $tecnico): bool
    {
        return $user->can('restore_tecnico');
    }

    /**
     * Determine whether the user can replicate the tecnico.
     */
    public function replicate(User $user, Tecnico $tecnico): bool
    {
        return $user->can('replicate_tecnico');
    }

    /**
     * Determine whether the user can force delete the tecnico.
     */
    public function forceDelete(User $user, Tecnico $tecnico): bool
    {
        return $user->can('force_delete_tecnico');
    }
}
