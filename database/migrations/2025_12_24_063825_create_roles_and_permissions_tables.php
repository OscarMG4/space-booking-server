<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // ID único del rol
            $table->string('name')->unique(); // Nombre del rol (ej: "Administrador", "Usuario")
            $table->string('slug')->unique(); // Slug único para identificación (ej: "admin", "user")
            $table->text('description')->nullable(); // Descripción del rol y sus responsabilidades
            $table->timestamps(); // created_at y updated_at
            
            $table->index('slug');
        });

        // Tabla de permisos
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // ID único del permiso
            $table->string('name')->unique(); // Nombre del permiso (ej: "Crear Espacios")
            $table->string('slug')->unique(); // Slug único para identificación (ej: "spaces.create")
            $table->text('description')->nullable(); // Descripción de qué permite hacer este permiso
            $table->string('group')->nullable(); // Grupo al que pertenece (ej: 'spaces', 'bookings', 'users') para organización
            $table->timestamps(); // created_at y updated_at
            
            $table->index('slug');
            $table->index('group');
        });

        // Tabla pivot: role_permission
        Schema::create('role_permission', function (Blueprint $table) {
            $table->id(); // ID único de la relación
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Rol al que se asigna el permiso
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade'); // Permiso asignado al rol
            $table->timestamps(); // created_at y updated_at
            
            $table->unique(['role_id', 'permission_id']);
        });

        // Tabla pivot: role_user (relación muchos a muchos)
        Schema::create('role_user', function (Blueprint $table) {
            $table->id(); // ID único de la relación
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario al que se asigna el rol
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Rol asignado al usuario
            $table->timestamps(); // created_at y updated_at para auditoría
            
            $table->unique(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
