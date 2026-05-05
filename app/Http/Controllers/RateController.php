<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Rate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RateController extends Controller
{
    public function index(): View
    {
        return view('rates.index', [
            'rates' => Rate::query()
                ->with(['originBranch', 'destinationBranch'])
                ->latest()
                ->paginate(10),
            'branches' => Branch::query()->orderBy('branch_name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRate($request);

        Rate::create($validated);

        return redirect()->route('admin.rates.index')->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function edit(Rate $rate): View
    {
        return view('rates.edit', [
            'rate' => $rate->load(['originBranch', 'destinationBranch']),
            'branches' => Branch::query()->orderBy('branch_name')->get(),
        ]);
    }

    public function update(Request $request, Rate $rate): RedirectResponse
    {
        $validated = $this->validateRate($request, $rate);

        $rate->update($validated);

        return redirect()->route('admin.rates.index')->with('success', 'Tarif berhasil diperbarui.');
    }

    public function destroy(Rate $rate): RedirectResponse
    {
        $rate->delete();

        return redirect()->route('admin.rates.index')->with('success', 'Tarif berhasil dihapus.');
    }

    private function validateRate(Request $request, ?Rate $rate = null): array
    {
        $validated = $request->validate([
            'origin_branch_id' => ['required', 'exists:branches,id'],
            'destination_branch_id' => ['required', 'exists:branches,id', 'different:origin_branch_id'],
            'price_per_kg' => ['required', 'numeric', 'min:1'],
            'service_type' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rates', 'service_type')
                    ->where(fn ($query) => $query
                        ->where('origin_branch_id', $request->input('origin_branch_id'))
                        ->where('destination_branch_id', $request->input('destination_branch_id')))
                    ->ignore($rate?->id),
            ],
        ]);

        return $validated;
    }
}
