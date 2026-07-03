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
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    use InteractsWithShipments;

    public function adminIndex(): View
    {
        return view('shipments.index', [
            'shipments' => Shipment::query()
                ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle', 'payment', 'activeAssignment.courier'])
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
                ->with(['originBranch', 'destinationBranch', 'vehicle', 'payment', 'activeAssignment.courier'])
                ->where('customer_id', $customer->id)
                ->latest()
                ->paginate(10),
            'title' => 'Shipment Saya',
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function courierIndex(Request $request): View
    {
        return view('shipments.index', [
            'shipments' => Shipment::query()
                ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle', 'payment', 'activeAssignment.vehicle'])
                ->whereHas('activeAssignment', function ($q) use ($request) {
                    $q->where('courier_id', $request->user()->id);
                })
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
            // Pickup type
            'pickup_type' => ['nullable', 'string', 'in:drop_off,pickup_request'],
            'pickup_address' => ['required_if:pickup_type,pickup_request', 'nullable', 'string'],
            'pickup_contact_name' => ['nullable', 'string', 'max:255'],
            'pickup_contact_phone' => ['nullable', 'string', 'max:20'],
            // Payment type
            'payment_type' => ['nullable', 'string', 'in:prepaid,cod'],
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
        Gate::authorize('view', $shipment);

        $shipment->load([
            'customer.user',
            'originBranch',
            'destinationBranch',
            'vehicle',
            'shipmentItems',
            'payment.collectedBy',
            'activeAssignment.courier',
            'activeAssignment.vehicle',
            'assignments.courier',
            'assignments.vehicle',
            'assignments.assignedBy',
            'deliveryAttempts.attemptedBy',
            'shipmentTrackings' => fn ($query) => $query->latest('tracked_at'),
        ]);

        return view('shipments.show', [
            'shipment' => $shipment,
            'statusLabels' => Shipment::statusLabels(),
            'statusColors' => Shipment::statusColors(),
            'availableVehicles' => $request->user()->isAdmin()
                ? Vehicle::query()->orderBy('plate_number')->get()
                : collect(),
            'availableCouriers' => $request->user()->isAdmin()
                ? User::where('role', User::ROLE_COURIER)->orderBy('name')->get()
                : collect(),
            'backRoute' => match (true) {
                $request->user()->isAdmin() => 'admin.shipments.index',
                $request->user()->isCourier() => 'courier.shipments.index',
                default => 'customer.shipments.index',
            },
        ]);
    }

    public function invoice(Request $request, Shipment $shipment): View
    {
        Gate::authorize('view', $shipment);

        $shipment->load([
            'customer.user',
            'originBranch',
            'destinationBranch',
            'shipmentItems',
            'payment',
        ]);

        return view('shipments.invoice', [
            'shipment' => $shipment,
        ]);
    }

    public function label(Request $request, Shipment $shipment): View
    {
        Gate::authorize('view', $shipment);

        $shipment->load([
            'originBranch',
            'destinationBranch',
            'shipmentItems',
        ]);

        return view('shipments.label', [
            'shipment' => $shipment,
        ]);
    }

    public function updateStatus(Request $request, Shipment $shipment): RedirectResponse
    {
        Gate::authorize('updateStatus', $shipment);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', Shipment::statuses())],
            'estimated_arrival' => ['nullable', 'date'],
            'tracking_location' => ['nullable', 'string', 'max:255'],
            'tracking_description' => ['nullable', 'string'],
        ]);

        $newStatus = $validated['status'];

        // Validasi transisi status
        if (! $shipment->canTransitionTo($newStatus)) {
            return redirect()
                ->back()
                ->with('error', "Status tidak bisa diubah dari '{$shipment->status}' ke '{$newStatus}'. Status berikutnya yang valid: ".implode(', ', $shipment->validNextStatuses()).'.');
        }

        $shipment->loadMissing(['originBranch', 'activeAssignment.vehicle']);

        $oldStatus = $shipment->status;

        $shipment->fill([
            'status' => $newStatus,
            'estimated_arrival' => $validated['estimated_arrival'] ?? $shipment->estimated_arrival,
        ]);

        $shipment->save();

        // Tracking entry
        $defaultDesc = Shipment::statusLabels()[$newStatus] ?? 'Status diperbarui oleh admin.';
        $shipment->shipmentTrackings()->create([
            'status' => $newStatus,
            'location' => $validated['tracking_location'] ?: $this->defaultTrackingLocation($shipment),
            'description' => $validated['tracking_description'] ?: $defaultDesc,
            'tracked_at' => now(),
            'created_by' => $request->user()->id,
        ]);

        // Auto-complete assignment if delivered
        if ($newStatus === Shipment::STATUS_DELIVERED) {
            $this->completeActiveAssignment($shipment);
        }

        return redirect()
            ->route('admin.shipments.show', $shipment)
            ->with('success', "Status berhasil diubah dari '{$oldStatus}' ke '".(Shipment::statusLabels()[$newStatus] ?? $newStatus)."'.");
    }

    /**
     * Helper: complete active assignment
     */
    private function completeActiveAssignment(Shipment $shipment): void
    {
        $activeAssignment = $shipment->activeAssignment;
        if ($activeAssignment && $activeAssignment->status === 'active') {
            $activeAssignment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}
