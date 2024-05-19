<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Bus;
use App\Models\User;

class BusPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //return $user->checkPermissionTo('view-any Bus');
        return $user->hasRole('Administrador');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bus $bus): bool
    {
        return $user->checkPermissionTo('view Bus');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //return $user->checkPermissionTo('create Bus');
        return $user->hasRole('Administrador') || $user->checkPermissionTo('create Bus');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Bus $bus): bool
    {
        //return $user->checkPermissionTo('update Bus');
        return $user->hasRole('Administrador') || $user->checkPermissionTo('update Bus');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bus $bus): bool
    {
        //return $user->checkPermissionTo('delete Bus');
        return $user->hasRole('Administrador') || $user->checkPermissionTo('delete Bus');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bus $bus): bool
    {
        return $user->checkPermissionTo('restore Bus');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bus $bus): bool
    {
        return $user->checkPermissionTo('force-delete Bus');
    }
}
