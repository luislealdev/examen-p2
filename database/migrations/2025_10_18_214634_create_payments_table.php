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
        Schema::create('payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('rental_id')->nullable();
            $table->decimal('amount', 8, 2);
            $table->datetime('payment_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime('last_update')->default(DB::raw('CURRENT_TIMESTAMP'));
            
            $table->foreign('customer_id')->references('customer_id')->on('customers');
            $table->foreign('staff_id')->references('staff_id')->on('staff');
            $table->foreign('rental_id')->references('rental_id')->on('rental');
            
            $table->index(['customer_id']);
            $table->index(['staff_id']);
            $table->index(['rental_id']);
            $table->index(['payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
