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
        Schema::create('space_availabilities', function (Blueprint $table) {
            $table->id(); // ID único de la configuración de disponibilidad
            $table->foreignId('space_id')->constrained('spaces')->onDelete('cascade'); // Espacio al que aplica esta disponibilidad
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']); // Día de la semana
            $table->time('start_time'); // Hora de inicio de disponibilidad
            $table->time('end_time'); // Hora de fin de disponibilidad
            $table->boolean('is_available')->default(true); // Si el espacio está disponible o bloqueado en este horario
            $table->date('effective_from')->nullable(); // Fecha desde la cual aplica esta configuración
            $table->date('effective_until')->nullable(); // Fecha hasta la cual aplica esta configuración
            $table->text('notes')->nullable(); // Notas sobre la disponibilidad (mantenimiento, eventos especiales, etc)
            $table->timestamps(); // created_at y updated_at
            
            $table->index('space_id');
            $table->index('day_of_week');
            $table->index(['space_id', 'day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_availabilities');
    }
};
