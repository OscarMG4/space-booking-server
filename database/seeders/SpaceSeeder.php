<?php

namespace Database\Seeders;

use App\Models\Space;
use App\Models\SpaceCategory;
use Illuminate\Database\Seeder;

class SpaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $spaces = [
            [
                'name' => 'Sala de Juntas Ejecutiva',
                'description' => 'Amplia sala de juntas con vista panorámica, ideal para reuniones ejecutivas y presentaciones importantes.',
                'type' => 'meeting_room',
                'capacity' => 20,
                'price_per_hour' => 150.00,
                'location' => 'Edificio Principal',
                'floor' => '10° Piso',
                'amenities' => ['proyector', 'wifi', 'pizarra', 'sistema_audio', 'videoconferencia', 'aire_acondicionado'],
                'rules' => 'No fumar. No comida con olor fuerte.',
                'categories' => ['tecnologia', 'presentaciones', 'videoconferencia'],
            ],
            [
                'name' => 'Auditorio Principal',
                'description' => 'Auditorio con capacidad para grandes eventos, conferencias y presentaciones.',
                'type' => 'auditorium',
                'capacity' => 200,
                'price_per_hour' => 500.00,
                'location' => 'Edificio Principal',
                'floor' => '1° Piso',
                'amenities' => ['proyector', 'wifi', 'sistema_audio', 'escenario', 'iluminacion_profesional', 'micrófonos'],
                'rules' => 'Requiere aprobación previa. No comida ni bebida.',
                'categories' => ['eventos', 'presentaciones', 'capacitacion'],
            ],
            [
                'name' => 'Sala de Conferencias A',
                'description' => 'Sala equipada con tecnología de última generación para videoconferencias internacionales.',
                'type' => 'conference_room',
                'capacity' => 15,
                'price_per_hour' => 100.00,
                'location' => 'Edificio Principal',
                'floor' => '5° Piso',
                'amenities' => ['proyector', 'wifi', 'pizarra', 'videoconferencia', 'sistema_audio'],
                'rules' => 'Silencio en el pasillo.',
                'categories' => ['tecnologia', 'videoconferencia'],
            ],
            [
                'name' => 'Aula de Capacitación 1',
                'description' => 'Espacio diseñado para formación y capacitación con configuración tipo escuela.',
                'type' => 'classroom',
                'capacity' => 30,
                'price_per_hour' => 80.00,
                'location' => 'Edificio de Capacitación',
                'floor' => '2° Piso',
                'amenities' => ['proyector', 'wifi', 'pizarra', 'mesas_escolares', 'aire_acondicionado'],
                'rules' => 'Mantener el orden de las mesas.',
                'categories' => ['capacitacion', 'tecnologia'],
            ],
            [
                'name' => 'Salón de Eventos VIP',
                'description' => 'Elegante salón para eventos corporativos, celebraciones y recepciones.',
                'type' => 'event_hall',
                'capacity' => 100,
                'price_per_hour' => 300.00,
                'location' => 'Edificio Norte',
                'floor' => '3° Piso',
                'amenities' => ['wifi', 'sistema_audio', 'iluminacion_ambiental', 'catering_disponible', 'estacionamiento'],
                'rules' => 'Requiere depósito. Limpieza obligatoria al finalizar.',
                'categories' => ['eventos'],
            ],
            [
                'name' => 'Espacio Coworking Premium',
                'description' => 'Espacio de trabajo compartido con escritorios individuales y áreas comunes.',
                'type' => 'workspace',
                'capacity' => 25,
                'price_per_hour' => 50.00,
                'location' => 'Edificio Norte',
                'floor' => '4° Piso',
                'amenities' => ['wifi', 'café', 'impresora', 'lockers', 'aire_acondicionado'],
                'rules' => 'Silencio en zona de concentración.',
                'categories' => ['coworking', 'tecnologia'],
            ],
            [
                'name' => 'Sala de Juntas Pequeña',
                'description' => 'Sala íntima para reuniones de equipos pequeños.',
                'type' => 'meeting_room',
                'capacity' => 6,
                'price_per_hour' => 40.00,
                'location' => 'Edificio Principal',
                'floor' => '7° Piso',
                'amenities' => ['wifi', 'pizarra', 'pantalla_tv', 'aire_acondicionado'],
                'rules' => 'Máximo 2 horas por reserva.',
                'categories' => ['tecnologia'],
            ],
            [
                'name' => 'Aula de Capacitación 2',
                'description' => 'Segunda aula para formación con equipo multimedia.',
                'type' => 'classroom',
                'capacity' => 25,
                'price_per_hour' => 75.00,
                'location' => 'Edificio de Capacitación',
                'floor' => '3° Piso',
                'amenities' => ['proyector', 'wifi', 'pizarra', 'aire_acondicionado'],
                'rules' => 'Reportar cualquier daño al equipo.',
                'categories' => ['capacitacion'],
            ],
        ];

        foreach ($spaces as $spaceData) {
            $categories = $spaceData['categories'];
            unset($spaceData['categories']);

            $space = Space::create($spaceData);

            // Asociar categorías
            $categoryIds = SpaceCategory::whereIn('slug', $categories)->pluck('id');
            $space->categories()->attach($categoryIds);
        }
    }
}
