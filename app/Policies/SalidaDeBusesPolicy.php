<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SalidaDeBuses;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalidaDeBusesPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any salidas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_salida');
    }

    /**
     * Determine whether the user can view a specific salida.
     */
    public function view(User $user, SalidaDeBuses $salida):bool
    {
        return $user->can('view_salida');
    }

    /**
     * Determine whether the user can create salidas.
     */
    public function create(User $user):bool
    {
        return $user->can('create_salida');
    }

    /**
     * Determine whether the user can update the salida.
     */
    public function update(User $user, SalidaDeBuses $salida): bool
    {
        return $user->can('update_salida');
    }

    /**
     * Determine whether the user can delete the salida.
     */
    public function delete(User $user, SalidaDeBuses $salida): bool
    {
        return $user->can('delete_salida');
    }

    /**
     * Determine whether the user can delete multiple salidas.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_salida');
    }

    /**
     * Determine whether the user can restore the salida.
     */
    public function restore(User $user, SalidaDeBuses $salida): bool
    {
        return $user->can('restore_salida');
    }

    /**
     * Determine whether the user can replicate the salida.
     */
    public function replicate(User $user, SalidaDeBuses $salida): bool
    {
        return $user->can('replicate_salida');
    }

    /**
     * Determine whether the user can force delete the salida.
     */
    public function forceDelete(User $user, SalidaDeBuses $salida): bool
    {
        return $user->can('force_delete_salida');
    }
}
