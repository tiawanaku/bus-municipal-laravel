<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AsignacionDeBus;
use Illuminate\Auth\Access\HandlesAuthorization;

class AsignacionDeBusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any asignacion de buses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_asignacion::de::bus');
    }

    /**
     * Determine whether the user can view the asignacion de bus.
     */
    public function view(User $user, AsignacionDeBus $asignacionDeBus): bool
    {
        return $user->can('view_asignacion::de::bus');
    }

    /**
     * Determine whether the user can create asignacion de buses.
     */
    public function create(User $user): bool
    {
        return $user->can('create_asignacion::de::bus');
    }

    /**
     * Determine whether the user can update the asignacion de bus.
     */
    public function update(User $user, AsignacionDeBus $asignacionDeBus): bool
    {
        return $user->can('update_asignacion::de::bus');
    }

    /**
     * Determine whether the user can delete the asignacion de bus.
     */
    public function delete(User $user, AsignacionDeBus $asignacionDeBus): bool
    {
        return $user->can('delete_asignacion::de::bus');
    }

    /**
     * Determine whether the user can delete multiple asignacion de buses.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_asignacion::de::bus');
    }

    /**
     * Determine whether the user can restore the asignacion de bus.
     */
    public function restore(User $user, AsignacionDeBus $asignacionDeBus): bool
    {
        return $user->can('restore_asignacion::de::bus');
    }

    /**
     * Determine whether the user can replicate the asignacion de bus.
     */
    public function replicate(User $user, AsignacionDeBus $asignacionDeBus): bool
    {
        return $user->can('replicate_asignacion::de::bus');
    }

    /**
     * Determine whether the user can reorder asignacion de buses.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_asignacion::de::bus');
    }

    /**
     * Determine whether the user can force delete the asignacion de bus.
     */
    public function forceDelete(User $user, AsignacionDeBus $asignacionDeBus): bool
    {
        return $user->can('force_delete_asignacion::de::bus');
    }
}
