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
        Schema::create('rentals', function (Blueprint $table) {
            $table->id('rental_id');
            
            // Foreign keys
            $table->foreignId('inventory_id')->constrained('inventory', 'inventory_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id');
            $table->foreignId('staff_id')->constrained('staff', 'staff_id');
            
            // Rental dates
            $table->timestamp('rental_date');
            $table->timestamp('return_date')->nullable();
            
            // Rental details
            $table->decimal('rental_amount', 8, 2);
            $table->enum('status', ['active', 'returned', 'overdue'])->default('active');
            
            // Additional fields
            $table->text('notes')->nullable();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            // Indexes
            $table->index('rental_date');
            $table->index('return_date');
            $table->index('status');
            $table->index(['customer_id', 'rental_date']);
            $table->index(['inventory_id', 'rental_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rentals');
    }
};
