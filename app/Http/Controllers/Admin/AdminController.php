<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Display admin login form
     */
    public function showLoginForm()
    {
        return view('admin.login.index');
    }

    /**
     * Process admin login form
     */
    public function login(Request $request)
    {
        $this->validate($request,[
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = $request->only(['username', 'password']);

        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            return redirect()->route('admin.dashboard');
        } else {
            throw ValidationException::withMessages([
                'username' => 'Invalid username or password'
            ]);
        }

    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('home');
    }

    /**
     * Display admin dashboard
     */
    public function index()
    {
        $today = Carbon::now()->toDateString();
        
        // Basic counts
        $totalUsers = User::count();
        $totalRooms = Room::count();
        $totalBookings = Booking::count();
        
        // Available rooms (not booked today)
        $availableRooms = DB::table(Room::getTableName().' as r')
        ->leftJoin(Booking::getTableName().' as b', 'b.room_id', 'r.uuid')
        ->where(function($query) use ($today) {
            $query->whereNull('b.room_id')
                  ->orWhereDate('b.start_date', '<>', $today)
                  ->orWhereDate('b.end_date', '<>', $today);
        })
        ->select('r.*')
        ->count();
        
        // Total revenue from all bookings
        $totalRevenue = Booking::sum('amount');
        
        // Average daily rate (average room rate)
        $averageDailyRate = Room::avg('rate');
        
        // Occupancy rate calculation (booked rooms / total rooms * 100)
        $bookedRoomsToday = Booking::whereDate('start_date', '<=', $today)
                                  ->whereDate('end_date', '>=', $today)
                                  ->distinct('room_id')
                                  ->count();
        $occupancyRate = $totalRooms > 0 ? ($bookedRoomsToday / $totalRooms) * 100 : 0;
        
        // Revenue data for charts (last 7 days)
        $revenueData = [];
        $revenueLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenueLabels[] = $date->format('M d');
            $dailyRevenue = Booking::whereDate('created_at', $date->toDateString())
                                  ->sum('amount');
            $revenueData[] = $dailyRevenue ?? 0;
        }
        
        // Booking status distribution for pie chart (based on new status system)
        $confirmedBookings = Booking::where('status', 2)->count(); // Confirmed
        $pendingBookings = Booking::where('status', 1)->count(); // Pending
        $cancelledBookings = Booking::where('status', 3)->count(); // Canceled
        
        // Recent bookings for the table
        $recentBookings = Booking::with(['room', 'user'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
        
        // Monthly bookings count
        $monthlyBookings = Booking::whereMonth('created_at', Carbon::now()->month)
                                 ->whereYear('created_at', Carbon::now()->year)
                                 ->count();
        
        return view('admin.dashboard.index', compact(
            'totalRooms', 
            'totalBookings', 
            'totalUsers', 
            'availableRooms',
            'totalRevenue',
            'averageDailyRate',
            'occupancyRate',
            'revenueData',
            'revenueLabels',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'recentBookings',
            'monthlyBookings'
        ));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
}
