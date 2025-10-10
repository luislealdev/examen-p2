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
        // Check if tables exist before creating foreign keys
        if (Schema::hasTable('film') && Schema::hasTable('stores')) {
            Schema::create('inventory', function (Blueprint $table) {
                // Primary key
                $table->unsignedSmallInteger('inventory_id')->autoIncrement();
                
                // Foreign keys
                $table->unsignedSmallInteger('film_id');
                $table->unsignedTinyInteger('store_id');
                
                // Timestamp
                $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
                
                // Foreign key constraints
                $table->foreign('film_id')
                      ->references('film_id')
                      ->on('film')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');
                      
                $table->foreign('store_id')
                      ->references('store_id')
                      ->on('stores')
                      ->onDelete('restrict')
                      ->onUpdate('cascade');
                
                // Indexes for performance
                $table->index('film_id');
                $table->index('store_id');
                $table->index(['film_id', 'store_id']);
                $table->index('last_update');
            });
        } else {
            throw new Exception('Required tables (film, stores) do not exist. Please run their migrations first.');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
