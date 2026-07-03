<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithShipments;
use App\Models\DeliveryAttempt;
use App\Models\Shipment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DeliveryAttemptController extends Controller
{
    use InteractsWithShipments;
    /**
     * Form kurir catat delivery attempt (success atau failed)
     */
    public function create(Shipment $shipment): View
    {
        Gate::authorize('view', $shipment);

        $assignment = $shipment->activeAssignment;
        abort_unless($assignment !== null, 403, 'Shipment belum memiliki assignment kurir.');
        abort_unless($shipment->status === Shipment::STATUS_OUT_FOR_DELIVERY, 403, 'Shipment belum dalam status "Sedang Diantar".');

        $attemptNumber = $shipment->deliveryAttempts()->count() + 1;

        return view('delivery-attempts.create', [
            'shipment' => $shipment->load(['customer', 'originBranch', 'destinationBranch', 'activeAssignment.courier', 'activeAssignment.vehicle']),
            'attemptNumber' => $attemptNumber,
            'failureReasons' => DeliveryAttempt::failureReasons(),
            'maxAttempts' => $shipment->max_delivery_attempts,
            'failedCount' => $shipment->failedAttemptCount(),
        ]);
    }

    /**
     * Simpan delivery attempt
     */
    public function store(Request $request, Shipment $shipment): RedirectResponse
    {
        Gate::authorize('view', $shipment);

        $assignment = $shipment->activeAssignment;
        abort_unless($assignment !== null, 403, 'Shipment belum memiliki assignment kurir.');
        abort_unless($shipment->status === Shipment::STATUS_OUT_FOR_DELIVERY, 403, 'Shipment belum dalam status "Sedang Diantar".');

        $validated = $request->validate([
            'attempt_status' => ['required', 'string', 'in:success,failed'],
            'failure_reason' => ['required_if:attempt_status,failed', 'nullable', 'string', 'in:'.implode(',', array_keys(DeliveryAttempt::failureReasons()))],
            'notes' => ['nullable', 'string'],
            'proof_photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('delivery-proofs', 'public');
        }

        $attemptNumber = $shipment->deliveryAttempts()->count() + 1;
        $isSuccess = $validated['attempt_status'] === 'success';

        DB::transaction(function () use ($shipment, $validated, $request, $attemptNumber, $proofPath, $isSuccess) {
            // Simpan delivery attempt
            $attempt = DeliveryAttempt::create([
                'shipment_id' => $shipment->id,
                'attempted_by' => $request->user()->id,
                'attempt_number' => $attemptNumber,
                'status' => $isSuccess ? DeliveryAttempt::STATUS_SUCCESS : DeliveryAttempt::STATUS_FAILED,
                'failure_reason' => $isSuccess ? null : $validated['failure_reason'],
                'notes' => $validated['notes'] ?? null,
                'proof_path' => $proofPath,
                'attempted_at' => now(),
                'next_retry_at' => $isSuccess ? null : now()->addDay(), // default retry besok
            ]);

            if ($isSuccess) {
                // Paket diterima → delivered
                $shipment->update(['status' => Shipment::STATUS_DELIVERED]);

                $shipment->shipmentTrackings()->create([
                    'status' => Shipment::STATUS_DELIVERED,
                    'location' => $shipment->receiver_address,
                    'description' => "Paket berhasil diterima oleh penerima. (Percobaan ke-{$attemptNumber})",
                    'tracked_at' => now(),
                    'created_by' => $request->user()->id,
                    'proof_path' => $proofPath,
                ]);

                // Complete assignment
                $assignment = $shipment->activeAssignment;
                if ($assignment && $assignment->status === 'active') {
                    $assignment->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);
                }
            } else {
                // Gagal antar
                $failureLabel = DeliveryAttempt::failureReasons()[$validated['failure_reason']] ?? $validated['failure_reason'];

                if ($shipment->shouldReturnToSender()) {
                    // Sudah max attempt → RTS
                    $shipment->update([
                        'status' => Shipment::STATUS_RETURNED_TO_SENDER,
                        'rts_reason' => "Gagal {$shipment->max_delivery_attempts}x percobaan pengiriman. Alasan terakhir: {$failureLabel}",
                    ]);

                    $shipment->shipmentTrackings()->create([
                        'status' => Shipment::STATUS_RETURNED_TO_SENDER,
                        'location' => $shipment->originBranch?->city ?? 'Gudang',
                        'description' => "Paket dikembalikan ke pengirim setelah {$shipment->max_delivery_attempts}x gagal antar. Alasan: {$failureLabel}",
                        'tracked_at' => now(),
                        'created_by' => $request->user()->id,
                    ]);

                    // Complete assignment (cancelled)
                    $assignment = $shipment->activeAssignment;
                    if ($assignment && $assignment->status === 'active') {
                        $assignment->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);
                    }
                } else {
                    // Masih ada sisa attempt → stay di failed_delivery
                    $remaining = $shipment->remainingAttempts();

                    $shipment->shipmentTrackings()->create([
                        'status' => Shipment::STATUS_FAILED_DELIVERY,
                        'location' => $shipment->receiver_address,
                        'description' => "Gagal antar (percobaan ke-{$attemptNumber}). Alasan: {$failureLabel}. Sisa percobaan: {$remaining}.",
                        'tracked_at' => now(),
                        'created_by' => $request->user()->id,
                        'proof_path' => $proofPath,
                    ]);

                    // Update status ke failed_delivery
                    $shipment->update(['status' => Shipment::STATUS_FAILED_DELIVERY]);
                }
            }
        });

        $message = $isSuccess
            ? 'Paket berhasil diterima penerima!'
            : 'Delivery attempt gagal dicatat.';

        return redirect()
            ->route('courier.shipments.show', $shipment)
            ->with('success', $message);
    }

    /**
     * Admin: reschedule delivery (ubah status kembali ke out_for_delivery)
     */
    public function reschedule(Shipment $shipment): RedirectResponse
    {
        Gate::authorize('updateStatus', $shipment);

        abort_unless($shipment->status === Shipment::STATUS_FAILED_DELIVERY, 403, 'Hanya shipment gagal yang bisa direschedule.');
        abort_unless($shipment->canAttemptDelivery(), 403, 'Sudah mencapai batas maks percobaan.');

        $shipment->update(['status' => Shipment::STATUS_OUT_FOR_DELIVERY]);

        $attemptNum = $shipment->deliveryAttempts()->count() + 1;
        $shipment->shipmentTrackings()->create([
            'status' => Shipment::STATUS_OUT_FOR_DELIVERY,
            'location' => $this->defaultTrackingLocation($shipment),
            'description' => "Reschedule pengiriman. Percobaan ke-{$attemptNum}.",
            'tracked_at' => now(),
            'created_by' => request()->user()?->id,
        ]);

        return redirect()
            ->route('admin.shipments.show', $shipment)
            ->with('success', 'Delivery berhasil direschedule.');
    }
}
