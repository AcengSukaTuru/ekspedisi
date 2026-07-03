<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'driver_name',
        'driver_phone',
        'status',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function activeShipments(): HasMany
    {
        return $this->hasMany(Shipment::class)->where('status', '!=', Shipment::STATUS_DELIVERED);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ShipmentAssignment::class);
    }

    public function activeAssignments(): HasMany
    {
        return $this->hasMany(ShipmentAssignment::class)->where('status', 'active');
    }
}
