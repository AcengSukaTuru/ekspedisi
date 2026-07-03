<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\ShipmentAssignment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ShipmentAssignmentController extends Controller
{
    /**
     * Assign courier and vehicle to shipment
     */
    public function assign(Request $request, Shipment $shipment): RedirectResponse
    {
        Gate::authorize('assign', ShipmentAssignment::class);

        $validated = $request->validate([
            'courier_id' => ['required', 'exists:users,id', Rule::exists('users', 'id')->where('role', User::ROLE_COURIER)],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($shipment, $validated, $request) {
            // Cancel any existing active assignment
            ShipmentAssignment::where('shipment_id', $shipment->id)
                ->where('status', ShipmentAssignment::STATUS_ACTIVE)
                ->update([
                    'status' => ShipmentAssignment::STATUS_CANCELLED,
                    'cancelled_at' => now(),
                    'notes' => 'Auto-cancelled due to new assignment',
                ]);

            // Create new assignment
            ShipmentAssignment::create([
                'shipment_id' => $shipment->id,
                'courier_id' => $validated['courier_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'assigned_by' => $request->user()->id,
                'status' => ShipmentAssignment::STATUS_ACTIVE,
                'assigned_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update shipment vehicle_id for backward compatibility
            $shipment->update(['vehicle_id' => $validated['vehicle_id']]);

            // Create tracking entry
            $courier = User::find($validated['courier_id']);
            $vehicle = Vehicle::find($validated['vehicle_id']);

            $shipment->shipmentTrackings()->create([
                'status' => $shipment->status,
                'location' => $shipment->originBranch?->city ?? 'Warehouse',
                'description' => "Shipment ditugaskan ke kurir {$courier->name} dengan kendaraan {$vehicle->plate_number}",
                'tracked_at' => now(),
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()
            ->route('admin.shipments.show', $shipment)
            ->with('success', 'Kurir dan kendaraan berhasil ditugaskan ke shipment.');
    }

    /**
     * Reassign shipment to different courier/vehicle
     */
    public function reassign(Request $request, ShipmentAssignment $assignment): RedirectResponse
    {
        Gate::authorize('reassign', $assignment);

        if ($assignment->status !== ShipmentAssignment::STATUS_ACTIVE) {
            return redirect()
                ->back()
                ->with('error', 'Hanya assignment yang aktif yang bisa direassign.');
        }

        $validated = $request->validate([
            'courier_id' => ['required', 'exists:users,id', Rule::exists('users', 'id')->where('role', User::ROLE_COURIER)],
            'vehicle_id' => ['required', 'exists:vehicles,id'],
            'notes' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($assignment, $validated, $request) {
            $shipment = $assignment->shipment;

            // Cancel current assignment
            $assignment->update([
                'status' => ShipmentAssignment::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'notes' => $validated['notes'],
            ]);

            // Create new assignment
            ShipmentAssignment::create([
                'shipment_id' => $shipment->id,
                'courier_id' => $validated['courier_id'],
                'vehicle_id' => $validated['vehicle_id'],
                'assigned_by' => $request->user()->id,
                'status' => ShipmentAssignment::STATUS_ACTIVE,
                'assigned_at' => now(),
                'notes' => 'Reassigned from previous courier',
            ]);

            // Update shipment vehicle_id for backward compatibility
            $shipment->update(['vehicle_id' => $validated['vehicle_id']]);

            // Create tracking entry
            $courier = User::find($validated['courier_id']);
            $vehicle = Vehicle::find($validated['vehicle_id']);

            $shipment->shipmentTrackings()->create([
                'status' => $shipment->status,
                'location' => $shipment->originBranch?->city ?? 'Warehouse',
                'description' => "Shipment direassign ke kurir {$courier->name} dengan kendaraan {$vehicle->plate_number}. Alasan: {$validated['notes']}",
                'tracked_at' => now(),
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()
            ->route('admin.shipments.show', $assignment->shipment)
            ->with('success', 'Shipment berhasil direassign ke kurir lain.');
    }

    /**
     * Cancel assignment
     */
    public function cancel(Request $request, ShipmentAssignment $assignment): RedirectResponse
    {
        Gate::authorize('cancel', $assignment);

        if ($assignment->status !== ShipmentAssignment::STATUS_ACTIVE) {
            return redirect()
                ->back()
                ->with('error', 'Hanya assignment yang aktif yang bisa dicancel.');
        }

        $validated = $request->validate([
            'notes' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($assignment, $validated, $request) {
            $assignment->update([
                'status' => ShipmentAssignment::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'notes' => $validated['notes'],
            ]);

            // Create tracking entry
            $assignment->shipment->shipmentTrackings()->create([
                'status' => $assignment->shipment->status,
                'location' => $assignment->shipment->originBranch?->city ?? 'Warehouse',
                'description' => "Assignment dibatalkan. Alasan: {$validated['notes']}",
                'tracked_at' => now(),
                'created_by' => $request->user()->id,
            ]);
        });

        return redirect()
            ->route('admin.shipments.show', $assignment->shipment)
            ->with('success', 'Assignment berhasil dibatalkan.');
    }

    /**
     * Complete assignment (for courier or admin)
     */
    public function complete(Request $request, ShipmentAssignment $assignment): RedirectResponse
    {
        Gate::authorize('complete', $assignment);

        if ($assignment->status !== ShipmentAssignment::STATUS_ACTIVE) {
            return redirect()
                ->back()
                ->with('error', 'Hanya assignment yang aktif yang bisa dicomplete.');
        }

        $assignment->update([
            'status' => ShipmentAssignment::STATUS_COMPLETED,
            'completed_at' => now(),
        ]);

        $redirectRoute = $request->user()->isAdmin()
            ? route('admin.shipments.show', $assignment->shipment)
            : route('courier.shipments.show', $assignment->shipment);

        return redirect($redirectRoute)
            ->with('success', 'Assignment berhasil diselesaikan.');
    }
}
