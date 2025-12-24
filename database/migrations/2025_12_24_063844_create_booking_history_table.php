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
        Schema::create('booking_history', function (Blueprint $table) {
            $table->id(); // ID único del registro de historial
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade'); // Reserva a la que pertenece este registro
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Usuario que realizó la acción
            $table->string('action'); // Tipo de acción: 'created', 'updated', 'cancelled', 'completed', 'status_changed'
            $table->string('old_status')->nullable(); // Estado anterior de la reserva
            $table->string('new_status')->nullable(); // Nuevo estado de la reserva
            $table->json('changes')->nullable(); // JSON con los campos modificados y sus valores anteriores/nuevos
            $table->text('notes')->nullable(); // Notas adicionales sobre el cambio
            $table->string('ip_address', 45)->nullable(); // Dirección IP desde donde se hizo el cambio
            $table->string('user_agent')->nullable(); // Navegador/dispositivo usado para el cambio
            $table->timestamps(); // created_at y updated_at
            
            $table->index('booking_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_history');
    }
};
