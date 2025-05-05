<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EntregaTalonario;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntregaTalonarioPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any entrega talonarios.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_entrega::talonario');
    }

    /**
     * Determine whether the user can view the entrega talonario.
     */
    public function view(User $user, EntregaTalonario $entregaTalonario): bool
    {
        return $user->can('view_entrega::talonario');
    }

    /**
     * Determine whether the user can create entrega talonarios.
     */
    public function create(User $user): bool
    {
        return $user->can('create_entrega::talonario');
    }

    /**
     * Determine whether the user can update the entrega talonario.
     */
    public function update(User $user, EntregaTalonario $entregaTalonario): bool
    {
        return $user->can('update_entrega::talonario');
    }

    /**
     * Determine whether the user can delete the entrega talonario.
     */
    public function delete(User $user, EntregaTalonario $entregaTalonario): bool
    {
        return $user->can('delete_entrega::talonario');
    }

    /**
     * Determine whether the user can delete multiple entrega talonarios.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_entrega::talonario');
    }

    /**
     * Determine whether the user can restore the entrega talonario.
     */
    public function restore(User $user, EntregaTalonario $entregaTalonario): bool
    {
        return $user->can('restore_entrega::talonario');
    }

    /**
     * Determine whether the user can replicate the entrega talonario.
     */
    public function replicate(User $user, EntregaTalonario $entregaTalonario): bool
    {
        return $user->can('replicate_entrega::talonario');
    }

    /**
     * Determine whether the user can force delete the entrega talonario.
     */
    public function forceDelete(User $user, EntregaTalonario $entregaTalonario): bool
    {
        return $user->can('force_delete_entrega::talonario');
    }
}
