<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_name',
        'city',
        'address',
        'phone',
    ];

    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_branch_id');
    }

    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_branch_id');
    }

    public function originRates(): HasMany
    {
        return $this->hasMany(Rate::class, 'origin_branch_id');
    }

    public function destinationRates(): HasMany
    {
        return $this->hasMany(Rate::class, 'destination_branch_id');
    }
}
