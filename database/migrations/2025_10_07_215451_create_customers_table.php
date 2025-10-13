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
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id'); // Surrogate primary key
            $table->unsignedBigInteger('store_id'); // Foreign key to stores table
            $table->string('first_name', 45); // Customer first name
            $table->string('last_name', 45); // Customer last name
            $table->string('email', 50)->nullable(); // Customer email address
            $table->unsignedBigInteger('address_id'); // Foreign key to address table
            $table->boolean('active')->default(true); // Active customer indicator
            $table->timestamp('create_date')->useCurrent(); // Date customer was added
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate(); // Auto-update timestamp
            
            // Indexes for better performance
            $table->index('store_id');
            $table->index('address_id');
            $table->index('active');
            $table->index(['last_name', 'first_name']);
            
            // Foreign key constraints (commented out for now until we have the referenced tables)
            // $table->foreign('store_id')->references('store_id')->on('stores');
            // $table->foreign('address_id')->references('address_id')->on('addresses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
