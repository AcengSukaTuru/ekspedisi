<?php

namespace Tests\Feature;

use App\Models\Shipment;
use App\Models\ShipmentAssignment;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Branch;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Fase1ShipmentAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup initial required data
        $this->admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->courier = User::factory()->create(['role' => User::ROLE_COURIER]);
        $this->courier2 = User::factory()->create(['role' => User::ROLE_COURIER]);
        $this->vehicle = Vehicle::create(['plate_number' => 'B 1234 CD', 'vehicle_type' => 'Van', 'driver_name' => 'John']);
        
        $customerUser = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $customer = Customer::create(['user_id' => $customerUser->id, 'name' => 'Cust', 'email' => 'c@c.com', 'phone' => '123', 'address' => 'abc']);
        
        $branch1 = Branch::create(['branch_name' => 'A', 'branch_code' => 'A', 'city' => 'City A', 'address' => 'Addr A']);
        $branch2 = Branch::create(['branch_name' => 'B', 'branch_code' => 'B', 'city' => 'City B', 'address' => 'Addr B']);
        
        $this->shipment = Shipment::create([
            'tracking_number' => 'TRK123',
            'customer_id' => $customer->id,
            'origin_branch_id' => $branch1->id,
            'destination_branch_id' => $branch2->id,
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

    public function test_shipment_can_have_multiple_historical_assignments()
    {
        $a1 = ShipmentAssignment::create([
            'shipment_id' => $this->shipment->id,
            'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_CANCELLED,
            'assigned_at' => now()->subDay(),
        ]);
        
        $a2 = ShipmentAssignment::create([
            'shipment_id' => $this->shipment->id,
            'courier_id' => $this->courier2->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
            'assigned_at' => now(),
        ]);

        $this->assertCount(2, $this->shipment->assignments);
        $this->assertEquals($a2->id, $this->shipment->activeAssignment->id);
    }

    public function test_assignment_belongs_to_relations()
    {
        $assignment = ShipmentAssignment::create([
            'shipment_id' => $this->shipment->id,
            'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id,
            'assigned_by' => $this->admin->id,
        ]);

        $this->assertEquals($this->shipment->id, $assignment->shipment->id);
        $this->assertEquals($this->courier->id, $assignment->courier->id);
        $this->assertEquals($this->vehicle->id, $assignment->vehicle->id);
        $this->assertEquals($this->admin->id, $assignment->assignedBy->id);
    }

    public function test_courier_can_have_many_active_shipments()
    {
        $shipment2 = $this->shipment->replicate();
        $shipment2->tracking_number = 'TRK456';
        $shipment2->save();

        ShipmentAssignment::create([
            'shipment_id' => $this->shipment->id,
            'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
        ]);

        ShipmentAssignment::create([
            'shipment_id' => $shipment2->id,
            'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
        ]);

        $this->assertCount(2, $this->courier->courierAssignments()->where('status', 'active')->get());
    }

    public function test_vehicle_can_have_many_active_shipments()
    {
        ShipmentAssignment::create([
            'shipment_id' => $this->shipment->id,
            'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id,
            'status' => ShipmentAssignment::STATUS_ACTIVE,
        ]);

        $this->assertCount(1, $this->vehicle->activeAssignments);
    }

    public function test_deleting_shipment_cascades_assignments()
    {
        ShipmentAssignment::create([
            'shipment_id' => $this->shipment->id,
            'courier_id' => $this->courier->id,
            'vehicle_id' => $this->vehicle->id,
        ]);

        $this->shipment->delete();
        $this->assertDatabaseCount('shipment_assignments', 0);
    }

    public function test_old_columns_are_preserved()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('shipments', 'vehicle_id'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'driver_name'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('vehicles', 'driver_phone'));
    }
}
