<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Ruta;
use App\Models\User;

class RutaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->checkPermissionTo('view-any Ruta');
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ruta $ruta): bool
    {
        return $user->checkPermissionTo('view Ruta');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Ruta');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ruta $ruta): bool
    {
        return $user->checkPermissionTo('update Ruta');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ruta $ruta): bool
    {
        return $user->checkPermissionTo('delete Ruta');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ruta $ruta): bool
    {
        return $user->checkPermissionTo('restore Ruta');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ruta $ruta): bool
    {
        return $user->checkPermissionTo('force-delete Ruta');
    }
}
