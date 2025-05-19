<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Seguimiento;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeguimientoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any seguimientos.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_seguimiento');
    }

    /**
     * Determine whether the user can view a specific seguimiento.
     */
    public function view(User $user, Seguimiento $seguimiento): bool
    {
        return $user->can('view_seguimiento');
    }

    /**
     * Determine whether the user can create seguimientos.
     */
    public function create(User $user): bool
    {
        return $user->can('create_seguimiento');
    }

    /**
     * Determine whether the user can update the seguimiento.
     */
    public function update(User $user, Seguimiento $seguimiento): bool
    {
        return $user->can('update_seguimiento');
    }

    /**
     * Determine whether the user can delete the seguimiento.
     */
    public function delete(User $user, Seguimiento $seguimiento): bool
    {
        return $user->can('delete_seguimiento');
    }

    /**
     * Determine whether the user can delete multiple seguimientos.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_seguimiento');
    }

    /**
     * Determine whether the user can restore the seguimiento.
     */
    public function restore(User $user, Seguimiento $seguimiento): bool
    {
        return $user->can('restore_seguimiento');
    }

    /**
     * Determine whether the user can replicate the seguimiento.
     */
    public function replicate(User $user, Seguimiento $seguimiento): bool
    {
        return $user->can('replicate_seguimiento');
    }

    /**
     * Determine whether the user can force delete the seguimiento.
     */
    public function forceDelete(User $user, Seguimiento $seguimiento): bool
    {
        return $user->can('force_delete_seguimiento');
    }
}
