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
        Schema::create('cities', function (Blueprint $table) {
            $table->smallIncrements('city_id');
            $table->string('city', 50);
            $table->unsignedSmallInteger('country_id');
            $table->timestamp('last_update')->useCurrent();

            $table->foreign('country_id')
                  ->references('country_id')
                  ->on('countries')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};