<?php

namespace Database\Factories;

use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpaceFactory extends Factory
{
    protected $model = Space::class;

    public function definition(): array
    {
        $types = ['sala_reuniones', 'oficina', 'auditorio', 'laboratorio', 'espacio_coworking', 'otro'];
        
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement($types),
            'capacity' => $this->faker->numberBetween(2, 50),
            'price_per_hour' => $this->faker->randomFloat(2, 10, 200),
            'location' => $this->faker->words(3, true),
            'amenities' => json_encode($this->faker->randomElements(['WiFi', 'Proyector', 'Pizarra', 'Café', 'Aire acondicionado'], 3)),
            'is_available' => true,
        ];
    }
}
