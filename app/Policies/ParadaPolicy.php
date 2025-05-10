<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Parada;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParadaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any paradas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_parada');
    }

    /**
     * Determine whether the user can view the parada.
     */
    public function view(User $user, Parada $parada): bool
    {
        return $user->can('view_parada');
    }

    /**
     * Determine whether the user can create paradas.
     */
    public function create(User $user): bool
    {
        return $user->can('create_parada');
    }

    /**
     * Determine whether the user can update the parada.
     */
    public function update(User $user, Parada $parada): bool
    {
        return $user->can('update_parada');
    }

    /**
     * Determine whether the user can delete the parada.
     */
    public function delete(User $user, Parada $parada): bool
    {
        return $user->can('delete_parada');
    }

    /**
     * Determine whether the user can delete multiple paradas.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_parada');
    }

    /**
     * Determine whether the user can restore the parada.
     */
    public function restore(User $user, Parada $parada): bool
    {
        return $user->can('restore_parada');
    }

    /**
     * Determine whether the user can replicate the parada.
     */
    public function replicate(User $user, Parada $parada): bool
    {
        return $user->can('replicate_parada');
    }

    /**
     * Determine whether the user can force delete the parada.
     */
    public function forceDelete(User $user, Parada $parada): bool
    {
        return $user->can('force_delete_parada');
    }
}
