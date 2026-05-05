<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Vehicle;
use App\Services\ShipmentService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ShipmentDemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (Shipment::query()->exists()) {
            return;
        }

        $shipmentService = app(ShipmentService::class);

        $branches = Branch::query()->get()->keyBy('city');
        $vehicles = Vehicle::query()->get()->keyBy('plate_number');
        $mainCustomer = Customer::query()->where('email', 'customer@ekspedisi.test')->firstOrFail();
        $shopCustomer = Customer::query()->where('email', 'tokosentosa@ekspedisi.test')->firstOrFail();

        $pendingShipment = $shipmentService->createShipment($mainCustomer, [
            'origin_branch_id' => $branches['Jakarta']->id,
            'destination_branch_id' => $branches['Bandung']->id,
            'sender_name' => 'Customer Ekspedisi',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Melati No. 10, Jakarta Selatan',
            'receiver_name' => 'Budi Santoso',
            'receiver_phone' => '081377788899',
            'receiver_address' => 'Jl. Dago Atas No. 21, Bandung',
            'total_weight' => 7.50,
            'service_type' => 'regular',
            'shipment_date' => now()->subDay()->toDateString(),
            'estimated_arrival' => now()->addDays(2)->toDateString(),
            'initial_tracking_at' => now()->subDay()->addHours(2),
            'item_name' => 'Pakaian dan Dokumen',
            'quantity' => 2,
            'description' => 'Paket customer baru, masih menunggu proses admin.',
        ]);

        $this->updatePayment($pendingShipment, Payment::STATUS_PENDING);

        $inTransitShipment = $shipmentService->createShipment($mainCustomer, [
            'origin_branch_id' => $branches['Bandung']->id,
            'destination_branch_id' => $branches['Surabaya']->id,
            'sender_name' => 'Customer Ekspedisi',
            'sender_phone' => '081234567890',
            'sender_address' => 'Jl. Merdeka No. 45, Bandung',
            'receiver_name' => 'Toko Maju Jaya',
            'receiver_phone' => '081122334455',
            'receiver_address' => 'Jl. Raya Darmo No. 19, Surabaya',
            'total_weight' => 5.25,
            'service_type' => 'express',
            'shipment_date' => now()->subDays(2)->toDateString(),
            'estimated_arrival' => now()->addDay()->toDateString(),
            'initial_tracking_at' => now()->subDays(2)->addHour(),
            'item_name' => 'Aksesoris Elektronik',
            'quantity' => 1,
            'description' => 'Paket express untuk kebutuhan toko.',
        ]);

        $this->advanceShipment(
            $inTransitShipment,
            $vehicles['B 9123 TXL'],
            Shipment::STATUS_IN_TRANSIT,
            Payment::STATUS_PAID,
            'transfer',
            now()->subDays(2),
            [
                [
                    'status' => Shipment::STATUS_PROCESSED,
                    'location' => 'Gudang Bandung',
                    'description' => 'Shipment diverifikasi admin dan siap dijemput kurir.',
                    'tracked_at' => now()->subDays(2)->addHours(3),
                ],
                [
                    'status' => Shipment::STATUS_PICKED_UP,
                    'location' => 'Cabang Bandung',
                    'description' => 'Kurir mengambil shipment dari cabang asal.',
                    'tracked_at' => now()->subDays(1)->addHours(1),
                ],
                [
                    'status' => Shipment::STATUS_IN_TRANSIT,
                    'location' => 'Tol Cipali',
                    'description' => 'Shipment sedang menuju Surabaya.',
                    'tracked_at' => now()->subHours(6),
                ],
            ]
        );

        $processedShipment = $shipmentService->createShipment($shopCustomer, [
            'origin_branch_id' => $branches['Jakarta']->id,
            'destination_branch_id' => $branches['Surabaya']->id,
            'sender_name' => 'Toko Sentosa',
            'sender_phone' => '082233445566',
            'sender_address' => 'Jl. Soekarno Hatta No. 25, Bandung',
            'receiver_name' => 'PT Sinar Timur',
            'receiver_phone' => '081255566677',
            'receiver_address' => 'Jl. Ahmad Yani No. 90, Surabaya',
            'total_weight' => 10.00,
            'service_type' => 'regular',
            'shipment_date' => now()->subDays(1)->toDateString(),
            'estimated_arrival' => now()->addDays(3)->toDateString(),
            'initial_tracking_at' => now()->subDays(1)->addHours(2),
            'item_name' => 'Peralatan Display Toko',
            'quantity' => 3,
            'description' => 'Menunggu penugasan kendaraan dari admin.',
        ]);

        $this->updateShipmentStatus(
            $processedShipment,
            Shipment::STATUS_PROCESSED,
            null,
            [
                [
                    'status' => Shipment::STATUS_PROCESSED,
                    'location' => 'Gudang Jakarta',
                    'description' => 'Admin sedang menyiapkan shipment untuk keberangkatan.',
                    'tracked_at' => now()->subHours(10),
                ],
            ]
        );

        $deliveredShipment = $shipmentService->createShipment($shopCustomer, [
            'origin_branch_id' => $branches['Surabaya']->id,
            'destination_branch_id' => $branches['Jakarta']->id,
            'sender_name' => 'Toko Sentosa',
            'sender_phone' => '082233445566',
            'sender_address' => 'Jl. Pemuda No. 23, Surabaya',
            'receiver_name' => 'Nadia Prameswari',
            'receiver_phone' => '081388899900',
            'receiver_address' => 'Jl. Tebet Timur No. 11, Jakarta',
            'total_weight' => 3.40,
            'service_type' => 'express',
            'shipment_date' => now()->subDays(4)->toDateString(),
            'estimated_arrival' => now()->subDays(2)->toDateString(),
            'initial_tracking_at' => now()->subDays(4)->addHour(),
            'item_name' => 'Dokumen dan Sampel Produk',
            'quantity' => 1,
            'description' => 'Shipment express yang sudah selesai dikirim.',
        ]);

        $this->advanceShipment(
            $deliveredShipment,
            $vehicles['L 7788 EXP'],
            Shipment::STATUS_DELIVERED,
            Payment::STATUS_PAID,
            'cash',
            now()->subDays(4),
            [
                [
                    'status' => Shipment::STATUS_PROCESSED,
                    'location' => 'Gudang Surabaya',
                    'description' => 'Shipment selesai diproses admin.',
                    'tracked_at' => now()->subDays(4)->addHours(2),
                ],
                [
                    'status' => Shipment::STATUS_PICKED_UP,
                    'location' => 'Cabang Surabaya',
                    'description' => 'Kurir mengambil shipment dari cabang Surabaya.',
                    'tracked_at' => now()->subDays(3)->addHours(1),
                ],
                [
                    'status' => Shipment::STATUS_IN_TRANSIT,
                    'location' => 'Gerbang Tol Cikampek',
                    'description' => 'Shipment sedang dalam perjalanan ke Jakarta.',
                    'tracked_at' => now()->subDays(3)->addHours(10),
                ],
                [
                    'status' => Shipment::STATUS_DELIVERED,
                    'location' => 'Jakarta',
                    'description' => 'Shipment telah diterima oleh penerima.',
                    'tracked_at' => now()->subDays(2)->addHours(3),
                ],
            ]
        );
    }

    private function advanceShipment(
        Shipment $shipment,
        Vehicle $vehicle,
        string $finalStatus,
        string $paymentStatus,
        ?string $paymentMethod,
        Carbon $paymentDate,
        array $trackings
    ): void {
        $this->updateShipmentStatus($shipment, $finalStatus, $vehicle, $trackings);
        $this->updatePayment($shipment, $paymentStatus, $paymentMethod, $paymentDate);
    }

    private function updateShipmentStatus(Shipment $shipment, string $status, ?Vehicle $vehicle, array $trackings = []): void
    {
        $shipment->update([
            'status' => $status,
            'vehicle_id' => $vehicle?->id,
        ]);

        foreach ($trackings as $tracking) {
            $shipment->shipmentTrackings()->create($tracking);
        }
    }

    private function updatePayment(
        Shipment $shipment,
        string $status,
        ?string $method = null,
        ?Carbon $paidAt = null
    ): void {
        $shipment->payment()->update([
            'payment_status' => $status,
            'payment_method' => $method,
            'payment_date' => $paidAt?->toDateString(),
        ]);
    }
}
