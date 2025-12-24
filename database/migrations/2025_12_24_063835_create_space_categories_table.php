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
        Schema::create('space_categories', function (Blueprint $table) {
            $table->id(); // ID único de la categoría
            $table->string('name'); // Nombre de la categoría (ej: "Tecnología", "Capacitación")
            $table->string('slug')->unique(); // Slug único para URLs (ej: "tecnologia", "capacitacion")
            $table->text('description')->nullable(); // Descripción de la categoría
            $table->string('icon')->nullable(); // Nombre del ícono o clase CSS para UI
            $table->string('color')->nullable(); // Código hexadecimal de color para UI (ej: "#3B82F6")
            $table->integer('sort_order')->default(0); // Orden de visualización en listados
            $table->boolean('is_active')->default(true); // Si la categoría está activa o no
            $table->timestamps(); // created_at y updated_at
            
            $table->index('slug');
            $table->index('is_active');
        });

        // Tabla pivot para relación muchos a muchos entre spaces y categories
        Schema::create('space_category', function (Blueprint $table) {
            $table->id(); // ID único de la relación
            $table->foreignId('space_id')->constrained('spaces')->onDelete('cascade'); // Espacio al que se asigna la categoría
            $table->foreignId('space_category_id')->constrained('space_categories')->onDelete('cascade'); // Categoría asignada al espacio
            $table->timestamps(); // created_at y updated_at
            
            $table->unique(['space_id', 'space_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('space_category');
        Schema::dropIfExists('space_categories');
    }
};
