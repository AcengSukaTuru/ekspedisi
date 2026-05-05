<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Rate;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branches = [
            [
                'branch_name' => 'Cabang Jakarta Pusat',
                'city' => 'Jakarta',
                'address' => 'Jl. Jenderal Sudirman No. 15, Jakarta Pusat',
                'phone' => '0215551001',
            ],
            [
                'branch_name' => 'Cabang Bandung',
                'city' => 'Bandung',
                'address' => 'Jl. Asia Afrika No. 88, Bandung',
                'phone' => '0225552002',
            ],
            [
                'branch_name' => 'Cabang Surabaya',
                'city' => 'Surabaya',
                'address' => 'Jl. Pemuda No. 23, Surabaya',
                'phone' => '0315553003',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::query()->updateOrCreate(
                ['branch_name' => $branch['branch_name']],
                $branch
            );
        }

        $vehicles = [
            [
                'plate_number' => 'B 9123 TXL',
                'vehicle_type' => 'Box Van',
                'driver_name' => 'Rudi Hartono',
                'driver_phone' => '081300000111',
                'status' => 'on_trip',
            ],
            [
                'plate_number' => 'D 8456 KUR',
                'vehicle_type' => 'Pickup',
                'driver_name' => 'Andi Saputra',
                'driver_phone' => '081300000222',
                'status' => 'available',
            ],
            [
                'plate_number' => 'L 7788 EXP',
                'vehicle_type' => 'Truk CDE',
                'driver_name' => 'Siti Lestari',
                'driver_phone' => '081300000333',
                'status' => 'available',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::query()->updateOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                $vehicle
            );
        }

        $branchesByCity = Branch::query()->get()->keyBy('city');

        $routes = [
            ['origin' => 'Jakarta', 'destination' => 'Bandung', 'regular' => 12000, 'express' => 18000],
            ['origin' => 'Bandung', 'destination' => 'Jakarta', 'regular' => 12000, 'express' => 18000],
            ['origin' => 'Jakarta', 'destination' => 'Surabaya', 'regular' => 15000, 'express' => 22000],
            ['origin' => 'Surabaya', 'destination' => 'Jakarta', 'regular' => 15000, 'express' => 22000],
            ['origin' => 'Bandung', 'destination' => 'Surabaya', 'regular' => 14000, 'express' => 21000],
            ['origin' => 'Surabaya', 'destination' => 'Bandung', 'regular' => 14000, 'express' => 21000],
        ];

        foreach ($routes as $route) {
            $originBranch = $branchesByCity->get($route['origin']);
            $destinationBranch = $branchesByCity->get($route['destination']);

            foreach (['regular', 'express'] as $serviceType) {
                Rate::query()->updateOrCreate(
                    [
                        'origin_branch_id' => $originBranch->id,
                        'destination_branch_id' => $destinationBranch->id,
                        'service_type' => $serviceType,
                    ],
                    [
                        'price_per_kg' => $route[$serviceType],
                    ]
                );
            }
        }
    }
}
