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
        Schema::create('film_actors', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('film_id');
            $table->unsignedBigInteger('actor_id');
            $table->string('character_name')->nullable();
            $table->integer('order')->nullable(); // Orden de aparición en créditos
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('film_id')->references('film_id')->on('film')->onDelete('cascade');
            $table->foreign('actor_id')->references('actor_id')->on('actors')->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['film_id', 'actor_id']);
            
            // Índices para consultas
            $table->index('film_id');
            $table->index('actor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_actors');
    }
};
