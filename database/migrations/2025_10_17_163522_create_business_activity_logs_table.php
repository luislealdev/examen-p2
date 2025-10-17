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
        Schema::create('business_activity_logs', function (Blueprint $table) {
            $table->id();
            
            // Categorización
            $table->string('category', 50)->index(); // rental, inventory, access, customer, film, staff
            $table->string('action', 100)->index(); // create, update, delete, login, etc.
            $table->string('entity_type', 50); // rental, customer, film, inventory, etc.
            $table->unsignedBigInteger('entity_id')->nullable(); // ID del objeto afectado
            
            // Referencias de usuario y contexto
            $table->unsignedBigInteger('user_id')->nullable()->index(); // Usuario que realiza la acción
            $table->unsignedInteger('staff_id')->nullable()->index(); // Staff involucrado
            $table->unsignedInteger('customer_id')->nullable()->index(); // Cliente involucrado
            $table->unsignedInteger('film_id')->nullable()->index(); // Película involucrada
            $table->unsignedInteger('store_id')->nullable()->index(); // Tienda involucrada
            
            // Detalles y metadata
            $table->json('details')->nullable(); // Datos específicos de la actividad
            $table->enum('severity', ['info', 'warning', 'high', 'critical'])->default('info')->index();
            
            // Información de red y contexto
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices para búsquedas eficientes
            $table->index(['category', 'action']);
            $table->index(['user_id', 'created_at']);
            $table->index(['customer_id', 'created_at']);
            $table->index(['film_id', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index('created_at');
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('staff_id')->references('staff_id')->on('staff')->onDelete('set null');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
            $table->foreign('film_id')->references('film_id')->on('film')->onDelete('set null');
            $table->foreign('store_id')->references('store_id')->on('stores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_activity_logs');
    }
};
