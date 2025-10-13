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
        Schema::create('language', function (Blueprint $table) {
            $table->id('language_id'); // Surrogate primary key
            $table->string('name', 20); // English name of the language
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate(); // Auto-update timestamp
            
            // Indexes for better performance
            $table->index('name'); // Index for language name searches
            $table->unique('name'); // Ensure language names are unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language');
    }
};
