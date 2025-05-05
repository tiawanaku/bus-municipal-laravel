<?php

namespace App\Policies;

use App\Models\User;
use App\Models\InventarioTalonarios;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventarioTalonariosPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any inventario talonarios.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_inventario::talonarios');
    }

    /**
     * Determine whether the user can view the inventario talonario.
     */
    public function view(User $user, InventarioTalonarios $inventarioTalonarios): bool
    {
        return $user->can('view_inventario::talonarios');
    }

    /**
     * Determine whether the user can create inventario talonarios.
     */
    public function create(User $user): bool
    {
        return $user->can('create_inventario::talonarios');
    }

    /**
     * Determine whether the user can update the inventario talonario.
     */
    public function update(User $user, InventarioTalonarios $inventarioTalonarios): bool
    {
        return $user->can('update_inventario::talonarios');
    }

    /**
     * Determine whether the user can delete the inventario talonario.
     */
    public function delete(User $user, InventarioTalonarios $inventarioTalonarios): bool
    {
        return $user->can('delete_inventario::talonarios');
    }

    /**
     * Determine whether the user can delete multiple inventario talonarios.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_inventario::talonarios');
    }

    /**
     * Determine whether the user can restore the inventario talonario.
     */
    public function restore(User $user, InventarioTalonarios $inventarioTalonarios): bool
    {
        return $user->can('restore_inventario::talonarios');
    }

    /**
     * Determine whether the user can replicate the inventario talonario.
     */
    public function replicate(User $user, InventarioTalonarios $inventarioTalonarios): bool
    {
        return $user->can('replicate_inventario::talonarios');
    }

    /**
     * Determine whether the user can force delete the inventario talonario.
     */
    public function forceDelete(User $user, InventarioTalonarios $inventarioTalonarios): bool
    {
        return $user->can('force_delete_inventario::talonarios');
    }
}
