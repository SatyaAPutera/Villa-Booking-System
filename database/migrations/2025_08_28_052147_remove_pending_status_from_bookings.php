<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing pending bookings (status = 1) to confirmed (status = 1)
        // Since we removed pending, confirmed is now 1
        DB::table('bookings')->where('status', 1)->update(['status' => 1]);
        
        // Update canceled bookings from status 3 to status 2
        DB::table('bookings')->where('status', 3)->update(['status' => 2]);
        
        // Update completed bookings from status 4 to status 3
        DB::table('bookings')->where('status', 4)->update(['status' => 3]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the status changes
        // Completed bookings back to status 4
        DB::table('bookings')->where('status', 3)->update(['status' => 4]);
        
        // Canceled bookings back to status 3
        DB::table('bookings')->where('status', 2)->update(['status' => 3]);
    }
};
