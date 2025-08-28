<?php

namespace App\Http\Services;

use App\Models\Room;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function availableRooms($fromDate = null, $toDate = null)
    {
        if ($toDate === null || $toDate === '') {
            $toDate = $fromDate;
        }
        
        return DB::table(Room::getTableName() . ' as r')
            ->leftJoin(Booking::getTableName() . ' as b', function ($query) use ($fromDate, $toDate) {
                $query->on('r.uuid', '=', 'b.room_id')
                    ->where('b.status', '=', 2) // Only check confirmed bookings
                    ->where(function($dateQuery) use ($fromDate, $toDate) {
                        $dateQuery->where(function($q) use ($fromDate, $toDate) {
                            // Booking overlaps with selected date range
                            $q->whereDate('b.start_date', '<=', $toDate)
                              ->whereDate('b.end_date', '>=', $fromDate);
                        });
                    });
            })
            ->select('r.*', DB::raw('CASE WHEN b.room_id IS NOT NULL THEN 0 ELSE 1 END as is_available'))
            ->distinct()
            ->get();
    }

    public function allRoomsWithAvailability($fromDate = null, $toDate = null)
    {
        if ($toDate === null || $toDate === '') {
            $toDate = $fromDate;
        }
        
        return DB::table(Room::getTableName() . ' as r')
            ->leftJoin(Booking::getTableName() . ' as b', function ($query) use ($fromDate, $toDate) {
                $query->on('r.uuid', '=', 'b.room_id')
                    ->where('b.status', '=', 2) // Only check confirmed bookings
                    ->where(function($dateQuery) use ($fromDate, $toDate) {
                        $dateQuery->where(function($q) use ($fromDate, $toDate) {
                            // Booking overlaps with selected date range
                            $q->whereDate('b.start_date', '<=', $toDate)
                              ->whereDate('b.end_date', '>=', $fromDate);
                        });
                    });
            })
            ->select('r.*', DB::raw('CASE WHEN b.room_id IS NOT NULL THEN 0 ELSE 1 END as is_available'))
            ->get();
    }
}
