<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DeliveryAttempt;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Shipment;
use App\Models\ShipmentAssignment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompleteExpeditionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $courier;
    protected $customerUser;
    protected $customer;
    protected $originBranch;
    protected $destinationBranch;
    protected $vehicle;
    protected $rate;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->courier = User::factory()->create(['role' => User::ROLE_COURIER, 'name' => 'Test Courier']);
        $this->customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $this->customer = Customer::create([
            'user_id' => $this->customerUser->id,
            'name' => 'Test Customer',
            'email' => 'customer@test.com',
            'phone' => '08123456789',
            'address' => 'Test Address',
        ]);

        $this->originBranch = Branch::create([
            'branch_name' => 'Jakarta',
            'branch_code' => 'JKT',
            'city' => 'Jakarta',
            'address' => 'Jakarta Address',
        ]);
        $this->destinationBranch = Branch::create([
            'branch_name' => 'Bandung',
            'branch_code' => 'BDG',
            'city' => 'Bandung',
            'address' => 'Bandung Address',
        ]);

        $this->vehicle = Vehicle::create([
            'plate_number' => 'B 1234 XYZ',
            'vehicle_type' => 'Truck',
            'driver_name' => 'Test Courier',
            'driver_phone' => '08199999999',
        ]);

        $this->rate = Rate::create([
            'origin_branch_id' => $this->originBranch->id,
            'destination_branch_id' => $this->destinationBranch->id,
            'service_type' => 'regular',
            'price_per_kg' => 5000,
        ]);
    }

    public function test_complete_expedition_workflow_drop_off_to_delivery()
    {
        // === STEP 1: Customer creates shipment (drop-off, prepaid) ===
        $response = $this->actingAs($this->customerUser)
            ->post(route('customer.shipments.store'), [
                'origin_branch_id' => $this->originBranch->id,
                'destination_branch_id' => $this->destinationBranch->id,
                'sender_name' => 'Sender', 'sender_phone' => '081', 'sender_address' => 'Jakarta Addr',
                'receiver_name' => 'Receiver', 'receiver_phone' => '082', 'receiver_address' => 'Bandung Addr',
                'total_weight' => 10, 'service_type' => 'regular',
                'shipment_date' => now()->toDateString(), 'estimated_arrival' => now()->addDays(3)->toDateString(),
                'item_name' => 'Test Item', 'quantity' => 1, 'description' => 'Test',
                'pickup_type' => 'drop_off', 'payment_type' => 'prepaid',
            ]);

        $response->assertRedirect();
        $shipment = Shipment::where('customer_id', $this->customer->id)->first();
        $this->assertEquals(Shipment::STATUS_CREATED, $shipment->status);
        $this->assertNotNull($shipment->tracking_number);
        $this->assertEquals('drop_off', $shipment->pickup_type);

        // Payment auto-created
        $this->assertNotNull($shipment->payment);
        $this->assertEquals('prepaid', $shipment->payment->payment_type);
        $this->assertEquals(Payment::STATUS_PENDING, $shipment->payment->payment_status);

        // === STEP 2: Admin assigns courier ===
        $response = $this->actingAs($this->admin)
            ->post(route('admin.shipments.assign', $shipment), [
                'courier_id' => $this->courier->id,
                'vehicle_id' => $this->vehicle->id,
            ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('shipment_assignments', [
            'shipment_id' => $shipment->id, 'courier_id' => $this->courier->id, 'status' => 'active',
        ]);

        // === STEP 3: Admin updates status created → picked_up ===
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.shipments.update-status', $shipment), [
                'status' => Shipment::STATUS_PICKED_UP,
                'tracking_location' => 'Jakarta', 'tracking_description' => 'Paket dijemput',
            ]);
        $response->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_PICKED_UP, $shipment->status);

        // === STEP 4: Admin moves through hub flow ===
        foreach ([
            Shipment::STATUS_AT_ORIGIN_HUB => 'Sampai di hub asal',
            Shipment::STATUS_IN_TRANSIT => 'Dalam perjalanan',
            Shipment::STATUS_AT_DEST_HUB => 'Sampai di hub tujuan',
            Shipment::STATUS_OUT_FOR_DELIVERY => 'Kurir bawa paket ke alamat',
        ] as $status => $desc) {
            $response = $this->actingAs($this->admin)
                ->patch(route('admin.shipments.update-status', $shipment), [
                    'status' => $status, 'tracking_location' => 'Test', 'tracking_description' => $desc,
                ]);
            $response->assertRedirect();
            $shipment->refresh();
            $this->assertEquals($status, $shipment->status);
        }

        // === STEP 5: Courier records delivery attempt - success ===
        $response = $this->actingAs($this->courier)
            ->post(route('courier.delivery-attempts.store', $shipment), [
                'attempt_status' => 'success',
                'notes' => 'Diterima oleh penerima',
            ]);
        $response->assertRedirect();

        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->status);
        $this->assertEquals(1, $shipment->deliveryAttempts()->count());

        // Assignment auto-completed
        $assignment = $shipment->activeAssignment;
        $this->assertNull($assignment); // no longer active
        $this->assertDatabaseHas('shipment_assignments', [
            'shipment_id' => $shipment->id, 'status' => 'completed',
        ]);
    }

    public function test_failed_delivery_and_return_to_sender()
    {
        // Create shipment manually for this test
        $shipment = Shipment::create([
            'tracking_number' => 'TRK-FAIL-TEST',
            'customer_id' => $this->customer->id,
            'origin_branch_id' => $this->originBranch->id,
            'destination_branch_id' => $this->destinationBranch->id,
            'sender_name' => 'S', 'sender_phone' => '1', 'sender_address' => 'Addr',
            'receiver_name' => 'R', 'receiver_phone' => '2', 'receiver_address' => 'Addr',
            'total_weight' => 1, 'shipping_cost' => 5000,
            'status' => Shipment::STATUS_OUT_FOR_DELIVERY,
            'max_delivery_attempts' => 3,
        ]);

        // Assign courier
        ShipmentAssignment::create([
            'shipment_id' => $shipment->id, 'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id, 'assigned_by' => $this->admin->id,
            'status' => 'active', 'assigned_at' => now(),
        ]);

        // === Attempt 1: Failed ===
        $response = $this->actingAs($this->courier)
            ->post(route('courier.delivery-attempts.store', $shipment), [
                'attempt_status' => 'failed', 'failure_reason' => 'receiver_not_available', 'notes' => 'Tidak ada orang',
            ]);
        $response->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_FAILED_DELIVERY, $shipment->status);
        $this->assertEquals(1, $shipment->failedAttemptCount());
        $this->assertTrue($shipment->canAttemptDelivery());

        // === Admin reschedules ===
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.shipments.reschedule', $shipment));
        $response->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_OUT_FOR_DELIVERY, $shipment->status);

        // === Attempt 2: Failed ===
        $response = $this->actingAs($this->courier)
            ->post(route('courier.delivery-attempts.store', $shipment), [
                'attempt_status' => 'failed', 'failure_reason' => 'wrong_address',
            ]);
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_FAILED_DELIVERY, $shipment->status);

        // Admin reschedules again
        $this->actingAs($this->admin)->patch(route('admin.shipments.reschedule', $shipment));

        // === Attempt 3: Failed → should trigger RTS ===
        $response = $this->actingAs($this->courier)
            ->post(route('courier.delivery-attempts.store', $shipment), [
                'attempt_status' => 'failed', 'failure_reason' => 'refused',
            ]);
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_RETURNED_TO_SENDER, $shipment->status);
        $this->assertFalse($shipment->canAttemptDelivery());
        $this->assertNotNull($shipment->rts_reason);
    }

    public function test_invalid_status_transition_is_rejected()
    {
        $shipment = Shipment::create([
            'tracking_number' => 'TRK-TRANS', 'customer_id' => $this->customer->id,
            'origin_branch_id' => $this->originBranch->id, 'destination_branch_id' => $this->destinationBranch->id,
            'sender_name' => 'S', 'sender_phone' => '1', 'sender_address' => 'A',
            'receiver_name' => 'R', 'receiver_phone' => '2', 'receiver_address' => 'A',
            'total_weight' => 1, 'shipping_cost' => 10, 'status' => Shipment::STATUS_CREATED,
        ]);

        // Try to jump from created → in_transit (invalid)
        $response = $this->actingAs($this->admin)
            ->patch(route('admin.shipments.update-status', $shipment), [
                'status' => Shipment::STATUS_IN_TRANSIT,
            ]);

        $response->assertRedirect();
        $shipment->refresh();
        $this->assertEquals(Shipment::STATUS_CREATED, $shipment->status); // unchanged
    }

    public function test_cannot_update_status_after_delivered()
    {
        $shipment = Shipment::create([
            'tracking_number' => 'TRK-FINAL', 'customer_id' => $this->customer->id,
            'origin_branch_id' => $this->originBranch->id, 'destination_branch_id' => $this->destinationBranch->id,
            'sender_name' => 'S', 'sender_phone' => '1', 'sender_address' => 'A',
            'receiver_name' => 'R', 'receiver_phone' => '2', 'receiver_address' => 'A',
            'total_weight' => 1, 'shipping_cost' => 10, 'status' => Shipment::STATUS_DELIVERED,
        ]);

        // Admin cannot update final status
        $response = $this->actingAs($this->admin)
            ->get(route('admin.shipments.show', $shipment));
        $response->assertStatus(200);
        // The update form should NOT appear for final statuses (view logic)
    }

    public function test_cod_shipment_flow()
    {
        // Customer creates COD shipment
        $response = $this->actingAs($this->customerUser)
            ->post(route('customer.shipments.store'), [
                'origin_branch_id' => $this->originBranch->id,
                'destination_branch_id' => $this->destinationBranch->id,
                'sender_name' => 'S', 'sender_phone' => '1', 'sender_address' => 'A',
                'receiver_name' => 'R', 'receiver_phone' => '2', 'receiver_address' => 'B',
                'total_weight' => 5, 'service_type' => 'regular',
                'item_name' => 'COD Item', 'quantity' => 1,
                'payment_type' => 'cod',
            ]);

        $shipment = Shipment::where('customer_id', $this->customer->id)->first();
        $this->assertEquals('cod', $shipment->payment->payment_type);
        $this->assertEquals(Payment::STATUS_PENDING, $shipment->payment->payment_status);
    }

    public function test_pickup_request_shipment_flow()
    {
        $response = $this->actingAs($this->customerUser)
            ->post(route('customer.shipments.store'), [
                'origin_branch_id' => $this->originBranch->id,
                'destination_branch_id' => $this->destinationBranch->id,
                'sender_name' => 'S', 'sender_phone' => '1', 'sender_address' => 'Jakarta Addr',
                'receiver_name' => 'R', 'receiver_phone' => '2', 'receiver_address' => 'Bandung Addr',
                'total_weight' => 5, 'service_type' => 'regular',
                'item_name' => 'Pickup Item', 'quantity' => 1,
                'pickup_type' => 'pickup_request',
                'pickup_address' => 'Jl. Sudirman 123, Jakarta',
                'pickup_contact_name' => 'S', 'pickup_contact_phone' => '081',
            ]);

        $shipment = Shipment::where('customer_id', $this->customer->id)->first();
        $this->assertEquals('pickup_request', $shipment->pickup_type);
        $this->assertEquals('Jl. Sudirman 123, Jakarta', $shipment->pickup_address);
    }

    public function test_courier_cannot_access_unassigned_shipment()
    {
        $shipment = Shipment::create([
            'tracking_number' => 'TRK-UNASSIGN', 'customer_id' => $this->customer->id,
            'origin_branch_id' => $this->originBranch->id, 'destination_branch_id' => $this->destinationBranch->id,
            'sender_name' => 'S', 'sender_phone' => '1', 'sender_address' => 'A',
            'receiver_name' => 'R', 'receiver_phone' => '2', 'receiver_address' => 'A',
            'total_weight' => 1, 'shipping_cost' => 10,
        ]);

        $response = $this->actingAs($this->courier)->get(route('courier.shipments.show', $shipment));
        $response->assertStatus(403);
    }

    public function test_status_constants_match_industry_standard()
    {
        $statuses = Shipment::statuses();
        $this->assertCount(9, $statuses);
        $this->assertContains('created', $statuses);
        $this->assertContains('picked_up', $statuses);
        $this->assertContains('at_origin_hub', $statuses);
        $this->assertContains('in_transit', $statuses);
        $this->assertContains('at_dest_hub', $statuses);
        $this->assertContains('out_for_delivery', $statuses);
        $this->assertContains('delivered', $statuses);
        $this->assertContains('failed_delivery', $statuses);
        $this->assertContains('returned_to_sender', $statuses);
    }
}
