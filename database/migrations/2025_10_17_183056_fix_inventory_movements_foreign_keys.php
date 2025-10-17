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
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Drop the incorrect foreign key constraint
            $table->dropForeign(['customer_id']);
            
            // Add the correct foreign key constraint pointing to 'customers' table
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Drop the corrected foreign key
            $table->dropForeign(['customer_id']);
            
            // Restore the incorrect one (for rollback purposes)
            $table->foreign('customer_id')->references('customer_id')->on('customer')->onDelete('set null');
        });
    }
};
