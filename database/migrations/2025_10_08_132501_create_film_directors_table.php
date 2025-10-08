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
        Schema::create('film_directors', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('film_id');
            $table->unsignedBigInteger('director_id');
            $table->string('role')->default('Director'); // Director, Co-Director, etc.
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('film_id')->references('film_id')->on('film')->onDelete('cascade');
            $table->foreign('director_id')->references('director_id')->on('directors')->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['film_id', 'director_id']);
            
            // Índices para consultas
            $table->index('film_id');
            $table->index('director_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film_directors');
    }
};
