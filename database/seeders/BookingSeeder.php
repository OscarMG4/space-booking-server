<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Space;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::whereHas('roles', function ($query) {
            $query->where('slug', 'user');
        })->get();

        $spaces = Space::all();

        if ($users->isEmpty() || $spaces->isEmpty()) {
            return;
        }

        // Reservas pasadas (completadas)
        for ($i = 1; $i <= 10; $i++) {
            $user = $users->random();
            $space = $spaces->random();
            $startDate = now()->subDays(rand(7, 30))->setHour(rand(8, 16))->setMinute(0)->setSecond(0);
            $duration = rand(1, 4); // 1-4 horas

            Booking::create([
                'user_id' => $user->id,
                'space_id' => $space->id,
                'event_title' => 'Reunión ' . $this->getEventType(),
                'event_description' => 'Descripción de la reunión número ' . $i,
                'start_time' => $startDate,
                'end_time' => $startDate->copy()->addHours($duration),
                'status' => 'completed',
                'attendees_count' => rand(3, $space->capacity),
                'total_price' => $space->price_per_hour * $duration,
            ]);
        }

        // Reservas futuras (confirmadas)
        for ($i = 1; $i <= 15; $i++) {
            $user = $users->random();
            $space = $spaces->random();
            $startDate = now()->addDays(rand(1, 30))->setHour(rand(8, 16))->setMinute(0)->setSecond(0);
            $duration = rand(1, 4);

            Booking::create([
                'user_id' => $user->id,
                'space_id' => $space->id,
                'event_title' => 'Reunión ' . $this->getEventType(),
                'event_description' => 'Descripción de la reunión futura número ' . $i,
                'start_time' => $startDate,
                'end_time' => $startDate->copy()->addHours($duration),
                'status' => 'confirmed',
                'attendees_count' => rand(3, $space->capacity),
                'special_requirements' => rand(0, 1) ? 'Requiere proyector y sistema de audio' : null,
                'total_price' => $space->price_per_hour * $duration,
            ]);
        }

        // Algunas reservas canceladas
        for ($i = 1; $i <= 3; $i++) {
            $user = $users->random();
            $space = $spaces->random();
            $startDate = now()->addDays(rand(1, 15))->setHour(rand(8, 16))->setMinute(0)->setSecond(0);
            $duration = rand(1, 3);

            Booking::create([
                'user_id' => $user->id,
                'space_id' => $space->id,
                'event_title' => 'Reunión Cancelada',
                'event_description' => 'Esta reunión fue cancelada',
                'start_time' => $startDate,
                'end_time' => $startDate->copy()->addHours($duration),
                'status' => 'cancelled',
                'attendees_count' => rand(3, 10),
                'total_price' => $space->price_per_hour * $duration,
                'cancellation_reason' => 'Conflicto de agenda',
                'cancelled_at' => now()->subDays(rand(1, 5)),
            ]);
        }
    }

    private function getEventType(): string
    {
        $types = [
            'de Equipo',
            'con Cliente',
            'de Planificación',
            'de Seguimiento',
            'Estrategica',
            'de Capacitación',
            'de Presentación',
            'General',
        ];

        return $types[array_rand($types)];
    }
}
