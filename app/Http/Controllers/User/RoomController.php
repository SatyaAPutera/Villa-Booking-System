<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Services\BookingService;
use App\Http\Constants\BookingConstants;

class RoomController extends Controller
{
    /**
     * Display a list of the rooms.
     */
    public function index(BookingService $bookingService)
    {
        $today = Carbon::now()->toDateString();
        $rooms = $bookingService->availableRooms($today);
        
        return view('frontend.rooms.index', compact('rooms'))->with('title', 'Rooms');
    }

    /**
     * Display the specified room details.
     */
    public function show(Room $room)
    {
        // Load the admin relationship
        $room->load('admin');
        
        return view('frontend.rooms.detail', compact('room'))->with('title', 'Room Details - ' . $room->name);
    }

    public function getAvailableRooms(Request $request, BookingService $bookingService)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $rooms = $bookingService->allRoomsWithAvailability($start_date, $end_date);

        return response()->json([
            'message' => 'Rooms with availability status.',
            'rooms' => $rooms
        ]);
    }
}
