<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['sala_reuniones', 'oficina', 'auditorio', 'laboratorio', 'espacio_coworking', 'otro'])->default('sala_reuniones');
            $table->integer('capacity')->unsigned();
            $table->decimal('price_per_hour', 10, 2)->default(0.00);
            $table->string('location')->nullable();
            $table->string('floor')->nullable();
            $table->json('amenities')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('rules')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('type');
            $table->index('is_available');
            $table->index('capacity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
