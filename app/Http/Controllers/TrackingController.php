<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithShipments;
use App\Models\DeliveryAttempt;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TrackingController extends Controller
{
    use InteractsWithShipments;

    public function publicIndex(Request $request): View|RedirectResponse
    {
        $trackingNumber = trim((string) $request->query('tracking_number', ''));

        if ($trackingNumber !== '') {
            $request->validate([
                'tracking_number' => ['required', 'string', 'max:50'],
            ]);

            return redirect()->route('tracking.public.show', ['trackingNumber' => $trackingNumber]);
        }

        return view('trackings.public', [
            'shipment' => null,
            'trackingNumber' => null,
            'notFound' => false,
            'statusLabels' => Shipment::statusLabels(),
            'statusColors' => Shipment::statusColors(),
        ]);
    }

    public function publicShow(string $trackingNumber): View
    {
        $shipment = Shipment::query()
            ->where('tracking_number', $trackingNumber)
            ->with([
                'originBranch',
                'destinationBranch',
                'activeAssignment.vehicle',
                'shipmentTrackings' => fn ($query) => $query->oldest('tracked_at'),
            ])
            ->first();

        return view('trackings.public', [
            'shipment' => $shipment,
            'trackingNumber' => $trackingNumber,
            'notFound' => $shipment === null,
            'statusLabels' => Shipment::statusLabels(),
            'statusColors' => Shipment::statusColors(),
        ]);
    }

    public function index(Request $request, Shipment $shipment): View
    {
        Gate::authorize('view', $shipment);

        $shipment->load([
            'customer',
            'originBranch',
            'destinationBranch',
            'vehicle',
            'activeAssignment.courier',
            'activeAssignment.vehicle',
            'deliveryAttempts.attemptedBy',
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
        Gate::authorize('view', $shipment);

        $assignment = $shipment->activeAssignment;
        abort_unless($assignment !== null, 403, 'Shipment ini belum memiliki assignment kurir.');

        return view('trackings.create', [
            'shipment' => $shipment->load(['customer', 'originBranch', 'destinationBranch', 'activeAssignment.vehicle', 'activeAssignment.courier']),
            'validStatuses' => $shipment->validNextStatuses(),
            'statusLabels' => Shipment::statusLabels(),
        ]);
    }

    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        Gate::authorize('view', $shipment);

        $assignment = $shipment->activeAssignment;
        abort_unless($assignment !== null, 403, 'Shipment ini belum memiliki assignment kurir.');

        $validStatuses = $shipment->validNextStatuses();

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', $validStatuses)],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tracked_at' => ['required', 'date'],
            'proof_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('tracking-proofs', 'public');
        }

        $newStatus = $validated['status'];

        $shipment->shipmentTrackings()->create([
            'status' => $newStatus,
            'location' => $validated['location'],
            'description' => $validated['description'] ?? (Shipment::statusLabels()[$newStatus] ?? ''),
            'tracked_at' => $validated['tracked_at'],
            'created_by' => $request->user()->id,
            'proof_path' => $proofPath,
        ]);

        // Update shipment status
        $shipment->update(['status' => $newStatus]);

        // Auto-complete assignment if delivered
        if ($newStatus === Shipment::STATUS_DELIVERED && $assignment->status === 'active') {
            $assignment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $label = Shipment::statusLabels()[$newStatus] ?? $newStatus;

        return redirect()
            ->route('courier.shipments.show', $shipment)
            ->with('success', "Tracking berhasil ditambahkan. Status: {$label}");
    }
}
