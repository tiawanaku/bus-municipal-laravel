<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Conductor;
use App\Models\User;

class ConductorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->checkPermissionTo('view-any Conductor');
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Conductor $conductor): bool
    {
        return $user->checkPermissionTo('view Conductor');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //return $user->checkPermissionTo('create Conductor');
        return $user->hasRole('Administrador') || $user->checkPermissionTo('create Conductor');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Conductor $conductor): bool
    {
        //return $user->checkPermissionTo('update Conductor');
        return $user->hasRole('Administrador') || $user->checkPermissionTo('update Conductor');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Conductor $conductor): bool
    {
        //return $user->checkPermissionTo('delete Conductor');
        return $user->hasRole('Administrador') || $user->checkPermissionTo('delete Conductor');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Conductor $conductor): bool
    {
        return $user->checkPermissionTo('restore Conductor');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Conductor $conductor): bool
    {
        return $user->checkPermissionTo('force-delete Conductor');
    }
}
