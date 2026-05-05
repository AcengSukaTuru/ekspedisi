<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithShipments;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    use InteractsWithShipments;

    public function index(Request $request, Shipment $shipment): View
    {
        $this->authorizeShipmentAccess($request->user(), $shipment);

        $shipment->load([
            'customer',
            'originBranch',
            'destinationBranch',
            'vehicle',
            'shipmentTrackings' => fn ($query) => $query->latest('tracked_at'),
        ]);

        return view('trackings.index', [
            'shipment' => $shipment,
            'canCreate' => $request->user()->isCourier(),
            'createRoute' => route('courier.trackings.create', $shipment),
        ]);
    }

    public function create(Shipment $shipment): View
    {
        abort_unless($shipment->vehicle_id !== null, 403, 'Shipment ini belum memiliki kendaraan.');

        return view('trackings.create', [
            'shipment' => $shipment->load(['customer', 'originBranch', 'destinationBranch', 'vehicle']),
        ]);
    }

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        abort_unless($shipment->vehicle_id !== null, 403, 'Shipment ini belum memiliki kendaraan.');

        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tracked_at' => ['required', 'date'],
        ]);

        $shipment->shipmentTrackings()->create($validated);
        $shipment->update(['status' => $validated['status']]);

        return redirect()->route('courier.shipments.show', $shipment)->with('success', 'Tracking berhasil ditambahkan.');
    }
}
