<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ruta;
use Illuminate\Auth\Access\HandlesAuthorization;

class RutaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any rutas.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_ruta');
    }

    /**
     * Determine whether the user can view the ruta.
     */
    public function view(User $user, Ruta $ruta): bool
    {
        return $user->can('view_ruta');
    }

    /**
     * Determine whether the user can create rutas.
     */
    public function create(User $user): bool
    {
        return $user->can('create_ruta');
    }

    /**
     * Determine whether the user can update the ruta.
     */
    public function update(User $user, Ruta $ruta): bool
    {
        return $user->can('update_ruta');
    }

    /**
     * Determine whether the user can delete the ruta.
     */
    public function delete(User $user, Ruta $ruta): bool
    {
        return $user->can('delete_ruta');
    }

    /**
     * Determine whether the user can delete multiple rutas.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_ruta');
    }

    /**
     * Determine whether the user can restore the ruta.
     */
    public function restore(User $user, Ruta $ruta): bool
    {
        return $user->can('restore_ruta');
    }

    /**
     * Determine whether the user can replicate the ruta.
     */
    public function replicate(User $user, Ruta $ruta): bool
    {
        return $user->can('replicate_ruta');
    }

    /**
     * Determine whether the user can force delete the ruta.
     */
    public function forceDelete(User $user, Ruta $ruta): bool
    {
        return $user->can('force_delete_ruta');
    }
}
