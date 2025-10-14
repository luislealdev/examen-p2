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
        if (!Schema::hasTable('staff')) {
            Schema::create('staff', function (Blueprint $table) {
                $table->id('staff_id'); // Surrogate primary key
                $table->string('first_name', 45); // Staff first name
                $table->string('last_name', 45); // Staff last name
                $table->unsignedBigInteger('address_id'); // Foreign key to address table
                $table->binary('picture')->nullable(); // BLOB containing employee photograph
                $table->string('email', 50)->nullable(); // Staff email address
                $table->unsignedBigInteger('store_id')->nullable(); // Foreign key to stores table (home store)
                $table->boolean('active')->default(true); // Active employee indicator
                $table->string('username', 16)->unique(); // Username for rental system access
                $table->string('password', 64); // Password hash (SHA2 compatible length)
                $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate(); // Auto-update timestamp
                
                // Indexes for better performance
                $table->index('store_id');
                $table->index('address_id');
                $table->index('active');
                $table->index(['last_name', 'first_name']);
                $table->index('email');
                // Note: username unique constraint is already created by the unique() method above
                
                // Foreign key constraints (commented out for now until we have the referenced tables)
                // $table->foreign('store_id')->references('store_id')->on('stores');
                // $table->foreign('address_id')->references('address_id')->on('addresses');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
