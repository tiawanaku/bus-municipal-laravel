<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Cajero;
use App\Models\User;

class CajeroPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->checkPermissionTo('view-any Cajero');
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Cajero $cajero): bool
    {
        return $user->checkPermissionTo('view Cajero');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Cajero');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Cajero $cajero): bool
    {
        return $user->checkPermissionTo('update Cajero');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cajero $cajero): bool
    {
        return $user->checkPermissionTo('delete Cajero');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Cajero $cajero): bool
    {
        return $user->checkPermissionTo('restore Cajero');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Cajero $cajero): bool
    {
        return $user->checkPermissionTo('force-delete Cajero');
    }
}
