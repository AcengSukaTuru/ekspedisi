<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('customers.index', [
            'customers' => Customer::query()->with('user')->latest()->paginate(10),
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customer->id),
                Rule::unique('users', 'email')->ignore($customer->user_id),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($customer, $validated) {
            $customer->update($validated);

            if ($customer->user) {
                $customer->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
            }
        });

        return redirect()->route('admin.customers.index')->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->shipments()->exists()) {
            return redirect()->route('admin.customers.index')->with('error', 'Customer tidak bisa dihapus karena sudah memiliki shipment.');
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
