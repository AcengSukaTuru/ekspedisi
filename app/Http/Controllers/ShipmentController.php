<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithShipments;
use App\Models\Branch;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\ShipmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    use InteractsWithShipments;

    public function adminIndex(): View
    {
        return view('shipments.index', [
            'shipments' => Shipment::query()
                ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle', 'payment'])
                ->latest()
                ->paginate(10),
            'title' => 'Semua Shipment',
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function customerIndex(Request $request): View
    {
        $customer = $this->currentCustomer($request->user());

        return view('shipments.index', [
            'shipments' => Shipment::query()
                ->with(['originBranch', 'destinationBranch', 'vehicle', 'payment'])
                ->where('customer_id', $customer->id)
                ->latest()
                ->paginate(10),
            'title' => 'Shipment Saya',
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function courierIndex(): View
    {
        return view('shipments.index', [
            'shipments' => Shipment::query()
                ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle', 'payment'])
                ->whereNotNull('vehicle_id')
                ->latest()
                ->paginate(10),
            'title' => 'Tugas Kurir',
            'role' => User::ROLE_COURIER,
        ]);
    }

    public function create(Request $request): View
    {
        $this->currentCustomer($request->user());

        $serviceTypes = Rate::query()
            ->select('service_type')
            ->distinct()
            ->orderBy('service_type')
            ->pluck('service_type');

        return view('shipments.create', [
            'branches' => Branch::query()->orderBy('branch_name')->get(),
            'serviceTypes' => $serviceTypes->isNotEmpty() ? $serviceTypes : collect(['regular']),
            'availableRates' => Rate::query()
                ->with(['originBranch', 'destinationBranch'])
                ->orderBy('origin_branch_id')
                ->orderBy('destination_branch_id')
                ->orderBy('service_type')
                ->get(),
        ]);
    }

    public function store(Request $request, ShipmentService $shipmentService): RedirectResponse
    {
        $customer = $this->currentCustomer($request->user());

        $validated = $request->validate([
            'origin_branch_id' => ['required', 'exists:branches,id'],
            'destination_branch_id' => ['required', 'exists:branches,id', 'different:origin_branch_id'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'string', 'max:20'],
            'sender_address' => ['required', 'string'],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20'],
            'receiver_address' => ['required', 'string'],
            'total_weight' => ['required', 'numeric', 'min:0.1'],
            'service_type' => ['required', 'string', 'max:50'],
            'shipment_date' => ['nullable', 'date'],
            'estimated_arrival' => ['nullable', 'date', 'after_or_equal:shipment_date'],
            'item_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photoPath = $request->hasFile('photo')
            ? $request->file('photo')->store('shipment-items', 'public')
            : null;

        $shipment = $shipmentService->createShipment($customer, $validated, $photoPath);

        return redirect()
            ->route('customer.shipments.show', $shipment)
            ->with('success', 'Shipment berhasil dibuat dengan nomor tracking '.$shipment->tracking_number.'.');
    }

    public function show(Request $request, Shipment $shipment): View
    {
        $this->authorizeShipmentAccess($request->user(), $shipment);

        $shipment->load([
            'customer.user',
            'originBranch',
            'destinationBranch',
            'vehicle',
            'shipmentItems',
            'payment',
            'shipmentTrackings' => fn ($query) => $query->latest('tracked_at'),
        ]);

        return view('shipments.show', [
            'shipment' => $shipment,
            'availableVehicles' => $request->user()->isAdmin()
                ? Vehicle::query()->orderBy('plate_number')->get()
                : collect(),
            'backRoute' => match (true) {
                $request->user()->isAdmin() => 'admin.shipments.index',
                $request->user()->isCourier() => 'courier.shipments.index',
                default => 'customer.shipments.index',
            },
        ]);
    }

    public function updateStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'estimated_arrival' => ['nullable', 'date'],
            'tracking_location' => ['nullable', 'string', 'max:255'],
            'tracking_description' => ['nullable', 'string'],
        ]);

        $shipment->loadMissing(['originBranch', 'vehicle']);

        $shipment->fill([
            'status' => $validated['status'],
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'estimated_arrival' => $validated['estimated_arrival'] ?? $shipment->estimated_arrival,
        ]);

        $shouldCreateTracking = $shipment->isDirty(['status', 'vehicle_id']) || ! empty($validated['tracking_description']);

        $shipment->save();
        $shipment->load('vehicle');

        if ($shouldCreateTracking) {
            $shipment->shipmentTrackings()->create([
                'status' => $validated['status'],
                'location' => $validated['tracking_location'] ?: $this->defaultTrackingLocation($shipment),
                'description' => $validated['tracking_description'] ?: 'Status shipment diperbarui oleh admin.',
                'tracked_at' => now(),
            ]);
        }

        return redirect()->route('admin.shipments.show', $shipment)->with('success', 'Shipment berhasil diperbarui.');
    }
}
