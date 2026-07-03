<?php

use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Safe data migration using DB queries instead of Eloquent models
        $shipments = DB::table('shipments')->whereNotNull('vehicle_id')->get();

        foreach ($shipments as $shipment) {
            // Get vehicle info
            $vehicle = DB::table('vehicles')->where('id', $shipment->vehicle_id)->first();

            if (!$vehicle) {
                continue;
            }

            $driverName = $vehicle->driver_name;

            if ($driverName) {
                // Find courier based on driver_name
                $couriers = DB::table('users')
                    ->where('name', $driverName)
                    ->where('role', 'courier')
                    ->get();

                if ($couriers->count() === 1) {
                    $courier = $couriers->first();

                    // Check if assignment already exists
                    $existingAssignment = DB::table('shipment_assignments')
                        ->where('shipment_id', $shipment->id)
                        ->where('status', 'active')
                        ->exists();

                    if (!$existingAssignment) {
                        DB::table('shipment_assignments')->insert([
                            'shipment_id' => $shipment->id,
                            'courier_id' => $courier->id,
                            'vehicle_id' => $shipment->vehicle_id,
                            'status' => 'active',
                            'assigned_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    // Ambiguous or none found, log warning
                    \Illuminate\Support\Facades\Log::warning(
                        "Cannot safely migrate assignment for shipment {$shipment->id} with driver name {$driverName}. " .
                        "Found {$couriers->count()} matching couriers."
                    );
                }
            }
        }
    }

    public function down(): void
    {
        // Reverse data migration
        DB::table('shipment_assignments')->truncate();
    }
};
