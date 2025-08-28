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
        Schema::table('bookings', function (Blueprint $table) {
            // Update the status field with comprehensive options
            $table->tinyInteger('status')->default(1)->change()->comment('1 => Pending; 2 => Confirmed; 3 => Canceled; 4 => Completed; 0 => Deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Revert back to original status options
            $table->tinyInteger('status')->default(2)->change()->comment('0 => Deleted; 1=>Booked; 2=>Available');
        });
    }
};
