<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithShipments;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use InteractsWithShipments;

    public function index(Request $request): View
    {
        $query = Payment::query()->with(['shipment.customer', 'shipment.originBranch', 'shipment.destinationBranch']);

        $title = 'Semua Pembayaran';
        $role = $request->user()->role;

        if ($request->user()->isCustomer()) {
            $customer = $this->currentCustomer($request->user());
            $query->whereHas('shipment', fn ($shipmentQuery) => $shipmentQuery->where('customer_id', $customer->id));
            $title = 'Pembayaran Saya';
        }

        return view('payments.index', [
            'payments' => $query->latest('created_at')->paginate(10),
            'title' => $title,
            'role' => $role,
        ]);
    }

    public function show(Request $request, Payment $payment): View
    {
        Gate::authorize('view', $payment);
        $payment->load(['shipment.customer', 'shipment.originBranch', 'shipment.destinationBranch', 'verifier']);

        return view('payments.show', [
            'payment' => $payment,
            'role' => $request->user()->role,
            'title' => $request->user()->isAdmin() ? 'Detail Pembayaran' : 'Detail Pembayaran Saya',
            'backRoute' => $request->user()->isAdmin() ? 'admin.payments.index' : 'customer.payments.index',
            'shipmentRoute' => $request->user()->isAdmin()
                ? route('admin.shipments.show', $payment->shipment)
                : route('customer.shipments.show', $payment->shipment),
        ]);
    }

    public function submitByCustomer(Request $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('update', $payment);

        if ($payment->payment_status === Payment::STATUS_PAID) {
            return redirect()
                ->route('customer.payments.show', $payment)
                ->with('error', 'Pembayaran ini sudah diverifikasi paid dan tidak dapat diubah.');
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', Rule::in(['cash', 'transfer', 'e-wallet'])],
            'proof_file' => [
                Rule::requiredIf(fn () => in_array($request->input('payment_method'), ['transfer', 'e-wallet'], true) && ! $payment->proof_of_payment),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:2048',
            ],
        ]);

        $proofPath = $payment->proof_of_payment;

        if ($request->hasFile('proof_file')) {
            if ($proofPath) {
                Storage::disk('public')->delete($proofPath);
            }

            $proofPath = $request->file('proof_file')->store('payment-proofs', 'public');
        }

        if ($validated['payment_method'] === 'cash') {
            if ($proofPath) {
                Storage::disk('public')->delete($proofPath);
            }

            $proofPath = null;
        }

        $payment->update([
            'payment_method' => $validated['payment_method'],
            'payment_date' => now()->toDateString(),
            'payment_status' => Payment::STATUS_PENDING,
            'proof_of_payment' => $proofPath,
            'verified_at' => null,
            'verified_by' => null,
            'admin_note' => null,
        ]);

        return redirect()
            ->route('customer.payments.show', $payment)
            ->with('success', 'Konfirmasi pembayaran berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function verifyByAdmin(Request $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('verify', $payment);

        $validated = $request->validate([
            'payment_status' => ['required', 'string', Rule::in(Payment::statuses())],
            'admin_note' => ['nullable', 'string'],
        ]);

        $attributes = [
            'payment_status' => $validated['payment_status'],
            'admin_note' => $validated['admin_note'] ?? null,
        ];

        if ($validated['payment_status'] === Payment::STATUS_PENDING) {
            $attributes['verified_at'] = null;
            $attributes['verified_by'] = null;
        } else {
            $attributes['verified_at'] = now();
            $attributes['verified_by'] = $request->user()->id;
        }

        $payment->update($attributes);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }

}
