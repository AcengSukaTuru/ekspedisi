<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithShipments;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use InteractsWithShipments;

    public function index(Request $request): RedirectResponse
    {
        return redirect()->route($request->user()->dashboardRouteName());
    }

    public function admin(): View
    {
        $totalShipments = Shipment::count();
        $statusCounts = Shipment::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            ['label' => 'Total Shipment', 'value' => $totalShipments],
            ['label' => 'Shipment Aktif', 'value' => Shipment::whereNotIn('status', [Shipment::STATUS_DELIVERED, Shipment::STATUS_RETURNED_TO_SENDER])->count()],
            ['label' => 'Sudah Sampai', 'value' => Shipment::where('status', Shipment::STATUS_DELIVERED)->count()],
            ['label' => 'Gagal / RTS', 'value' => Shipment::whereIn('status', [Shipment::STATUS_FAILED_DELIVERY, Shipment::STATUS_RETURNED_TO_SENDER])->count()],
            ['label' => 'Payment Pending', 'value' => Payment::where('payment_status', Payment::STATUS_PENDING)->count()],
            ['label' => 'Revenue Paid', 'value' => 'Rp '.number_format((float) Payment::where('payment_status', Payment::STATUS_PAID)->sum('amount'), 0, ',', '.')],
        ];

        $recentShipments = Shipment::query()
            ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle', 'activeAssignment.courier', 'activeAssignment.vehicle'])
            ->latest()
            ->take(6)
            ->get();

        $recentPayments = Payment::query()
            ->with(['shipment.customer'])
            ->latest()
            ->take(5)
            ->get();

        $attentionShipments = Shipment::query()
            ->with(['customer', 'originBranch', 'destinationBranch', 'payment', 'activeAssignment'])
            ->where(function ($query) {
                $query->whereIn('status', [Shipment::STATUS_CREATED, Shipment::STATUS_FAILED_DELIVERY])
                    ->orWhereDoesntHave('activeAssignment')
                    ->orWhereHas('payment', fn ($payment) => $payment->where('payment_status', Payment::STATUS_PENDING));
            })
            ->latest()
            ->take(5)
            ->get();

        $statusDistribution = collect(Shipment::statuses())
            ->map(fn ($status) => [
                'status' => $status,
                'label' => Shipment::statusLabels()[$status] ?? $status,
                'count' => (int) ($statusCounts[$status] ?? 0),
                'percentage' => $totalShipments > 0 ? round(((int) ($statusCounts[$status] ?? 0) / $totalShipments) * 100) : 0,
            ]);

        return view('dashboard.admin', [
            'title' => 'Dashboard Admin',
            'subtitle' => 'Kelola shipment, payment, kurir, dan performa operasional dari satu control center.',
            'stats' => $stats,
            'recentShipments' => $recentShipments,
            'recentPayments' => $recentPayments,
            'attentionShipments' => $attentionShipments,
            'statusDistribution' => $statusDistribution,
            'role' => User::ROLE_ADMIN,
        ]);
    }

    public function customer(Request $request): View
    {
        $customer = $this->currentCustomer($request->user());

        $shipmentQuery = Shipment::query()->where('customer_id', $customer->id);
        $paymentQuery = Payment::query()->whereHas('shipment', function ($query) use ($customer) {
            $query->where('customer_id', $customer->id);
        });

        $stats = [
            ['label' => 'Shipment Saya', 'value' => (clone $shipmentQuery)->count()],
            ['label' => 'Masih Diproses', 'value' => (clone $shipmentQuery)->where('status', '!=', Shipment::STATUS_DELIVERED)->count()],
            ['label' => 'Sudah Sampai', 'value' => (clone $shipmentQuery)->where('status', Shipment::STATUS_DELIVERED)->count()],
            ['label' => 'Pembayaran Pending', 'value' => (clone $paymentQuery)->where('payment_status', Payment::STATUS_PENDING)->count()],
        ];

        $recentShipments = Shipment::query()
            ->with(['originBranch', 'destinationBranch', 'vehicle'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'title' => 'Dashboard Customer',
            'subtitle' => 'Buat shipment baru dan pantau status pengiriman Anda.',
            'stats' => $stats,
            'recentShipments' => $recentShipments,
            'role' => User::ROLE_CUSTOMER,
        ]);
    }

    public function courier(Request $request): View
    {
        $courierId = $request->user()->id;
        $shipmentQuery = Shipment::query()->whereHas('activeAssignment', fn ($q) => $q->where('courier_id', $courierId));

        $stats = [
            ['label' => 'Tugas Aktif', 'value' => (clone $shipmentQuery)->whereNotIn('status', [Shipment::STATUS_DELIVERED, Shipment::STATUS_RETURNED_TO_SENDER])->count()],
            ['label' => 'Dalam Perjalanan', 'value' => (clone $shipmentQuery)->where('status', Shipment::STATUS_IN_TRANSIT)->count()],
            ['label' => 'Gagal Dikirim', 'value' => (clone $shipmentQuery)->where('status', Shipment::STATUS_FAILED_DELIVERY)->count()],
            ['label' => 'Sudah Selesai', 'value' => (clone $shipmentQuery)->where('status', Shipment::STATUS_DELIVERED)->count()],
        ];

        $recentShipments = Shipment::query()
            ->with(['customer', 'originBranch', 'destinationBranch', 'activeAssignment.vehicle'])
            ->whereHas('activeAssignment', fn ($q) => $q->where('courier_id', $courierId))
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'title' => 'Dashboard Kurir',
            'subtitle' => 'Lihat tugas pengiriman dan update tracking terbaru.',
            'stats' => $stats,
            'recentShipments' => $recentShipments,
            'role' => User::ROLE_COURIER,
        ]);
    }
}
