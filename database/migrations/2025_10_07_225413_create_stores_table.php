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
        Schema::create('stores', function (Blueprint $table) {
            $table->id('store_id');
            $table->unsignedBigInteger('manager_staff_id');
            $table->string('address', 255);
            $table->string('city', 50);
            $table->string('state', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('country', 50);
            $table->string('phone', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamp('last_update')->useCurrent()->useCurrentOnUpdate();
            
            // Computed location field
            $table->string('location')->virtualAs("CONCAT(city, ', ', country)");
            
            // Indexes
            $table->index('manager_staff_id');
            $table->index('city');
            $table->index('country');
            $table->index('last_update');
            
            // Foreign keys (will be added if tables exist)
            if (Schema::hasTable('staff')) {
                $table->foreign('manager_staff_id')->references('staff_id')->on('staff')->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
