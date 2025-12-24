<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class SpaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_anyone_can_list_spaces()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        Space::factory()->count(3)->create();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/spaces');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'current_page',
                    'per_page'
                ]
            ]);
    }

    public function test_can_view_single_space()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        
        $space = Space::factory()->create([
            'name' => 'Test Space',
            'description' => 'Test Description',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/spaces/{$space->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJsonPath('data.name', 'Test Space');
    }

    public function test_authenticated_user_can_create_space()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $spaceData = [
            'name' => 'Nueva Sala',
            'description' => 'Descripción de la sala',
            'type' => 'sala_reuniones',
            'capacity' => 10,
            'price_per_hour' => 50.00,
            'location' => 'Piso 3',
            'amenities' => ['Proyector', 'WiFi'],
            'is_available' => true,
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/spaces', $spaceData);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJsonPath('data.name', 'Nueva Sala')
            ->assertJsonPath('data.type', 'sala_reuniones');

        $this->assertDatabaseHas('spaces', [
            'name' => 'Nueva Sala',
            'type' => 'sala_reuniones',
        ]);
    }

    public function test_authenticated_user_can_update_space()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create(['name' => 'Sala Original']);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/spaces/{$space->id}", [
                'name' => 'Sala Actualizada',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('spaces', [
            'id' => $space->id,
            'name' => 'Sala Actualizada',
        ]);
    }

    public function test_authenticated_user_can_delete_space()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/spaces/{$space->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('spaces', ['id' => $space->id]);
    }
}
