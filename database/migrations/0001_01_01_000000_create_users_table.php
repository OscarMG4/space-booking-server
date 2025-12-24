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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID único del usuario
            $table->string('name'); // Nombre completo del usuario
            $table->string('email')->unique(); // Correo electrónico único para login
            $table->timestamp('email_verified_at')->nullable(); // Fecha de verificación de email
            $table->string('password'); // Contraseña encriptada
            $table->string('phone', 20)->nullable(); // Teléfono de contacto
            $table->boolean('is_active')->default(true); // Estado activo/inactivo del usuario
            $table->string('avatar')->nullable(); // URL de la imagen de perfil
            $table->text('bio')->nullable(); // Biografía o descripción del usuario
            $table->string('department')->nullable(); // Departamento o área de trabajo
            $table->rememberToken(); // Token para "recordar sesión"
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at para borrado lógico
            
            // Indexes
            $table->index('email');
            $table->index('is_active');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email del usuario que solicita reset
            $table->string('token'); // Token de recuperación de contraseña
            $table->timestamp('created_at')->nullable(); // Fecha de creación del token
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID único de la sesión
            $table->foreignId('user_id')->nullable()->index(); // ID del usuario autenticado
            $table->string('ip_address', 45)->nullable(); // Dirección IP del cliente
            $table->text('user_agent')->nullable(); // Información del navegador/dispositivo
            $table->longText('payload'); // Datos de la sesión serializados
            $table->integer('last_activity')->index(); // Timestamp de última actividad
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
