<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Customer;
use App\Models\Shipment;
use App\Models\User;

trait InteractsWithShipments
{
    protected function currentCustomer(User $user): Customer
    {
        abort_unless($user->customer, 403, 'Data customer belum tersedia untuk akun ini.');

        return $user->customer;
    }

    protected function authorizeShipmentAccess(User $user, Shipment $shipment): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isCustomer() && $user->customer && $shipment->customer_id === $user->customer->id) {
            return;
        }

        if ($user->isCourier() && $shipment->vehicle_id !== null) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke shipment ini.');
    }

    protected function defaultTrackingLocation(Shipment $shipment): string
    {
        return $shipment->vehicle?->plate_number
            ?? $shipment->originBranch?->city
            ?? 'Gudang';
    }
}
