<?php

namespace App\Policies;

use App\Models\ShipmentAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShipmentAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCourier();
    }

    public function view(User $user, ShipmentAssignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCourier()) {
            return $assignment->courier_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function assign(User $user): bool
    {
        return $user->isAdmin();
    }

    public function reassign(User $user, ShipmentAssignment $assignment): bool
    {
        return $user->isAdmin();
    }

    public function cancel(User $user, ShipmentAssignment $assignment): bool
    {
        return $user->isAdmin();
    }

    public function complete(User $user, ShipmentAssignment $assignment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCourier()) {
            return $assignment->courier_id === $user->id && $assignment->status === 'active';
        }

        return false;
    }
}
