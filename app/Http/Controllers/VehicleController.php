<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(): View
    {
        return view('vehicles.index', [
            'vehicles' => Vehicle::query()->latest()->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:50', 'unique:vehicles,plate_number'],
            'vehicle_type' => ['required', 'string', 'max:100'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        Vehicle::create($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:50', Rule::unique('vehicles', 'plate_number')->ignore($vehicle->id)],
            'vehicle_type' => ['required', 'string', 'max:100'],
            'driver_name' => ['required', 'string', 'max:255'],
            'driver_phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'max:50'],
        ]);

        $vehicle->update($validated);

        return redirect()->route('admin.vehicles.index')->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->shipments()->exists()) {
            return redirect()->route('admin.vehicles.index')->with('error', 'Kendaraan tidak bisa dihapus karena sudah dipakai shipment.');
        }

        $vehicle->delete();

        return redirect()->route('admin.vehicles.index')->with('success', 'Kendaraan berhasil dihapus.');
    }
}
