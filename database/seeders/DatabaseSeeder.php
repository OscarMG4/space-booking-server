<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,  // Primero: roles y permisos
            UserSeeder::class,            // Segundo: usuarios con roles
            SpaceCategorySeeder::class,   // Tercero: categorías de espacios
            SpaceSeeder::class,           // Cuarto: espacios con categorías
            SpaceAvailabilitySeeder::class, // Quinto: disponibilidad de espacios
            BookingSeeder::class,         // Sexto: reservas (necesita usuarios y espacios)
            ReviewSeeder::class,          // Séptimo: reseñas (necesita reservas completadas)
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('👤 Credenciales de acceso:');
        $this->command->info('Admin: admin@spacebooking.com / password');
        $this->command->info('Gestor: manager@spacebooking.com / password');
        $this->command->info('Usuario: juan@spacebooking.com / password');
    }
}
