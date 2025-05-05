<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Anfitrion;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnfitrionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any anfitriones.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_anfitrion');
    }

    /**
     * Determine whether the user can view the anfitrion.
     */
    public function view(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('view_anfitrion');
    }

    /**
     * Determine whether the user can create anfitriones.
     */
    public function create(User $user): bool
    {
        return $user->can('create_anfitrion');
    }

    /**
     * Determine whether the user can update the anfitrion.
     */
    public function update(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('update_anfitrion');
    }

    /**
     * Determine whether the user can delete the anfitrion.
     */
    public function delete(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('delete_anfitrion');
    }

    /**
     * Determine whether the user can delete multiple anfitriones.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_anfitrion');
    }

    /**
     * Determine whether the user can restore the anfitrion.
     */
    public function restore(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('restore_anfitrion');
    }

    /**
     * Determine whether the user can replicate the anfitrion.
     */
    public function replicate(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('replicate_anfitrion');
    }

    /**
     * Determine whether the user can reorder anfitriones.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_anfitrion');
    }

    /**
     * Determine whether the user can force delete the anfitrion.
     */
    public function forceDelete(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('force_delete_anfitrion');
    }
}
