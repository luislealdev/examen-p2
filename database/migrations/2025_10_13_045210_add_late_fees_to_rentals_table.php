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
        Schema::table('rentals', function (Blueprint $table) {
            $table->decimal('late_fee', 8, 2)->default(0.00)->after('rental_amount');
            $table->timestamp('due_date')->nullable()->after('return_date');
            $table->boolean('late_fee_applied')->default(false)->after('late_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['late_fee', 'due_date', 'late_fee_applied']);
        });
    }
};
