<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Space;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_authenticated_user_can_list_their_bookings()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create();
        
        Booking::factory()->count(3)->create([
            'user_id' => $user->id,
            'space_id' => $space->id,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/bookings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data',
                    'current_page'
                ]
            ]);
    }

    public function test_can_view_single_booking()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'space_id' => $space->id,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'data'])
            ->assertJsonPath('data.id', $booking->id);
    }

    public function test_authenticated_user_can_create_booking()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create([
            'capacity' => 10,
            'price_per_hour' => 50.00,
        ]);

        $token = JWTAuth::fromUser($user);

        $startTime = Carbon::tomorrow()->setTime(10, 0, 0);
        $endTime = Carbon::tomorrow()->setTime(12, 0, 0);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/bookings', [
                'space_id' => $space->id,
                'event_title' => 'Reunión importante',
                'event_description' => 'Reunión con el equipo',
                'start_time' => $startTime->format('Y-m-d H:i:s'),
                'end_time' => $endTime->format('Y-m-d H:i:s'),
                'attendees_count' => 5,
                'special_requirements' => 'Proyector necesario',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['success', 'message', 'data'])
            ->assertJsonPath('data.space_id', $space->id)
            ->assertJsonPath('data.attendees_count', 5);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'space_id' => $space->id,
            'attendees_count' => 5,
        ]);
    }

    public function test_user_can_update_their_booking()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create(['price_per_hour' => 50.00]);
        
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'space_id' => $space->id,
            'status' => 'pending',
        ]);

        $token = JWTAuth::fromUser($user);

        $newStart = Carbon::tomorrow()->setTime(14, 0, 0);
        $newEnd = Carbon::tomorrow()->setTime(16, 0, 0);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson("/api/bookings/{$booking->id}", [
                'space_id' => $space->id,
                'event_title' => 'Reunión actualizada',
                'start_time' => $newStart->format('Y-m-d H:i:s'),
                'end_time' => $newEnd->format('Y-m-d H:i:s'),
                'attendees_count' => 8,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'attendees_count' => 8,
        ]);
    }

    public function test_user_can_cancel_their_booking()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create();
        
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'space_id' => $space->id,
            'status' => 'confirmed',
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/bookings/{$booking->id}/cancel", [
                'cancellation_reason' => 'Cambio de planes',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Cambio de planes',
        ]);
    }

    public function test_user_can_delete_their_booking()
    {
        $user = User::factory()->create();
        $space = Space::factory()->create();
        
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'space_id' => $space->id,
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson("/api/bookings/{$booking->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);
    }
}
