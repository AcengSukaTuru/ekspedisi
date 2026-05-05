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
        $stats = [
            ['label' => 'Total Shipment', 'value' => Shipment::count()],
            ['label' => 'Shipment Pending', 'value' => Shipment::where('status', Shipment::STATUS_PENDING)->count()],
            ['label' => 'Shipment Delivered', 'value' => Shipment::where('status', Shipment::STATUS_DELIVERED)->count()],
            ['label' => 'Payment Pending', 'value' => Payment::where('payment_status', Payment::STATUS_PENDING)->count()],
        ];

        $recentShipments = Shipment::query()
            ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', [
            'title' => 'Dashboard Admin',
            'subtitle' => 'Kelola seluruh data ekspedisi dari satu tempat.',
            'stats' => $stats,
            'recentShipments' => $recentShipments,
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

    public function courier(): View
    {
        $shipmentQuery = Shipment::query()->whereNotNull('vehicle_id');

        $stats = [
            ['label' => 'Tugas Aktif', 'value' => (clone $shipmentQuery)->where('status', '!=', Shipment::STATUS_DELIVERED)->count()],
            ['label' => 'Dalam Perjalanan', 'value' => (clone $shipmentQuery)->where('status', Shipment::STATUS_IN_TRANSIT)->count()],
            ['label' => 'Sudah Selesai', 'value' => (clone $shipmentQuery)->where('status', Shipment::STATUS_DELIVERED)->count()],
            ['label' => 'Semua Tugas', 'value' => (clone $shipmentQuery)->count()],
        ];

        $recentShipments = Shipment::query()
            ->with(['customer', 'originBranch', 'destinationBranch', 'vehicle'])
            ->whereNotNull('vehicle_id')
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
