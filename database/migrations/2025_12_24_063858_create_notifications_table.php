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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // ID único de la notificación
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario destinatario de la notificación
            $table->string('type'); // Tipo de notificación: 'booking_confirmed', 'booking_reminder', 'booking_cancelled', etc.
            $table->string('title'); // Título de la notificación
            $table->text('message'); // Mensaje o contenido de la notificación
            $table->json('data')->nullable(); // Datos adicionales en formato JSON (enlaces, IDs, etc)
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('cascade'); // Reserva relacionada con esta notificación
            $table->boolean('is_read')->default(false); // Si la notificación ha sido leída
            $table->timestamp('read_at')->nullable(); // Fecha y hora en que se leyó la notificación
            $table->enum('priority', ['low', 'normal', 'high'])->default('normal'); // Prioridad de la notificación
            $table->timestamps(); // created_at y updated_at
            
            $table->index('user_id');
            $table->index('type');
            $table->index('is_read');
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
