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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // ID único de la reserva
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que hace la reserva
            $table->foreignId('space_id')->constrained('spaces')->onDelete('cascade'); // Espacio reservado
            $table->string('event_title'); // Título del evento o reunión
            $table->text('event_description')->nullable(); // Descripción del evento
            $table->dateTime('start_time'); // Fecha y hora de inicio de la reserva
            $table->dateTime('end_time'); // Fecha y hora de fin de la reserva
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('confirmed'); // Estado de la reserva
            $table->integer('attendees_count')->unsigned()->nullable(); // Número de asistentes esperados
            $table->text('special_requirements')->nullable(); // Requerimientos especiales (catering, setup, etc)
            $table->decimal('total_price', 10, 2)->default(0.00); // Precio total calculado de la reserva
            $table->text('cancellation_reason')->nullable(); // Razón de cancelación si aplica
            $table->timestamp('cancelled_at')->nullable(); // Fecha y hora de cancelación
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at para borrado lógico
            
            // Indexes
            $table->index('user_id');
            $table->index('space_id');
            $table->index('status');
            $table->index('start_time');
            $table->index('end_time');
            $table->index(['space_id', 'start_time', 'end_time']); // Para verificar disponibilidad
            
            // Constraint: end_time debe ser mayor que start_time
            $table->check('end_time > start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
