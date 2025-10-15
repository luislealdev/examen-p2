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
        Schema::create('address', function (Blueprint $table) {
            $table->id('address_id');
            $table->string('address', 50);
            $table->string('address2', 50)->nullable();
            $table->string('district', 20);
            $table->unsignedBigInteger('city_id');
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            // Note: SQLite doesn't support spatial types, so we'll use a string for location
            // In production with MySQL/PostgreSQL, this would be a POINT or GEOMETRY type
            $table->string('location', 100)->nullable()->comment('Spatial coordinates as string (lat,lng)');
            
            // Foreign keys
            $table->foreign('city_id')->references('city_id')->on('city')->onDelete('restrict')->onUpdate('cascade');
            
            // Indexes
            $table->index('city_id');
            $table->index('postal_code');
            $table->index('district');
            $table->index('last_update');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};
