<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Agregar columnas (esto SÍ funciona en SQLite)
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('staff_id')->nullable()->after('role');
            $table->unsignedBigInteger('store_id')->nullable()->after('staff_id');
            $table->dateTime('last_login_at')->nullable()->after('updated_at');
            
            // NOTA: Las foreign keys en SQLite solo funcionan si están habilitadas
            // y solo se pueden agregar al crear la tabla, no con ALTER TABLE
            // Por lo tanto, las omitimos aquí o usamos índices simples
            $table->index('staff_id');
            $table->index('store_id');
        });

        // 2. Sincronizar datos existentes
        DB::statement('
            UPDATE users 
            SET staff_id = (SELECT staff_id FROM staff WHERE staff.email = users.email),
                store_id = (SELECT store_id FROM staff WHERE staff.email = users.email)
            WHERE role IN ("admin", "employee")
        ');
    }

    public function down(): void
    {
        // SQLite no soporta DROP COLUMN directamente
        // Necesitamos recrear la tabla completa
        
        Schema::table('users', function (Blueprint $table) {
            // Esto fallará en SQLite < 3.35.0
            // En versiones modernas funciona, en antiguas hay que recrear la tabla
            $table->dropColumn(['staff_id', 'store_id', 'last_login_at']);
        });
    }
};