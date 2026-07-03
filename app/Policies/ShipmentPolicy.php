<?php

namespace App\Policies;

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShipmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // We use scoped queries in controller for list
    }

    public function view(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCustomer()) {
            return $shipment->customer && $shipment->customer->user_id === $user->id;
        }

        if ($user->isCourier()) {
            $assignment = $shipment->activeAssignment()->first();
            return $assignment && $assignment->courier_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function update(User $user, Shipment $shipment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        
        return false;
    }

    public function assign(User $user, Shipment $shipment): bool
    {
        return $user->isAdmin();
    }

    public function updateStatus(User $user, Shipment $shipment): bool
    {
        // For Phase 2, admin can update status. Courier status update will be in Phase 3.
        return $user->isAdmin() || ($user->isCourier() && $this->view($user, $shipment));
    }
}
