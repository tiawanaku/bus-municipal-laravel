<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Asignacion;
use App\Models\User;

class AsignacionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->checkPermissionTo('view-any Asignacion');
        return $user->hasRole('Empleado');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asignacion $asignacion): bool
    {
        return $user->checkPermissionTo('view Asignacion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //return $user->checkPermissionTo('create Asignacion');
        return $user->hasRole('Empleado') || $user->checkPermissionTo('create Asignacion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asignacion $asignacion): bool
    {
        //return $user->checkPermissionTo('update Asignacion');
        return $user->hasRole('Empleado') || $user->checkPermissionTo('create Asignacion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asignacion $asignacion): bool
    {
        return $user->checkPermissionTo('delete Asignacion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Asignacion $asignacion): bool
    {
        return $user->checkPermissionTo('restore Asignacion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Asignacion $asignacion): bool
    {
        return $user->checkPermissionTo('force-delete Asignacion');
    }
}
