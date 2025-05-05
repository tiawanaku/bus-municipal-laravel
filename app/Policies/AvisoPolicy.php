<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Aviso;
use Illuminate\Auth\Access\HandlesAuthorization;

class AvisoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any avisos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_aviso');
    }

    /**
     * Determine whether the user can view the aviso.
     */
    public function view(User $user, Aviso $aviso): bool
    {
        return $user->can('view_aviso');
    }

    /**
     * Determine whether the user can create avisos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_aviso');
    }

    /**
     * Determine whether the user can update the aviso.
     */
    public function update(User $user, Aviso $aviso): bool
    {
        return $user->can('update_aviso');
    }

    /**
     * Determine whether the user can delete the aviso.
     */
    public function delete(User $user, Aviso $aviso): bool
    {
        return $user->can('delete_aviso');
    }

    /**
     * Determine whether the user can delete multiple avisos.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_aviso');
    }

    /**
     * Determine whether the user can restore the aviso.
     */
    public function restore(User $user, Aviso $aviso): bool
    {
        return $user->can('restore_aviso');
    }

    /**
     * Determine whether the user can replicate the aviso.
     */
    public function replicate(User $user, Aviso $aviso): bool
    {
        return $user->can('replicate_aviso');
    }

    /**
     * Determine whether the user can force delete the aviso.
     */
    public function forceDelete(User $user, Aviso $aviso): bool
    {
        return $user->can('force_delete_aviso');
    }
}
