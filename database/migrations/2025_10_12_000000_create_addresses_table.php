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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('address_id');
            $table->string('address', 100);
            $table->string('address2', 100)->nullable();
            $table->string('district', 50)->nullable();
            $table->unsignedSmallInteger('city_id')->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('city_id')
                  ->references('city_id')
                  ->on('cities')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->index('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
