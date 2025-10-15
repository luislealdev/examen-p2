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
        Schema::create('city', function (Blueprint $table) {
            $table->id('city_id');
            $table->string('city', 50);
            $table->unsignedBigInteger('country_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign keys
            $table->foreign('country_id')->references('country_id')->on('country')->onDelete('restrict')->onUpdate('cascade');
            
            // Indexes
            $table->index('city');
            $table->index('country_id');
            $table->index('last_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('city');
    }
};
