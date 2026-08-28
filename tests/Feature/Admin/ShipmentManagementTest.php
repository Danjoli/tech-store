<?php

namespace Tests\Feature\Admin;

use App\Enums\ShipmentStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ShipmentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_view_and_update_a_shipment(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();
        $shipment = $order->shipment()->create([
            'status' => ShipmentStatus::PENDING,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.shipments.edit', $shipment))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Shipments/Edit')
                ->where('shipment.id', $shipment->id)
                ->has('statuses', 5)
            );

        $this->actingAs($admin)
            ->put(route('admin.shipments.update', $shipment), [
                'status' => ShipmentStatus::SHIPPED->value,
                'carrier' => 'Correios',
                'tracking_code' => 'BR123456789BR',
            ])
            ->assertRedirect(route('admin.shipments.index'));

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => ShipmentStatus::SHIPPED->value,
            'carrier' => 'Correios',
            'tracking_code' => 'BR123456789BR',
        ]);
        $this->assertNotNull($shipment->fresh()->shipped_at);
    }

    public function test_a_shipment_requires_a_known_status(): void
    {
        $admin = User::factory()->admin()->create();
        $shipment = Order::factory()->create()->shipment()->create([
            'status' => ShipmentStatus::PENDING,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.shipments.update', $shipment), [
                'status' => 'unknown',
            ])
            ->assertSessionHasErrors('status');
    }
}
