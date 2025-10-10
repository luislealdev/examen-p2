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
        Schema::table('film', function (Blueprint $table) {
            // Add category_id column
            $table->unsignedTinyInteger('category_id')->nullable()->after('last_update');
            
            // Add foreign key constraint
            $table->foreign('category_id')->references('category_id')->on('category')->onDelete('set null');
            
            // Add index for performance
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('film', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['category_id']);
            
            // Drop the column
            $table->dropColumn('category_id');
        });
    }
};
