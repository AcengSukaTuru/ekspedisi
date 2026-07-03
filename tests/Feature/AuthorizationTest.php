<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\ShipmentAssignment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $customerUserA;
    protected $customerA;
    protected $customerUserB;
    protected $customerB;
    protected $courierA;
    protected $courierB;
    protected $branch1;
    protected $branch2;
    protected $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->courierA = User::factory()->create(['role' => User::ROLE_COURIER]);
        $this->courierB = User::factory()->create(['role' => User::ROLE_COURIER]);
        $this->vehicle = Vehicle::create(['plate_number' => 'B 1234 CD', 'vehicle_type' => 'Van', 'driver_name' => 'John']);

        $this->customerUserA = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $this->customerA = Customer::create(['user_id' => $this->customerUserA->id, 'name' => 'Cust A', 'email' => 'a@c.com', 'phone' => '1', 'address' => 'A']);

        $this->customerUserB = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $this->customerB = Customer::create(['user_id' => $this->customerUserB->id, 'name' => 'Cust B', 'email' => 'b@c.com', 'phone' => '2', 'address' => 'B']);

        $this->branch1 = Branch::create(['branch_name' => 'A', 'branch_code' => 'A', 'city' => 'City A', 'address' => 'Addr A']);
        $this->branch2 = Branch::create(['branch_name' => 'B', 'branch_code' => 'B', 'city' => 'City B', 'address' => 'Addr B']);
    }

    protected function createShipment($customerId, $trackingNumber = 'TRK123')
    {
        return Shipment::create([
            'tracking_number' => $trackingNumber,
            'customer_id' => $customerId,
            'origin_branch_id' => $this->branch1->id,
            'destination_branch_id' => $this->branch2->id,
            'sender_name' => 'S',
            'sender_phone' => '1',
            'sender_address' => 'S',
            'receiver_name' => 'R',
            'receiver_phone' => '2',
            'receiver_address' => 'R',
            'total_weight' => 1,
            'shipping_cost' => 10,
        ]);
    }

    public function test_admin_can_view_any_shipment(): void
    {
        $shipment = $this->createShipment($this->customerA->id);
        $response = $this->actingAs($this->admin)->get(route('admin.shipments.show', $shipment));
        $response->assertStatus(200);
    }

    public function test_customer_can_view_own_shipment(): void
    {
        $shipmentA = $this->createShipment($this->customerA->id);
        $response = $this->actingAs($this->customerUserA)->get(route('customer.shipments.show', $shipmentA));
        $response->assertStatus(200);
    }

    public function test_customer_cannot_view_others_shipment(): void
    {
        $shipmentB = $this->createShipment($this->customerB->id);
        $response = $this->actingAs($this->customerUserA)->get(route('customer.shipments.show', $shipmentB));
        $response->assertStatus(403);
    }

    public function test_courier_can_view_active_assigned_shipment(): void
    {
        $shipment = $this->createShipment($this->customerA->id);
        ShipmentAssignment::create([
            'shipment_id' => $shipment->id,
            'courier_id' => $this->courierA->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($this->courierA)->get(route('courier.shipments.show', $shipment));
        $response->assertStatus(200);
    }

    public function test_courier_cannot_view_others_assignment(): void
    {
        $shipment = $this->createShipment($this->customerA->id);
        ShipmentAssignment::create([
            'shipment_id' => $shipment->id,
            'courier_id' => $this->courierB->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($this->courierA)->get(route('courier.shipments.show', $shipment));
        $response->assertStatus(403);
    }

    public function test_courier_cannot_view_unassigned_shipment(): void
    {
        $shipment = $this->createShipment($this->customerA->id);
        $response = $this->actingAs($this->courierA)->get(route('courier.shipments.show', $shipment));
        $response->assertStatus(403);
    }

    public function test_courier_cannot_view_cancelled_assignment(): void
    {
        $shipment = $this->createShipment($this->customerA->id);
        ShipmentAssignment::create([
            'shipment_id' => $shipment->id,
            'courier_id' => $this->courierA->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_CANCELLED,
        ]);

        $response = $this->actingAs($this->courierA)->get(route('courier.shipments.show', $shipment));
        $response->assertStatus(403);
    }

    public function test_customer_and_courier_cannot_assign_courier(): void
    {
        $shipment = $this->createShipment($this->customerA->id);

        $this->assertFalse(Gate::forUser($this->customerUserA)->allows('assign', $shipment));
        $this->assertFalse(Gate::forUser($this->courierA)->allows('assign', $shipment));
    }

    public function test_customer_and_courier_cannot_verify_payment(): void
    {
        $shipment = $this->createShipment($this->customerA->id);
        $payment = Payment::create([
            'shipment_id' => $shipment->id,
            'payment_status' => 'unpaid',
            'amount' => 10,
        ]);

        $this->actingAs($this->customerUserA)->patch(route('admin.payments.verify', $payment))->assertStatus(403);
        $this->actingAs($this->courierA)->patch(route('admin.payments.verify', $payment))->assertStatus(403);
    }

    public function test_customer_only_uploads_proof_for_own_shipment(): void
    {
        $shipmentA = $this->createShipment($this->customerA->id, 'TRK1');
        $paymentA = Payment::create([
            'shipment_id' => $shipmentA->id,
            'payment_status' => 'unpaid',
            'amount' => 10,
        ]);

        $shipmentB = $this->createShipment($this->customerB->id, 'TRK2');
        $paymentB = Payment::create([
            'shipment_id' => $shipmentB->id,
            'payment_status' => 'unpaid',
            'amount' => 10,
        ]);

        $this->actingAs($this->customerUserA)->patch(route('customer.payments.submit', $paymentB), [
            'payment_method' => 'cash',
        ])->assertStatus(403);

        $this->actingAs($this->customerUserA)->patch(route('customer.payments.submit', $paymentA), [
            'payment_method' => 'cash',
        ])->assertRedirect();
    }

    public function test_customer_list_query_only_shows_own_shipments(): void
    {
        $this->createShipment($this->customerA->id, 'TRK1');
        $this->createShipment($this->customerB->id, 'TRK2');

        $response = $this->actingAs($this->customerUserA)->get(route('customer.shipments.index'));
        $response->assertStatus(200);
        $response->assertViewHas('shipments', function ($shipments) {
            return $shipments->count() === 1 && $shipments->first()->tracking_number === 'TRK1';
        });
    }

    public function test_courier_list_query_only_shows_active_assignments(): void
    {
        $shipment1 = $this->createShipment($this->customerA->id, 'TRK1');
        ShipmentAssignment::create([
            'shipment_id' => $shipment1->id,
            'courier_id' => $this->courierA->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
        ]);
        
        $shipment2 = $this->createShipment($this->customerB->id, 'TRK2');
        ShipmentAssignment::create([
            'shipment_id' => $shipment2->id,
            'courier_id' => $this->courierA->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_CANCELLED,
        ]);

        $response = $this->actingAs($this->courierA)->get(route('courier.shipments.index'));
        $response->assertStatus(200);
        $response->assertViewHas('shipments', function ($shipments) use ($shipment1) {
            return $shipments->count() === 1 && $shipments->first()->id === $shipment1->id;
        });
    }
}
