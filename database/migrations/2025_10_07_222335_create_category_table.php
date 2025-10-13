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
        if (!Schema::hasTable('category')) {
            Schema::create('category', function (Blueprint $table) {
                // Primary key following Sakila naming convention
                $table->tinyIncrements('category_id')->comment('A surrogate primary key used to uniquely identify each category in the table');
                
                // Category name - required field with constraints
                $table->string('name', 25)->unique()->comment('The name of the category');
                
                // Timestamp for last update (Sakila standard)
                $table->timestamp('last_update')
                      ->useCurrent()
                      ->useCurrentOnUpdate()
                      ->comment('When the row was created or most recently updated');
                
                // Indexes for performance
                $table->index('name', 'idx_category_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category');
    }
};
