<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryAttempt;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentAssignment;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ShipmentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ShipmentDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Shipment::query()->exists()) {
            return;
        }

        $shipmentService = app(ShipmentService::class);
        $branches = Branch::query()->get()->keyBy('city');
        $vehicles = Vehicle::query()->get()->keyBy('plate_number');
        $admin = User::where('email', 'admin@ekspedisi.test')->first();
        $courier1 = User::where('email', 'kurir1@ekspedisi.test')->first();
        $courier2 = User::where('email', 'kurir2@ekspedisi.test')->first();
        $customer1 = Customer::where('email', 'customer@ekspedisi.test')->first();
        $customer2 = Customer::where('email', 'customer2@ekspedisi.test')->first();
        $toko = Customer::where('email', 'toko@ekspedisi.test')->first();

        // ─── 1. Shipment CREATED (baru dibuat, drop-off, prepaid) ───
        $s1 = $shipmentService->createShipment($customer1, [
            'origin_branch_id' => $branches['Jakarta']->id,
            'destination_branch_id' => $branches['Bandung']->id,
            'sender_name' => 'Budi Santoso', 'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Melati No. 10, Jakarta Selatan',
            'receiver_name' => 'Andi Wijaya', 'receiver_phone' => '081377788899',
            'receiver_address' => 'Jl. Dago Atas No. 21, Bandung',
            'total_weight' => 5.0, 'service_type' => 'regular',
            'shipment_date' => now()->toDateString(), 'estimated_arrival' => now()->addDays(3)->toDateString(),
            'item_name' => 'Pakaian & Dokumen', 'quantity' => 2, 'description' => 'Paket drop-off prepaid, baru dibuat.',
            'pickup_type' => 'drop_off', 'payment_type' => 'prepaid',
        ]);

        // ─── 2. Shipment PICKED_UP (sudah dijemput) ───
        $s2 = $shipmentService->createShipment($customer1, [
            'origin_branch_id' => $branches['Bandung']->id,
            'destination_branch_id' => $branches['Surabaya']->id,
            'sender_name' => 'Budi Santoso', 'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Merdeka No. 45, Bandung',
            'receiver_name' => 'Toko Maju Jaya', 'receiver_phone' => '081122334455',
            'receiver_address' => 'Jl. Raya Darmo No. 19, Surabaya',
            'total_weight' => 3.5, 'service_type' => 'express',
            'shipment_date' => now()->subDay()->toDateString(), 'estimated_arrival' => now()->addDays(2)->toDateString(),
            'item_name' => 'Aksesoris Elektronik', 'quantity' => 1, 'description' => 'Request pickup, sudah dijemput kurir.',
            'pickup_type' => 'pickup_request',
            'pickup_address' => 'Jl. Merdeka No. 45, Bandung',
            'pickup_contact_name' => 'Budi Santoso', 'pickup_contact_phone' => '081234567890',
            'payment_type' => 'prepaid',
        ]);
        $this->setShipment($s2, Shipment::STATUS_PICKED_UP, $admin);
        $this->assignCourier($s2, $courier1, $vehicles['D 8456 KUR'], $admin);
        $this->payShipment($s2, 'transfer');

        // ─── 3. Shipment IN_TRANSIT (dalam perjalanan) ───
        $s3 = $shipmentService->createShipment($toko, [
            'origin_branch_id' => $branches['Jakarta']->id,
            'destination_branch_id' => $branches['Surabaya']->id,
            'sender_name' => 'Toko Sentosa', 'sender_phone' => '082233445566',
            'sender_address' => 'Jl. Soekarno Hatta No. 25, Bandung',
            'receiver_name' => 'PT Sinar Timur', 'receiver_phone' => '081255566677',
            'receiver_address' => 'Jl. Ahmad Yani No. 90, Surabaya',
            'total_weight' => 12.0, 'service_type' => 'regular',
            'shipment_date' => now()->subDays(2)->toDateString(), 'estimated_arrival' => now()->addDay()->toDateString(),
            'item_name' => 'Peralatan Display Toko', 'quantity' => 5, 'description' => 'Dalam perjalanan Jakarta → Surabaya.',
            'pickup_type' => 'drop_off', 'payment_type' => 'prepaid',
        ]);
        $this->setShipment($s3, Shipment::STATUS_IN_TRANSIT, $admin);
        $this->assignCourier($s3, $courier1, $vehicles['B 9123 TXL'], $admin);
        $this->payShipment($s3, 'transfer');
        $s3->shipmentTrackings()->create(['status' => Shipment::STATUS_PICKED_UP, 'location' => 'Jakarta', 'description' => 'Paket dijemput dari cabang.', 'tracked_at' => now()->subDays(1)]);
        $s3->shipmentTrackings()->create(['status' => Shipment::STATUS_AT_ORIGIN_HUB, 'location' => 'Hub Jakarta', 'description' => 'Proses sorting di hub asal.', 'tracked_at' => now()->subHours(18)]);
        $s3->shipmentTrackings()->create(['status' => Shipment::STATUS_IN_TRANSIT, 'location' => 'Tol Cipularang', 'description' => 'Dalam perjalanan ke Surabaya.', 'tracked_at' => now()->subHours(6)]);

        // ─── 4. Shipment OUT_FOR_DELIVERY (sedang diantar) ───
        $s4 = $shipmentService->createShipment($customer2, [
            'origin_branch_id' => $branches['Surabaya']->id,
            'destination_branch_id' => $branches['Jakarta']->id,
            'sender_name' => 'Nadia Prameswari', 'sender_phone' => '081388899900',
            'sender_address' => 'Jl. Tebet Timur No. 11, Jakarta',
            'receiver_name' => 'Rina Marlina', 'receiver_phone' => '081566677788',
            'receiver_address' => 'Jl. Gatot Subroto No. 50, Jakarta',
            'total_weight' => 2.0, 'service_type' => 'express',
            'shipment_date' => now()->subDays(2)->toDateString(), 'estimated_arrival' => now()->toDateString(),
            'item_name' => 'Kosmetik & Skincare', 'quantity' => 3, 'description' => 'Sedang diantar ke penerima.',
            'pickup_type' => 'pickup_request',
            'pickup_address' => 'Jl. Tebet Timur No. 11, Jakarta',
            'pickup_contact_name' => 'Nadia', 'pickup_contact_phone' => '081388899900',
            'payment_type' => 'prepaid',
        ]);
        $this->setShipment($s4, Shipment::STATUS_OUT_FOR_DELIVERY, $admin);
        $this->assignCourier($s4, $courier2, $vehicles['L 7788 EXP'], $admin);
        $this->payShipment($s4, 'e-wallet');
        $s4->shipmentTrackings()->create(['status' => Shipment::STATUS_PICKED_UP, 'location' => 'Surabaya', 'description' => 'Paket dijemput.', 'tracked_at' => now()->subDays(1)->addHours(3)]);
        $s4->shipmentTrackings()->create(['status' => Shipment::STATUS_AT_ORIGIN_HUB, 'location' => 'Hub Surabaya', 'description' => 'Sorting.', 'tracked_at' => now()->subDays(1)]);
        $s4->shipmentTrackings()->create(['status' => Shipment::STATUS_IN_TRANSIT, 'location' => 'Tol Cikampek', 'description' => 'Transit.', 'tracked_at' => now()->subHours(12)]);
        $s4->shipmentTrackings()->create(['status' => Shipment::STATUS_AT_DEST_HUB, 'location' => 'Hub Jakarta', 'description' => 'Tiba di hub tujuan.', 'tracked_at' => now()->subHours(4)]);
        $s4->shipmentTrackings()->create(['status' => Shipment::STATUS_OUT_FOR_DELIVERY, 'location' => 'Jakarta Selatan', 'description' => 'Kurir bawa paket ke alamat.', 'tracked_at' => now()->subHours(2)]);

        // ─── 5. Shipment FAILED_DELIVERY (gagal antar, percobaan 1) ───
        $s5 = $shipmentService->createShipment($toko, [
            'origin_branch_id' => $branches['Bandung']->id,
            'destination_branch_id' => $branches['Jakarta']->id,
            'sender_name' => 'Toko Sentosa', 'sender_phone' => '082233445566',
            'sender_address' => 'Jl. Soekarno Hatta No. 25, Bandung',
            'receiver_name' => 'PT Maju Bersama', 'receiver_phone' => '081499900011',
            'receiver_address' => 'Jl. Sudirman No. 100, Jakarta',
            'total_weight' => 8.0, 'service_type' => 'regular',
            'shipment_date' => now()->subDays(3)->toDateString(), 'estimated_arrival' => now()->subDay()->toDateString(),
            'item_name' => 'Sample Produk', 'quantity' => 2, 'description' => 'Gagal antar percobaan 1.',
            'pickup_type' => 'drop_off', 'payment_type' => 'cod',
        ]);
        $this->setShipment($s5, Shipment::STATUS_OUT_FOR_DELIVERY, $admin);
        $this->assignCourier($s5, $courier2, $vehicles['D 8456 KUR'], $admin);
        foreach ([Shipment::STATUS_PICKED_UP, Shipment::STATUS_AT_ORIGIN_HUB, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_AT_DEST_HUB, Shipment::STATUS_OUT_FOR_DELIVERY] as $st) {
            $s5->shipmentTrackings()->create(['status' => $st, 'location' => 'Rute', 'description' => Shipment::statusLabels()[$st], 'tracked_at' => now()->subDays(rand(1, 3))]);
        }
        // Delivery attempt failed
        $s5->update(['status' => Shipment::STATUS_FAILED_DELIVERY]);
        DeliveryAttempt::create([
            'shipment_id' => $s5->id, 'attempted_by' => $courier2->id, 'attempt_number' => 1,
            'status' => 'failed', 'failure_reason' => 'receiver_not_available',
            'notes' => 'Penerima tidak ada di tempat, gedung kosong.', 'attempted_at' => now()->subHours(3),
        ]);
        $s5->shipmentTrackings()->create(['status' => Shipment::STATUS_FAILED_DELIVERY, 'location' => 'Jakarta', 'description' => 'Gagal antar. Penerima tidak ada. Sisa 2 percobaan.', 'tracked_at' => now()->subHours(3)]);

        // ─── 6. Shipment DELIVERED (berhasil dikirim) + COD ───
        $s6 = $shipmentService->createShipment($customer2, [
            'origin_branch_id' => $branches['Jakarta']->id,
            'destination_branch_id' => $branches['Bandung']->id,
            'sender_name' => 'Nadia Prameswari', 'sender_phone' => '081388899900',
            'sender_address' => 'Jl. Tebet Timur No. 11, Jakarta',
            'receiver_name' => 'Sari Dewi', 'receiver_phone' => '081622233344',
            'receiver_address' => 'Jl. Buah Batu No. 30, Bandung',
            'total_weight' => 1.5, 'service_type' => 'express',
            'shipment_date' => now()->subDays(4)->toDateString(), 'estimated_arrival' => now()->subDays(2)->toDateString(),
            'item_name' => 'Aksesoris Fashion', 'quantity' => 4, 'description' => 'COD, sudah diterima penerima.',
            'pickup_type' => 'drop_off', 'payment_type' => 'cod',
        ]);
        $this->setShipment($s6, Shipment::STATUS_DELIVERED, $admin);
        $this->assignCourier($s6, $courier1, $vehicles['B 9123 TXL'], $admin, 'completed');
        foreach ([Shipment::STATUS_PICKED_UP, Shipment::STATUS_AT_ORIGIN_HUB, Shipment::STATUS_IN_TRANSIT, 'at_dest_hub', 'out_for_delivery', 'delivered'] as $st) {
            $s6->shipmentTrackings()->create(['status' => $st, 'location' => 'Rute', 'description' => Shipment::statusLabels()[$st], 'tracked_at' => now()->subDays(rand(1, 4))]);
        }
        // Delivery attempt success
        DeliveryAttempt::create([
            'shipment_id' => $s6->id, 'attempted_by' => $courier1->id, 'attempt_number' => 1,
            'status' => 'success', 'notes' => 'Diterima oleh penerima langsung.', 'attempted_at' => now()->subDays(2),
        ]);
        // COD collected
        $s6->payment()->update([
            'payment_status' => Payment::STATUS_PAID,
            'payment_type' => 'cod',
            'payment_method' => 'cash',
            'payment_date' => now()->subDays(2)->toDateString(),
            'collected_by' => $courier1->id,
            'cod_collected_at' => now()->subDays(2),
        ]);
    }

    private function setShipment(Shipment $shipment, string $status, ?User $updatedBy = null): void
    {
        $shipment->update(['status' => $status]);
        $shipment->shipmentTrackings()->create([
            'status' => $status,
            'location' => $shipment->originBranch?->city ?? 'Gudang',
            'description' => Shipment::statusLabels()[$status] ?? $status,
            'tracked_at' => now(),
            'created_by' => $updatedBy?->id,
        ]);
    }

    private function assignCourier(Shipment $shipment, User $courier, Vehicle $vehicle, User $admin, string $status = 'active'): void
    {
        ShipmentAssignment::create([
            'shipment_id' => $shipment->id,
            'courier_id' => $courier->id,
            'vehicle_id' => $vehicle->id,
            'assigned_by' => $admin->id,
            'status' => $status,
            'assigned_at' => now()->subDays(rand(1, 3)),
            'completed_at' => $status === 'completed' ? now()->subDays(2) : null,
        ]);
        $shipment->update(['vehicle_id' => $vehicle->id]);
    }

    private function payShipment(Shipment $shipment, string $method): void
    {
        $shipment->payment()->update([
            'payment_status' => Payment::STATUS_PAID,
            'payment_method' => $method,
            'payment_date' => now()->subDay()->toDateString(),
        ]);
    }
}
