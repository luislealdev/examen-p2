<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Primero elimina la columna existente si existe
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            
            // Crea la nueva columna con el enum
            $table->enum('role', ['admin', 'employee', 'customer'])->default('customer');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};