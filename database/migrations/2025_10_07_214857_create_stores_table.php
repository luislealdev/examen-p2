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
        Schema::create('stores', function (Blueprint $table) {
            $table->id('store_id'); // Surrogate primary key
            $table->unsignedBigInteger('manager_staff_id'); // Foreign key to staff table
            $table->unsignedBigInteger('address_id'); // Foreign key to address table
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate(); // Auto-update timestamp
            
            // Foreign key constraints (commented out for now until we have the referenced tables)
            // $table->foreign('manager_staff_id')->references('staff_id')->on('staff');
            // $table->foreign('address_id')->references('address_id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
