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
        Schema::table('inventory', function (Blueprint $table) {
            $table->enum('condition', ['available', 'damaged', 'lost'])
                  ->default('available')
                  ->after('store_id');
            
            $table->timestamp('condition_updated_at')
                  ->nullable()
                  ->after('condition');
            
            $table->text('condition_notes')
                  ->nullable()
                  ->after('condition_updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn(['condition', 'condition_updated_at', 'condition_notes']);
        });
    }
};
