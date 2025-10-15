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
        Schema::create('rental', function (Blueprint $table) {
            $table->id('rental_id');
            $table->timestamp('rental_date')->useCurrent();
            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamp('return_date')->nullable();
            $table->unsignedBigInteger('staff_id');
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            // Foreign keys
            $table->foreign('inventory_id')->references('inventory_id')->on('inventory')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('restrict')->onUpdate('cascade');
            
            // Indexes
            $table->index('rental_date');
            $table->index('return_date');
            $table->index(['inventory_id', 'rental_date']);
            $table->index(['customer_id', 'rental_date']);
            $table->index(['staff_id', 'rental_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental');
    }
};
