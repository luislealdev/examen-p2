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
        Schema::create('directors', function (Blueprint $table) {
            $table->id('director_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->text('biography')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 200)->nullable();
            $table->string('imdb_id', 20)->nullable()->unique();
            $table->string('photo_url')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas
            $table->index(['first_name', 'last_name']);
            $table->index('imdb_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('directors');
    }
};
