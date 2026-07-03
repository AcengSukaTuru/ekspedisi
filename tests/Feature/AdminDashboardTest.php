<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_operational_widgets(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $customer = Customer::create(['user_id' => $customerUser->id, 'name' => 'Customer Demo', 'email' => 'demo@test.test', 'phone' => '081', 'address' => 'Alamat']);
        $origin = Branch::create(['branch_name' => 'Jakarta Hub', 'branch_code' => 'JKT', 'city' => 'Jakarta', 'address' => 'Jakarta']);
        $destination = Branch::create(['branch_name' => 'Bandung Hub', 'branch_code' => 'BDG', 'city' => 'Bandung', 'address' => 'Bandung']);

        $shipment = Shipment::create([
            'tracking_number' => 'EXP-DASH-001',
            'customer_id' => $customer->id,
            'origin_branch_id' => $origin->id,
            'destination_branch_id' => $destination->id,
            'sender_name' => 'S',
            'sender_phone' => '1',
            'sender_address' => 'A',
            'receiver_name' => 'R',
            'receiver_phone' => '2',
            'receiver_address' => 'B',
            'total_weight' => 1,
            'shipping_cost' => 10000,
            'status' => Shipment::STATUS_CREATED,
        ]);
        Payment::create(['shipment_id' => $shipment->id, 'amount' => 10000, 'payment_type' => Payment::TYPE_PREPAID, 'payment_status' => Payment::STATUS_PENDING]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Revenue Paid')
            ->assertSee('Distribusi Status')
            ->assertSee('Butuh Tindakan')
            ->assertSee('Payment Terbaru')
            ->assertSee('EXP-DASH-001');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $customer = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
