<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // ─── ADMIN ──────────────────────────────────────────────
        User::query()->updateOrCreate(
            ['email' => 'admin@ekspedisi.test'],
            [
                'name' => 'Admin Utama',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin2@ekspedisi.test'],
            [
                'name' => 'Admin Cabang',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // ─── COURIER ────────────────────────────────────────────
        $courier1 = User::query()->updateOrCreate(
            ['email' => 'kurir1@ekspedisi.test'],
            [
                'name' => 'Rudi Hartono',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COURIER,
            ]
        );

        $courier2 = User::query()->updateOrCreate(
            ['email' => 'kurir2@ekspedisi.test'],
            [
                'name' => 'Andi Saputra',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COURIER,
            ]
        );

        $courier3 = User::query()->updateOrCreate(
            ['email' => 'kurir3@ekspedisi.test'],
            [
                'name' => 'Siti Lestari',
                'password' => Hash::make('password'),
                'role' => User::ROLE_COURIER,
            ]
        );

        // ─── CUSTOMER ───────────────────────────────────────────
        $customer1 = User::query()->updateOrCreate(
            ['email' => 'customer@ekspedisi.test'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
            ]
        );

        $customer2 = User::query()->updateOrCreate(
            ['email' => 'customer2@ekspedisi.test'],
            [
                'name' => 'Nadia Prameswari',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
            ]
        );

        $customer3 = User::query()->updateOrCreate(
            ['email' => 'toko@ekspedisi.test'],
            [
                'name' => 'Toko Sentosa',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CUSTOMER,
            ]
        );

        // ─── CUSTOMER PROFILES ──────────────────────────────────
        Customer::query()->updateOrCreate(
            ['email' => 'customer@ekspedisi.test'],
            [
                'user_id' => $customer1->id,
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'address' => 'Jl. Melati No. 10, Jakarta Selatan',
            ]
        );

        Customer::query()->updateOrCreate(
            ['email' => 'customer2@ekspedisi.test'],
            [
                'user_id' => $customer2->id,
                'name' => 'Nadia Prameswari',
                'phone' => '081388899900',
                'address' => 'Jl. Tebet Timur No. 11, Jakarta',
            ]
        );

        Customer::query()->updateOrCreate(
            ['email' => 'toko@ekspedisi.test'],
            [
                'user_id' => $customer3->id,
                'name' => 'Toko Sentosa',
                'phone' => '082233445566',
                'address' => 'Jl. Soekarno Hatta No. 25, Bandung',
            ]
        );
    }
}
