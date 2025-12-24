<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Space;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startTime = Carbon::tomorrow()->setTime($this->faker->numberBetween(9, 16), 0, 0);
        $endTime = (clone $startTime)->addHours($this->faker->numberBetween(1, 4));
        $duration = $startTime->diffInHours($endTime);
        $space = Space::factory()->create();

        return [
            'user_id' => User::factory(),
            'space_id' => $space->id,
            'event_title' => $this->faker->sentence(4),
            'event_description' => $this->faker->optional()->sentence(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'attendees_count' => $this->faker->numberBetween(1, $space->capacity),
            'status' => 'confirmed',
            'total_price' => $space->price_per_hour * $duration,
            'special_requirements' => $this->faker->optional()->sentence(),
        ];
    }
}
