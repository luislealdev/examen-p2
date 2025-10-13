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
        // Check if the table already exists to avoid conflicts
        if (!Schema::hasTable('film')) {
            Schema::create('film', function (Blueprint $table) {
                // Primary key following Sakila naming convention
                $table->smallIncrements('film_id')->comment('A surrogate primary key used to uniquely identify each film in the table');
                
                // Basic film information
                $table->string('title', 128)->comment('The title of the film');
                $table->text('description')->nullable()->comment('A short description or plot summary of the film');
                $table->year('release_year')->nullable()->comment('The year in which the movie was released');
                
                // Language relationships
                $table->unsignedTinyInteger('language_id')->comment('A foreign key pointing at the language table; identifies the language of the film');
                $table->unsignedTinyInteger('original_language_id')->nullable()->comment('A foreign key pointing at the language table; identifies the original language of the film. Used when a film has been dubbed into a new language');
                
                // Rental information
                $table->unsignedTinyInteger('rental_duration')->default(3)->comment('The length of the rental period, in days');
                $table->decimal('rental_rate', 4, 2)->default(4.99)->comment('The cost to rent the film for the period specified in the rental_duration column');
                
                // Film specifications
                $table->unsignedSmallInteger('length')->nullable()->comment('The duration of the film, in minutes');
                $table->decimal('replacement_cost', 5, 2)->default(19.99)->comment('The amount charged to the customer if the film is not returned or is returned in a damaged state');
                
                // Film rating - ENUM with specific values
                $table->enum('rating', ['G', 'PG', 'PG-13', 'R', 'NC-17'])->default('G')->comment('The rating assigned to the film. Can be one of: G, PG, PG-13, R, or NC-17');
                
                // Special features - SET type (stored as JSON for Laravel compatibility)
                $table->json('special_features')->nullable()->comment('Lists which common special features are included on the DVD. Can be zero or more of: Trailers, Commentaries, Deleted Scenes, Behind the Scenes');
                
                // Timestamp for last update (Sakila standard)
                $table->timestamp('last_update')
                      ->useCurrent()
                      ->useCurrentOnUpdate()
                      ->comment('When the row was created or most recently updated');
                
                // Foreign key constraints
                $table->foreign('language_id')->references('language_id')->on('language')->onDelete('restrict');
                $table->foreign('original_language_id')->references('language_id')->on('language')->onDelete('set null');
                
                // Indexes for performance
                $table->index('title', 'idx_film_title');
                $table->index('language_id', 'idx_film_language_id');
                $table->index('original_language_id', 'idx_film_original_language_id');
                $table->index('release_year', 'idx_film_release_year');
                $table->index('rating', 'idx_film_rating');
                $table->index('rental_rate', 'idx_film_rental_rate');
                $table->index('length', 'idx_film_length');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('film');
    }
};
