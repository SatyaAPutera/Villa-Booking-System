<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;

use App\Models\Room;
use App\Models\User;
use App\Models\Booking;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

use App\Http\Services\BookingService;
use App\Http\Constants\BookingConstants;

class BookingController extends Controller
{
    /**
     * Display a listing of the booking.
     */
    public function index()
    {
        $bookings = DB::table(Booking::getTableName() . ' as b')
            ->select('b.uuid', 'b.number', 'b.start_date', 'b.end_date', 'b.status', 'b.remarks', 'b.no_of_guests', 'r.name as room_name')
            ->join(Room::getTableName() . ' as r', 'r.uuid', 'b.room_id')
            ->where('b.user_id', auth()->user()->uuid)
            ->orderBy('b.start_date', 'DESC')
            ->get();
        return $this->renderView('frontend.booking.index', compact('bookings'), 'Booking');
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(BookingService $bookingService)
    {
        $today = Carbon::now()->toDateString();
        // Get all rooms with proper model properties including rate
        $rooms = Room::all();
        return $this->renderView('frontend.booking.create', compact('rooms'), 'Booking');
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request, BookingService $bookingService)
    {
        $request->validate([
            'room_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'required',
            'no_of_guests' => 'required|numeric',
        ]);

        // Get the selected room to fetch its rate
        $room = Room::where('uuid', $request->room_id)->first();
        
        if (!$room) {
            return redirect()->back()->with('error', 'Selected room not found.');
        }

        // Calculate the number of nights
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $nights = $startDate->diffInDays($endDate);
        
        // Calculate total amount (room rate × number of nights)
        $totalAmount = $room->rate * $nights;

        // Generate a user-friendly booking number
        $latestBooking = Booking::whereNotNull('number')
                               ->orderBy('created_at', 'desc')
                               ->first();
        
        if ($latestBooking && $latestBooking->number) {
            // Extract number from format like "BK1001" and increment
            $lastNumber = intval(substr($latestBooking->number, 2));
            $newNumber = $lastNumber + 1;
        } else {
            // Start from 1001 if no previous bookings
            $newNumber = 1001;
        }
        
        $bookingNumber = 'J' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        Booking::create([
            'user_id' => auth()->user()->uuid,
            'room_id' => $request->room_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'no_of_guests' => $request->no_of_guests,
            'remarks' => $request->remarks,
            'amount' => $totalAmount,
            'number' => $bookingNumber,
            'status' => BookingConstants::CONFIRMED,
        ]);

        return redirect()->route('user.booking.index')->with('success', 'Room booked successfully. Booking ID: ' . $bookingNumber . ' | Total amount: Rp ' . number_format($totalAmount, 0, ',', '.'));
    }

    private function bookRoom($bookedIds = [], $from_date, $to_date)
    {
        return DB::table(Room::getTableName() . ' as r')
            ->leftJoin(Booking::getTableName() . ' as b', 'b.room_id', 'r.uuid')
            ->where(function ($query) use ($from_date, $to_date) {
                $query->whereNull('b.room_id')
                    ->orWhere('b.start_date', '!==', $from_date)
                    ->orWhere('b.end_date', '!==', $to_date);
            })
            ->whereNotIn('r.uuid', $bookedIds)
            ->value('r.uuid');
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking = DB::table(Booking::getTableName() . ' as b')
            ->select('b.uuid', 'b.number', 'b.created_at', 'b.start_date', 'b.end_date', 'b.status', 'b.remarks', 'b.no_of_guests', 'r.name as room_name', 'u.name as booking_user')
            ->join(Room::getTableName() . ' as r', 'r.uuid', 'b.room_id')
            ->join(User::getTableName() . ' as u', 'u.uuid', 'b.user_id')
            ->where('b.uuid', $booking->uuid)
            ->first();
        return view('frontend.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return  redirect()->route('user.booking.index')->with('success', 'Booking deleted successfully!');
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking)
    {
        // Check if the booking belongs to the authenticated user
        if ($booking->user_id !== auth()->user()->uuid) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the booking can be cancelled (only confirmed bookings can be cancelled)
        if ($booking->status !== BookingConstants::CONFIRMED) {
            return redirect()->back()->withErrors(['This booking cannot be cancelled.']);
        }

        // Update the booking status to cancelled
        $booking->update([
            'status' => BookingConstants::CANCELED
        ]);

        return redirect()->route('user.booking.index')->with('success', 'Booking cancelled successfully!');
    }
}
