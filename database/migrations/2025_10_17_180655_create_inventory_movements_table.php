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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            
            // Relación con inventory
            $table->unsignedInteger('inventory_id');
            $table->foreign('inventory_id')->references('inventory_id')->on('inventory')->onDelete('cascade');
            
            // Relación con rental (opcional, para devoluciones)
            $table->unsignedInteger('rental_id')->nullable();
            $table->foreign('rental_id')->references('rental_id')->on('rental')->onDelete('set null');
            
            // Tipo de movimiento
            $table->enum('movement_type', ['rental', 'return', 'damage', 'loss', 'repair', 'restock']);
            
            // Estado de la condición antes y después del movimiento
            $table->enum('condition_from', ['available', 'damaged', 'lost'])->nullable();
            $table->enum('condition_to', ['available', 'damaged', 'lost']);
            
            // Usuario que realizó el movimiento
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Staff que realizó el movimiento
            $table->unsignedInteger('staff_id')->nullable();
            $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('set null');
            
            // Cliente involucrado (para rentas/devoluciones)
            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('customer_id')->on('customer')->onDelete('set null');
            
            // Detalles del movimiento
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Para almacenar información adicional
            
            // Timestamps
            $table->timestamp('movement_date')->useCurrent();
            $table->timestamps();
            
            // Índices para mejorar performance
            $table->index(['inventory_id', 'movement_date']);
            $table->index(['movement_type', 'movement_date']);
            $table->index(['condition_to', 'movement_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
