<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cajero;
use Illuminate\Auth\Access\HandlesAuthorization;

class CajeroPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any cajeros.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_cajero');
    }

    /**
     * Determine whether the user can view the cajero.
     */
    public function view(User $user, Cajero $cajero): bool
    {
        return $user->can('view_cajero');
    }

    /**
     * Determine whether the user can create cajeros.
     */
    public function create(User $user): bool
    {
        return $user->can('create_cajero');
    }

    /**
     * Determine whether the user can update the cajero.
     */
    public function update(User $user, Cajero $cajero): bool
    {
        return $user->can('update_cajero');
    }

    /**
     * Determine whether the user can delete the cajero.
     */
    public function delete(User $user, Cajero $cajero): bool
    {
        return $user->can('delete_cajero');
    }

    /**
     * Determine whether the user can delete multiple cajeros.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_cajero');
    }

    /**
     * Determine whether the user can restore the cajero.
     */
    public function restore(User $user, Cajero $cajero): bool
    {
        return $user->can('restore_cajero');
    }

    /**
     * Determine whether the user can replicate the cajero.
     */
    public function replicate(User $user, Cajero $cajero): bool
    {
        return $user->can('replicate_cajero');
    }

    /**
     * Determine whether the user can force delete the cajero.
     */
    public function forceDelete(User $user, Cajero $cajero): bool
    {
        return $user->can('force_delete_cajero');
    }
}
