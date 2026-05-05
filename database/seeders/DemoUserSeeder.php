<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@ekspedisi.test'],
            [
                'name' => 'Admin Ekspedisi',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'courier@ekspedisi.test'],
            [
                'name' => 'Kurir Ekspedisi',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COURIER,
            ]
        );

        $customerUser = User::query()->updateOrCreate(
            ['email' => 'customer@ekspedisi.test'],
            [
                'name' => 'Customer Ekspedisi',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
            ]
        );

        Customer::query()->updateOrCreate(
            ['email' => 'customer@ekspedisi.test'],
            [
                'user_id' => $customerUser->id,
                'name' => 'Customer Ekspedisi',
                'phone' => '081234567890',
                'address' => 'Jl. Melati No. 10, Jakarta Selatan',
            ]
        );

        Customer::query()->updateOrCreate(
            ['email' => 'tokosentosa@ekspedisi.test'],
            [
                'user_id' => null,
                'name' => 'Toko Sentosa',
                'phone' => '082233445566',
                'address' => 'Jl. Soekarno Hatta No. 25, Bandung',
            ]
        );
    }
}
