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
        Schema::create('films', function (Blueprint $table) {
            $table->id('film_id');
            $table->string('title', 128);
            $table->text('description')->nullable();
            $table->year('release_year')->nullable();
            $table->unsignedBigInteger('language_id');
            $table->unsignedBigInteger('original_language_id')->nullable();
            $table->tinyInteger('rental_duration')->default(3);
            $table->decimal('rental_rate', 4, 2)->default(4.99);
            $table->smallInteger('length')->nullable();
            $table->decimal('replacement_cost', 5, 2)->default(19.99);
            $table->enum('rating', ['G', 'PG', 'PG-13', 'R', 'NC-17'])->default('G');
            $table->json('special_features')->nullable(); // Using JSON instead of SET for SQLite compatibility
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            $table->unsignedBigInteger('category_id')->nullable();
            
            // Indexes
            $table->index('title');
            $table->index('language_id');
            $table->index('original_language_id');
            $table->index('category_id');
            $table->index('last_update');
            
            // Foreign keys (will be added if tables exist)
            if (Schema::hasTable('languages')) {
                $table->foreign('language_id')->references('language_id')->on('languages')->onDelete('restrict');
                $table->foreign('original_language_id')->references('language_id')->on('languages')->onDelete('set null');
            }
            
            if (Schema::hasTable('categories')) {
                $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
