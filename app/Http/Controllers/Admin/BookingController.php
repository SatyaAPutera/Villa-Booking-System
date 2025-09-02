<?php

namespace App\Http\Controllers\Admin;

use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;

use Illuminate\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Constants\BookingConstants;

class BookingController extends Controller
{
    /**
     * Display a listing of the booking.
     */
    public function index()
    {
        $bookings = DB::table(Booking::getTableName().' as b')
        ->select('b.uuid', 'b.number', 'b.start_date', 'b.end_date', 'b.status', 'b.remarks', 'b.no_of_guests', 'b.amount', 'r.name as room_name', 'u.name as booking_user')
        ->join(Room::getTableName().' as r', 'r.uuid', 'b.room_id')
        ->join(User::getTableName().' as u', 'u.uuid', 'b.user_id')
        ->orderBy('b.start_date', 'DESC')
        ->get();
        return $this->renderView('admin.booking.index', compact('bookings'), 'Booking');
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create()
    {
        $users = User::get();
        $rooms = Room::get();
        return $this->renderView('admin.booking.create', compact('rooms', 'users'), 'Book a room');
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
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
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $nights = $startDate->diffInDays($endDate);
        
        // Calculate total amount (room rate × number of nights)
        $totalAmount = $room->rate * $nights;

        // Generate a user-friendly booking number
        $latestBooking = Booking::whereNotNull('number')
                               ->orderBy('created_at', 'desc')
                               ->first();
        
        if ($latestBooking && $latestBooking->number) {
            // Extract number from format like "J1001" and increment
            $lastNumber = intval(substr($latestBooking->number, 1));
            $newNumber = $lastNumber + 1;
        } else {
            // Start from 1001 if no previous bookings
            $newNumber = 1001;
        }
        
        $bookingNumber = 'J' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        Booking::create([
            'user_id' => $request->user_id,
            'room_id' => $request->room_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'no_of_guests' => $request->no_of_guests,
            'remarks' => $request->remarks,
            'amount' => $totalAmount,
            'number' => $bookingNumber,
            'status' => BookingConstants::CONFIRMED,
        ]);

        return redirect()->route('admin.booking.index')->with('success', 'Booking created successfully! Booking ID: ' . $bookingNumber . ' | Total amount: Rp ' . number_format($totalAmount, 0, ',', '.'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking = DB::table(Booking::getTableName().' as b')
        ->select('b.uuid', 'b.number', 'b.created_at', 'b.start_date', 'b.end_date', 'b.status', 'b.remarks', 'b.no_of_guests', 'b.amount', 'r.name as room_name', 'r.rate as room_rate', 'u.name as booking_user', 'u.email as user_email', 'u.mobile as user_mobile')
        ->join(Room::getTableName().' as r', 'r.uuid', 'b.room_id')
        ->join(User::getTableName().' as u', 'u.uuid', 'b.user_id')
        ->where('b.uuid', $booking->uuid)
        ->first();
        return view('admin.booking.show', compact('booking'));
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
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking)
    {
        // Check if the booking can be cancelled (only confirmed bookings can be cancelled)
        if ($booking->status !== BookingConstants::CONFIRMED) {
            return redirect()->back()->withErrors(['This booking cannot be cancelled.']);
        }

        // Update the booking status to cancelled
        $booking->update([
            'status' => BookingConstants::CANCELED
        ]);

        return redirect()->route('admin.booking.index')->with('success', 'Booking cancelled successfully!');
    }

    /**
     * Remove the specified booking from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        return  redirect()->route('admin.booking.index')->with('success', 'Booking deleted successfully!');
    }
}
