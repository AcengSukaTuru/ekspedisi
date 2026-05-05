<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function index(): View
    {
        return view('branches.index', [
            'branches' => Branch::query()->latest()->paginate(10),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function edit(Branch $branch): View
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'branch_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch): RedirectResponse
    {
        $hasDependencies = $branch->originShipments()->exists()
            || $branch->destinationShipments()->exists()
            || $branch->originRates()->exists()
            || $branch->destinationRates()->exists();

        if ($hasDependencies) {
            return redirect()->route('admin.branches.index')->with('error', 'Cabang tidak bisa dihapus karena sudah dipakai.');
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
