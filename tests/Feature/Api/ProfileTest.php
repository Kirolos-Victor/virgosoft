<?php

namespace Tests\Feature\Api;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile(): void
    {
        $user = User::factory()->create();
        Asset::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'balance'],
                'assets',
            ]);
    }

    public function test_unauthenticated_cannot_view_profile(): void
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);
    }
}
