<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Anfitrion;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnfitrionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_anfitrion');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('view_anfitrion');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_anfitrion');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('update_anfitrion');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('delete_anfitrion');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_anfitrion');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('force_delete_anfitrion');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_anfitrion');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('restore_anfitrion');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_anfitrion');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Anfitrion $anfitrion): bool
    {
        return $user->can('replicate_anfitrion');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_anfitrion');
    }
}
