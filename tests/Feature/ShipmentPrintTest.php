<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShipmentPrintTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customerUser;
    private User $otherCustomerUser;
    private Customer $customer;
    private Customer $otherCustomer;
    private Branch $origin;
    private Branch $destination;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $this->otherCustomerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);

        $this->customer = Customer::create(['user_id' => $this->customerUser->id, 'name' => 'Customer A', 'email' => 'a@test.test', 'phone' => '081', 'address' => 'A']);
        $this->otherCustomer = Customer::create(['user_id' => $this->otherCustomerUser->id, 'name' => 'Customer B', 'email' => 'b@test.test', 'phone' => '082', 'address' => 'B']);

        $this->origin = Branch::create(['branch_name' => 'Jakarta Hub', 'branch_code' => 'JKT', 'city' => 'Jakarta', 'address' => 'Jakarta']);
        $this->destination = Branch::create(['branch_name' => 'Bandung Hub', 'branch_code' => 'BDG', 'city' => 'Bandung', 'address' => 'Bandung']);
    }

    public function test_admin_can_print_invoice_and_label(): void
    {
        $shipment = $this->createShipment($this->customer, 'EXP-PRINT-ADMIN');

        $this->actingAs($this->admin)
            ->get(route('admin.shipments.invoice', $shipment))
            ->assertOk()
            ->assertSee('INV-'.$shipment->tracking_number)
            ->assertSee('Rp 25.000', false);

        $this->actingAs($this->admin)
            ->get(route('admin.shipments.label', $shipment))
            ->assertOk()
            ->assertSee('Shipping Label')
            ->assertSee($shipment->receiver_name);
    }

    public function test_customer_can_print_own_invoice_and_label(): void
    {
        $shipment = $this->createShipment($this->customer, 'EXP-PRINT-OWN');

        $this->actingAs($this->customerUser)
            ->get(route('customer.shipments.invoice', $shipment))
            ->assertOk()
            ->assertSee('INV-'.$shipment->tracking_number);

        $this->actingAs($this->customerUser)
            ->get(route('customer.shipments.label', $shipment))
            ->assertOk()
            ->assertSee($shipment->tracking_number);
    }

    public function test_customer_cannot_print_other_customer_shipment(): void
    {
        $shipment = $this->createShipment($this->otherCustomer, 'EXP-PRINT-OTHER');

        $this->actingAs($this->customerUser)
            ->get(route('customer.shipments.invoice', $shipment))
            ->assertForbidden();

        $this->actingAs($this->customerUser)
            ->get(route('customer.shipments.label', $shipment))
            ->assertForbidden();
    }

    public function test_guest_cannot_access_print_pages(): void
    {
        $shipment = $this->createShipment($this->customer, 'EXP-PRINT-GUEST');

        $this->get(route('customer.shipments.invoice', $shipment))->assertRedirect(route('login'));
        $this->get(route('customer.shipments.label', $shipment))->assertRedirect(route('login'));
    }

    private function createShipment(Customer $customer, string $trackingNumber): Shipment
    {
        $shipment = Shipment::create([
            'tracking_number' => $trackingNumber,
            'customer_id' => $customer->id,
            'origin_branch_id' => $this->origin->id,
            'destination_branch_id' => $this->destination->id,
            'sender_name' => 'Pengirim',
            'sender_phone' => '0811111111',
            'sender_address' => 'Alamat Pengirim',
            'receiver_name' => 'Penerima',
            'receiver_phone' => '0812222222',
            'receiver_address' => 'Alamat Penerima',
            'total_weight' => 2,
            'shipping_cost' => 25000,
            'service_type' => 'regular',
            'status' => Shipment::STATUS_CREATED,
        ]);

        Payment::create([
            'shipment_id' => $shipment->id,
            'amount' => 25000,
            'payment_type' => Payment::TYPE_PREPAID,
            'payment_status' => Payment::STATUS_PENDING,
        ]);

        $shipment->shipmentItems()->create([
            'item_name' => 'Dokumen',
            'quantity' => 1,
            'weight' => 2,
            'description' => 'Paket dokumen',
        ]);

        return $shipment;
    }
}
