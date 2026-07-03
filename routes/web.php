<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryAttemptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\ShipmentAssignmentController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('home');

Route::get('/tracking', [TrackingController::class, 'publicIndex'])
    ->middleware('throttle:30,1')
    ->name('tracking.public');
Route::get('/tracking/{trackingNumber}', [TrackingController::class, 'publicShow'])
    ->middleware('throttle:30,1')
    ->name('tracking.public.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Admin Routes ─────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [DashboardController::class, 'admin'])->name('dashboard');
        Route::resource('branches', BranchController::class)->except(['create', 'show']);
        Route::resource('vehicles', VehicleController::class)->except(['create', 'show']);
        Route::resource('rates', RateController::class)->except(['create', 'show']);

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
        Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

        Route::get('shipments', [ShipmentController::class, 'adminIndex'])->name('shipments.index');
        Route::get('shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
        Route::get('shipments/{shipment}/invoice', [ShipmentController::class, 'invoice'])->name('shipments.invoice');
        Route::get('shipments/{shipment}/label', [ShipmentController::class, 'label'])->name('shipments.label');
        Route::patch('shipments/{shipment}/status', [ShipmentController::class, 'updateStatus'])->name('shipments.update-status');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::patch('payments/{payment}/verify', [PaymentController::class, 'verifyByAdmin'])->name('payments.verify');

        // Shipment Assignment Routes
        Route::post('shipments/{shipment}/assign', [ShipmentAssignmentController::class, 'assign'])->name('shipments.assign');
        Route::patch('assignments/{assignment}/reassign', [ShipmentAssignmentController::class, 'reassign'])->name('assignments.reassign');
        Route::patch('assignments/{assignment}/cancel', [ShipmentAssignmentController::class, 'cancel'])->name('assignments.cancel');
        Route::patch('assignments/{assignment}/complete', [ShipmentAssignmentController::class, 'complete'])->name('assignments.complete');

        // Delivery Attempt: admin reschedule
        Route::patch('shipments/{shipment}/reschedule', [DeliveryAttemptController::class, 'reschedule'])->name('shipments.reschedule');
    });

    // ─── Customer Routes ──────────────────────────────────────
    Route::prefix('customer')->name('customer.')->middleware('role:customer')->group(function () {
        Route::get('/', [DashboardController::class, 'customer'])->name('dashboard');

        Route::get('shipments', [ShipmentController::class, 'customerIndex'])->name('shipments.index');
        Route::get('shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('shipments', [ShipmentController::class, 'store'])->name('shipments.store');
        Route::get('shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
        Route::get('shipments/{shipment}/invoice', [ShipmentController::class, 'invoice'])->name('shipments.invoice');
        Route::get('shipments/{shipment}/label', [ShipmentController::class, 'label'])->name('shipments.label');

        Route::get('trackings/{shipment}', [TrackingController::class, 'index'])->name('trackings.index');
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::patch('payments/{payment}/submit', [PaymentController::class, 'submitByCustomer'])->name('payments.submit');
    });

    // ─── Courier Routes ───────────────────────────────────────
    Route::prefix('courier')->name('courier.')->middleware('role:courier')->group(function () {
        Route::get('/', [DashboardController::class, 'courier'])->name('dashboard');

        Route::get('shipments', [ShipmentController::class, 'courierIndex'])->name('shipments.index');
        Route::get('shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');

        Route::get('trackings/{shipment}', [TrackingController::class, 'index'])->name('trackings.index');
        Route::get('trackings/{shipment}/create', [TrackingController::class, 'create'])->name('trackings.create');
        Route::post('trackings/{shipment}', [TrackingController::class, 'store'])->name('trackings.store');

        // Delivery Attempt routes (kurir)
        Route::get('shipments/{shipment}/delivery-attempt', [DeliveryAttemptController::class, 'create'])->name('delivery-attempts.create');
        Route::post('shipments/{shipment}/delivery-attempt', [DeliveryAttemptController::class, 'store'])->name('delivery-attempts.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
