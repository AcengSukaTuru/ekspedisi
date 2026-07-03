<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicTrackingTest extends TestCase
{
    use RefreshDatabase;

    private Branch $origin;
    private Branch $destination;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['role' => User::ROLE_CUSTOMER]);
        $this->customer = Customer::create([
            'user_id' => $user->id,
            'name' => 'Customer Demo',
            'email' => 'customer@example.test',
            'phone' => '0811111111',
            'address' => 'Alamat Customer Rahasia',
        ]);

        $this->origin = Branch::create(['branch_name' => 'Jakarta Hub', 'branch_code' => 'JKT', 'city' => 'Jakarta', 'address' => 'Jakarta']);
        $this->destination = Branch::create(['branch_name' => 'Bandung Hub', 'branch_code' => 'BDG', 'city' => 'Bandung', 'address' => 'Bandung']);
    }

    public function test_guest_can_open_public_tracking_page(): void
    {
        $this->get(route('tracking.public'))
            ->assertOk()
            ->assertSee('Cek Resi')
            ->assertSee('Lacak Paket');
    }

    public function test_guest_can_track_existing_shipment_without_private_data(): void
    {
        $shipment = $this->createShipment();
        $shipment->shipmentTrackings()->create([
            'status' => Shipment::STATUS_IN_TRANSIT,
            'location' => 'Hub Jakarta',
            'description' => 'Paket dalam perjalanan.',
            'tracked_at' => now(),
        ]);

        $this->get(route('tracking.public.show', $shipment->tracking_number))
            ->assertOk()
            ->assertSee($shipment->tracking_number)
            ->assertSee('Jakarta')
            ->assertSee('Bandung')
            ->assertSee('Paket dalam perjalanan')
            ->assertDontSee('0812222222')
            ->assertDontSee('Alamat Penerima Rahasia')
            ->assertDontSee('Alamat Pengirim Rahasia');
    }

    public function test_unknown_tracking_number_shows_friendly_not_found_state(): void
    {
        $this->get(route('tracking.public.show', 'EXP-NOT-FOUND'))
            ->assertOk()
            ->assertSee('Resi tidak ditemukan');
    }

    private function createShipment(): Shipment
    {
        return Shipment::create([
            'tracking_number' => 'EXP-260703-TEST01',
            'customer_id' => $this->customer->id,
            'origin_branch_id' => $this->origin->id,
            'destination_branch_id' => $this->destination->id,
            'sender_name' => 'Pengirim',
            'sender_phone' => '0811111111',
            'sender_address' => 'Alamat Pengirim Rahasia',
            'receiver_name' => 'Penerima',
            'receiver_phone' => '0812222222',
            'receiver_address' => 'Alamat Penerima Rahasia',
            'total_weight' => 2,
            'shipping_cost' => 20000,
            'service_type' => 'regular',
            'status' => Shipment::STATUS_IN_TRANSIT,
            'shipment_date' => now()->toDateString(),
            'estimated_arrival' => now()->addDay()->toDateString(),
        ]);
    }
}
