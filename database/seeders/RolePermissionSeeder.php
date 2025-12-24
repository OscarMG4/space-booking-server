<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            // Permisos de Espacios
            ['name' => 'Ver Espacios', 'slug' => 'spaces.view', 'group' => 'spaces', 'description' => 'Ver listado de espacios'],
            ['name' => 'Crear Espacios', 'slug' => 'spaces.create', 'group' => 'spaces', 'description' => 'Crear nuevos espacios'],
            ['name' => 'Editar Espacios', 'slug' => 'spaces.edit', 'group' => 'spaces', 'description' => 'Editar espacios existentes'],
            ['name' => 'Eliminar Espacios', 'slug' => 'spaces.delete', 'group' => 'spaces', 'description' => 'Eliminar espacios'],
            
            // Permisos de Reservas
            ['name' => 'Ver Reservas', 'slug' => 'bookings.view', 'group' => 'bookings', 'description' => 'Ver listado de reservas'],
            ['name' => 'Crear Reservas', 'slug' => 'bookings.create', 'group' => 'bookings', 'description' => 'Crear nuevas reservas'],
            ['name' => 'Editar Reservas', 'slug' => 'bookings.edit', 'group' => 'bookings', 'description' => 'Editar reservas propias'],
            ['name' => 'Eliminar Reservas', 'slug' => 'bookings.delete', 'group' => 'bookings', 'description' => 'Eliminar reservas propias'],
            ['name' => 'Ver Todas las Reservas', 'slug' => 'bookings.view-all', 'group' => 'bookings', 'description' => 'Ver todas las reservas del sistema'],
            ['name' => 'Gestionar Todas las Reservas', 'slug' => 'bookings.manage-all', 'group' => 'bookings', 'description' => 'Editar/eliminar cualquier reserva'],
            
            // Permisos de Usuarios
            ['name' => 'Ver Usuarios', 'slug' => 'users.view', 'group' => 'users', 'description' => 'Ver listado de usuarios'],
            ['name' => 'Crear Usuarios', 'slug' => 'users.create', 'group' => 'users', 'description' => 'Crear nuevos usuarios'],
            ['name' => 'Editar Usuarios', 'slug' => 'users.edit', 'group' => 'users', 'description' => 'Editar usuarios'],
            ['name' => 'Eliminar Usuarios', 'slug' => 'users.delete', 'group' => 'users', 'description' => 'Eliminar usuarios'],
            
            // Permisos de Reseñas
            ['name' => 'Ver Reseñas', 'slug' => 'reviews.view', 'group' => 'reviews', 'description' => 'Ver reseñas'],
            ['name' => 'Crear Reseñas', 'slug' => 'reviews.create', 'group' => 'reviews', 'description' => 'Crear reseñas'],
            ['name' => 'Moderar Reseñas', 'slug' => 'reviews.moderate', 'group' => 'reviews', 'description' => 'Aprobar/rechazar reseñas'],
            
            // Permisos de Categorías
            ['name' => 'Gestionar Categorías', 'slug' => 'categories.manage', 'group' => 'categories', 'description' => 'Gestionar categorías de espacios'],
            
            // Permisos de Roles
            ['name' => 'Gestionar Roles', 'slug' => 'roles.manage', 'group' => 'roles', 'description' => 'Gestionar roles y permisos'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Crear roles
        $adminRole = Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Administrador con acceso completo al sistema',
        ]);

        $userRole = Role::create([
            'name' => 'Usuario',
            'slug' => 'user',
            'description' => 'Usuario estándar con permisos básicos',
        ]);

        $managerRole = Role::create([
            'name' => 'Gestor',
            'slug' => 'manager',
            'description' => 'Gestor de espacios y reservas',
        ]);

        // Asignar todos los permisos al admin
        $adminRole->permissions()->attach(Permission::all());

        // Asignar permisos básicos al usuario
        $userRole->permissions()->attach(
            Permission::whereIn('slug', [
                'spaces.view',
                'bookings.view',
                'bookings.create',
                'bookings.edit',
                'bookings.delete',
                'reviews.view',
                'reviews.create',
            ])->get()
        );

        // Asignar permisos intermedios al gestor
        $managerRole->permissions()->attach(
            Permission::whereIn('slug', [
                'spaces.view',
                'spaces.create',
                'spaces.edit',
                'bookings.view',
                'bookings.view-all',
                'bookings.create',
                'bookings.edit',
                'bookings.manage-all',
                'reviews.view',
                'reviews.moderate',
                'categories.manage',
            ])->get()
        );
    }
}
