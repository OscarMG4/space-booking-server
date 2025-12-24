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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // ID único de la reseña
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que escribe la reseña
            $table->foreignId('space_id')->constrained('spaces')->onDelete('cascade'); // Espacio que se está reseñando
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->onDelete('set null'); // Reserva asociada a esta reseña
            $table->tinyInteger('rating')->unsigned(); // Calificación de 1 a 5 estrellas
            $table->text('comment')->nullable(); // Comentario o reseña escrita por el usuario
            $table->boolean('is_approved')->default(true); // Si la reseña está aprobada para mostrarse públicamente
            $table->boolean('is_flagged')->default(false); // Si la reseña fue marcada por contenido inapropiado
            $table->text('admin_notes')->nullable(); // Notas del administrador sobre esta reseña
            $table->timestamps(); // created_at y updated_at
            $table->softDeletes(); // deleted_at para borrado lógico
            
            $table->index('user_id');
            $table->index('space_id');
            $table->index('rating');
            $table->index('is_approved');
            
            // Constraint: rating debe estar entre 1 y 5
            $table->check('rating >= 1 AND rating <= 5');
            
            // Un usuario solo puede hacer una reseña por reserva
            $table->unique(['user_id', 'booking_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
