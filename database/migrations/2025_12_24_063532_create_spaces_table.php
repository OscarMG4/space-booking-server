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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id(); // ID único del espacio
            $table->string('name'); // Nombre del espacio (ej: "Sala de Juntas A")
            $table->text('description')->nullable(); // Descripción detallada del espacio
            $table->enum('type', ['meeting_room', 'auditorium', 'conference_room', 'classroom', 'event_hall', 'workspace', 'other'])->default('meeting_room'); // Tipo de espacio
            $table->integer('capacity')->unsigned(); // Capacidad máxima de personas
            $table->decimal('price_per_hour', 10, 2)->default(0.00); // Precio por hora de uso
            $table->string('location')->nullable(); // Ubicación física (edificio, dirección)
            $table->string('floor')->nullable(); // Piso donde se encuentra
            $table->json('amenities')->nullable(); // Amenidades disponibles: ['projector', 'wifi', 'whiteboard', 'audio_system', etc]
            $table->string('image_url')->nullable(); // URL de la imagen principal del espacio
            $table->boolean('is_available')->default(true); // Disponibilidad general del espacio
            $table->text('rules')->nullable(); // Reglas específicas del espacio (no fumar, no comida, etc)
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at para borrado lógico
            
            // Indexes
            $table->index('type');
            $table->index('is_available');
            $table->index('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
