<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Anfitrion;
use App\Models\User;

class AnfitrionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Anfitrion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Anfitrion $anfitrion): bool
    {
        return $user->checkPermissionTo('view Anfitrion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Anfitrion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Anfitrion $anfitrion): bool
    {
        return $user->checkPermissionTo('update Anfitrion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Anfitrion $anfitrion): bool
    {
        return $user->checkPermissionTo('delete Anfitrion');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Anfitrion $anfitrion): bool
    {
        return $user->checkPermissionTo('restore Anfitrion');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Anfitrion $anfitrion): bool
    {
        return $user->checkPermissionTo('force-delete Anfitrion');
    }
}
