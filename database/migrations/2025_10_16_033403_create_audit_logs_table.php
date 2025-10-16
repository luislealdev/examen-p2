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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action', 100); // login, logout, access_page, etc.
            $table->string('resource')->nullable(); // customers, inventories, etc.
            $table->string('method', 10)->default('GET'); // GET, POST, PUT, DELETE
            $table->string('url', 500);
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->json('request_data')->nullable(); // Para guardar datos de formularios
            $table->integer('response_code')->default(200);
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
