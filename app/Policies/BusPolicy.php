<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Bus;
use Illuminate\Auth\Access\HandlesAuthorization;

class BusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any buses.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bus');
    }

    /**
     * Determine whether the user can view the bus.
     */
    public function view(User $user, Bus $bus): bool
    {
        return $user->can('view_bus');
    }

    /**
     * Determine whether the user can create buses.
     */
    public function create(User $user): bool
    {
        return $user->can('create_bus');
    }

    /**
     * Determine whether the user can update the bus.
     */
    public function update(User $user, Bus $bus): bool
    {
        return $user->can('update_bus');
    }

    /**
     * Determine whether the user can delete the bus.
     */
    public function delete(User $user, Bus $bus): bool
    {
        return $user->can('delete_bus');
    }

    /**
     * Determine whether the user can delete multiple buses.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bus');
    }

    /**
     * Determine whether the user can restore the bus.
     */
    public function restore(User $user, Bus $bus): bool
    {
        return $user->can('restore_bus');
    }

    /**
     * Determine whether the user can replicate the bus.
     */
    public function replicate(User $user, Bus $bus): bool
    {
        return $user->can('replicate_bus');
    }

    /**
     * Determine whether the user can force delete the bus.
     */
    public function forceDelete(User $user, Bus $bus): bool
    {
        return $user->can('force_delete_bus');
    }
}
