<?php

namespace Database\Seeders;

use App\Models\SpaceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpaceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tecnología',
                'slug' => 'tecnologia',
                'description' => 'Espacios equipados con tecnología avanzada',
                'icon' => 'desktop-computer',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Capacitación',
                'slug' => 'capacitacion',
                'description' => 'Salas diseñadas para formación y entrenamiento',
                'icon' => 'academic-cap',
                'color' => '#10B981',
                'sort_order' => 2,
            ],
            [
                'name' => 'Eventos',
                'slug' => 'eventos',
                'description' => 'Espacios para eventos corporativos y celebraciones',
                'icon' => 'calendar',
                'color' => '#F59E0B',
                'sort_order' => 3,
            ],
            [
                'name' => 'Coworking',
                'slug' => 'coworking',
                'description' => 'Espacios de trabajo colaborativo',
                'icon' => 'users',
                'color' => '#8B5CF6',
                'sort_order' => 4,
            ],
            [
                'name' => 'Videoconferencia',
                'slug' => 'videoconferencia',
                'description' => 'Salas equipadas para videoconferencias',
                'icon' => 'video-camera',
                'color' => '#EF4444',
                'sort_order' => 5,
            ],
            [
                'name' => 'Presentaciones',
                'slug' => 'presentaciones',
                'description' => 'Espacios para presentaciones y pitch',
                'icon' => 'presentation-chart-bar',
                'color' => '#06B6D4',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            SpaceCategory::create($category);
        }
    }
}
