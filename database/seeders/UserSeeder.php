<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRole = Role::where('slug', 'admin')->first();
        $userRole = Role::where('slug', 'user')->first();
        $managerRole = Role::where('slug', 'manager')->first();

        // Usuario Administrador
        $admin = User::create([
            'name' => 'Administrador del Sistema',
            'email' => 'admin@spacebooking.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567890',
            'is_active' => true,
            'department' => 'TI',
            'bio' => 'Administrador principal del sistema de reservas',
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach($adminRole);

        // Usuario Gestor
        $manager = User::create([
            'name' => 'Carlos Gestor',
            'email' => 'manager@spacebooking.com',
            'password' => Hash::make('password'),
            'phone' => '+1234567891',
            'is_active' => true,
            'department' => 'Operaciones',
            'bio' => 'Gestor de espacios y reservas',
            'email_verified_at' => now(),
        ]);
        $manager->roles()->attach($managerRole);

        // Usuarios regulares
        $users = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan@spacebooking.com',
                'department' => 'Marketing',
                'bio' => 'Especialista en marketing digital',
            ],
            [
                'name' => 'María García',
                'email' => 'maria@spacebooking.com',
                'department' => 'Recursos Humanos',
                'bio' => 'Coordinadora de RRHH',
            ],
            [
                'name' => 'Pedro Martínez',
                'email' => 'pedro@spacebooking.com',
                'department' => 'Ventas',
                'bio' => 'Director de ventas',
            ],
            [
                'name' => 'Ana López',
                'email' => 'ana@spacebooking.com',
                'department' => 'Desarrollo',
                'bio' => 'Desarrolladora Full Stack',
            ],
            [
                'name' => 'Luis Rodríguez',
                'email' => 'luis@spacebooking.com',
                'department' => 'Diseño',
                'bio' => 'Diseñador UX/UI',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'phone' => '+1234' . rand(100000, 999999),
                'is_active' => true,
                'department' => $userData['department'],
                'bio' => $userData['bio'],
                'email_verified_at' => now(),
            ]);
            $user->roles()->attach($userRole);
        }
    }
}
