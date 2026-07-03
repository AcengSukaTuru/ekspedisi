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

    protected function defaultTrackingLocation(Shipment $shipment): string
    {
        return $shipment->activeAssignment?->vehicle?->plate_number
            ?? $shipment->vehicle?->plate_number
            ?? $shipment->originBranch?->city
            ?? 'Gudang';
    }
}
