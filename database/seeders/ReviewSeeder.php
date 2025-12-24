<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener reservas completadas
        $completedBookings = Booking::where('status', 'completed')
            ->with(['user', 'space'])
            ->get();

        $comments = [
            5 => [
                'Excelente espacio, muy bien equipado y cómodo.',
                'Perfecto para nuestras reuniones, lo recomiendo totalmente.',
                'Impecable limpieza y tecnología de primera.',
                'Superaron nuestras expectativas, definitivamente volveremos.',
            ],
            4 => [
                'Muy buen espacio, solo le faltaría mejor iluminación.',
                'Buena experiencia en general, el equipo de audio podría mejorar.',
                'Espacio adecuado y bien ubicado.',
                'Cumple con lo esperado, buena relación calidad-precio.',
            ],
            3 => [
                'Está bien pero podría mejorar la climatización.',
                'Espacio funcional pero básico.',
                'Cumple su función aunque esperaba más.',
            ],
            2 => [
                'El espacio necesita mantenimiento.',
                'Algunas cosas no funcionaban correctamente.',
            ],
            1 => [
                'Muy decepcionante, no correspondía con la descripción.',
            ],
        ];

        foreach ($completedBookings as $booking) {
            // 70% de probabilidad de tener reseña
            if (rand(1, 100) <= 70) {
                $rating = $this->getWeightedRating();
                
                Review::create([
                    'user_id' => $booking->user_id,
                    'space_id' => $booking->space_id,
                    'booking_id' => $booking->id,
                    'rating' => $rating,
                    'comment' => $comments[$rating][array_rand($comments[$rating])],
                    'is_approved' => true,
                    'is_flagged' => false,
                ]);
            }
        }
    }

    /**
     * Generar rating con distribución realista (más 4s y 5s)
     */
    private function getWeightedRating(): int
    {
        $rand = rand(1, 100);
        
        if ($rand <= 40) return 5;      // 40% son 5 estrellas
        if ($rand <= 70) return 4;      // 30% son 4 estrellas
        if ($rand <= 85) return 3;      // 15% son 3 estrellas
        if ($rand <= 95) return 2;      // 10% son 2 estrellas
        return 1;                       // 5% son 1 estrella
    }
}
