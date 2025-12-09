<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_place_order(): void
    {
        $user = User::factory()->create(['balance' => '10000.00000000']);
        Asset::factory()->create([
            'user_id' => $user->id,
            'symbol' => 'BTC',
            'amount' => '1.00000000',
        ]);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'symbol' => 'BTC',
            'side' => 'sell',
            'price' => '50000',
            'amount' => '0.1',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    public function test_user_can_view_their_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/user/orders');

        $response->assertStatus(200);
    }

    public function test_user_can_cancel_their_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => Order::STATUS_OPEN]);

        $response = $this->actingAs($user)->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
    }
}
