<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Shipment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ShipmentService
{
    public function createShipment(Customer $customer, array $attributes, ?string $photoPath = null): Shipment
    {
        $rate = $this->findRate(
            $attributes['origin_branch_id'],
            $attributes['destination_branch_id'],
            $attributes['service_type']
        );

        if (! $rate) {
            throw ValidationException::withMessages([
                'service_type' => 'Tarif untuk rute dan layanan tersebut belum tersedia. Silakan pilih rute lain atau hubungi admin.',
            ]);
        }

        $shippingCost = $this->calculateShippingCost($rate, (float) $attributes['total_weight']);
        $shipmentDate = ! empty($attributes['shipment_date'])
            ? Carbon::parse($attributes['shipment_date'])
            : now();
        $estimatedArrival = ! empty($attributes['estimated_arrival'])
            ? Carbon::parse($attributes['estimated_arrival'])
            : $this->defaultEstimatedArrival($shipmentDate, $attributes['service_type']);
        $initialTrackingAt = ! empty($attributes['initial_tracking_at'])
            ? Carbon::parse($attributes['initial_tracking_at'])
            : now();

        return DB::transaction(function () use (
            $attributes,
            $customer,
            $estimatedArrival,
            $initialTrackingAt,
            $photoPath,
            $rate,
            $shipmentDate,
            $shippingCost
        ) {
            $shipment = Shipment::create([
                'tracking_number' => $this->generateTrackingNumber(),
                'customer_id' => $customer->id,
                'origin_branch_id' => $attributes['origin_branch_id'],
                'destination_branch_id' => $attributes['destination_branch_id'],
                'vehicle_id' => $attributes['vehicle_id'] ?? null,
                'sender_name' => $attributes['sender_name'],
                'sender_phone' => $attributes['sender_phone'],
                'sender_address' => $attributes['sender_address'],
                'receiver_name' => $attributes['receiver_name'],
                'receiver_phone' => $attributes['receiver_phone'],
                'receiver_address' => $attributes['receiver_address'],
                'total_weight' => $attributes['total_weight'],
                'shipping_cost' => $shippingCost,
                'service_type' => $attributes['service_type'],
                'status' => Shipment::STATUS_PENDING,
                'shipment_date' => $shipmentDate->toDateString(),
                'estimated_arrival' => $estimatedArrival->toDateString(),
            ]);

            $shipment->shipmentItems()->create([
                'item_name' => $attributes['item_name'],
                'quantity' => $attributes['quantity'],
                'weight' => $attributes['total_weight'],
                'description' => $attributes['description'] ?? null,
                'photo' => $photoPath,
            ]);

            $shipment->payment()->create([
                'amount' => $shippingCost,
                'payment_status' => Payment::STATUS_PENDING,
            ]);

            $shipment->shipmentTrackings()->create([
                'status' => Shipment::STATUS_PENDING,
                'location' => $rate->originBranch?->city ?? 'Gudang',
                'description' => 'Shipment berhasil dibuat dan menunggu proses admin.',
                'tracked_at' => $initialTrackingAt,
            ]);

            return $shipment->load([
                'customer',
                'originBranch',
                'destinationBranch',
                'shipmentItems',
                'payment',
                'shipmentTrackings',
            ]);
        });
    }

    public function findRate(int|string $originBranchId, int|string $destinationBranchId, string $serviceType): ?Rate
    {
        return Rate::query()
            ->with(['originBranch', 'destinationBranch'])
            ->where('origin_branch_id', $originBranchId)
            ->where('destination_branch_id', $destinationBranchId)
            ->where('service_type', $serviceType)
            ->first();
    }

    public function calculateShippingCost(Rate $rate, float $totalWeight): float
    {
        return round((float) $rate->price_per_kg * $totalWeight, 2);
    }

    public function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'EXP-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (Shipment::query()->where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    public function defaultEstimatedArrival(Carbon $shipmentDate, string $serviceType): Carbon
    {
        return $shipmentDate->copy()->addDays($serviceType === 'express' ? 1 : 3);
    }
}
