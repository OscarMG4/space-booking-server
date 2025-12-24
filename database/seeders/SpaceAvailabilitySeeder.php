<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\SpaceAvailability;
use Illuminate\Database\Seeder;

class SpaceAvailabilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spaces = Space::all();
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $weekend = ['saturday', 'sunday'];

        foreach ($spaces as $space) {
            // Horario de lunes a viernes (8:00 AM - 6:00 PM)
            foreach ($weekdays as $day) {
                SpaceAvailability::create([
                    'space_id' => $space->id,
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '18:00:00',
                    'is_available' => true,
                    'notes' => 'Horario regular de oficina',
                ]);
            }

            // Algunos espacios también disponibles los sábados
            if (in_array($space->type, ['event_hall', 'auditorium', 'workspace'])) {
                SpaceAvailability::create([
                    'space_id' => $space->id,
                    'day_of_week' => 'saturday',
                    'start_time' => '09:00:00',
                    'end_time' => '14:00:00',
                    'is_available' => true,
                    'notes' => 'Horario reducido de sábado',
                ]);
            }

            // Domingos cerrado para todos
            SpaceAvailability::create([
                'space_id' => $space->id,
                'day_of_week' => 'sunday',
                'start_time' => '00:00:00',
                'end_time' => '23:59:59',
                'is_available' => false,
                'notes' => 'Cerrado los domingos',
            ]);
        }
    }
}
