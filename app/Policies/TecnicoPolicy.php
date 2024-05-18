<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Tecnico;
use App\Models\User;

class TecnicoPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->checkPermissionTo('view-any Tecnico');
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tecnico $tecnico): bool
    {
        return $user->checkPermissionTo('view Tecnico');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Tecnico');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tecnico $tecnico): bool
    {
        return $user->checkPermissionTo('update Tecnico');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tecnico $tecnico): bool
    {
        return $user->checkPermissionTo('delete Tecnico');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Tecnico $tecnico): bool
    {
        return $user->checkPermissionTo('restore Tecnico');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Tecnico $tecnico): bool
    {
        return $user->checkPermissionTo('force-delete Tecnico');
    }
}
