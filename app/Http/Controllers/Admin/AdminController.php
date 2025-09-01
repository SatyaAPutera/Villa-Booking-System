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
use App\Http\Constants\BookingConstants;
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
        $totalBookings = Booking::whereNotIn('status', [BookingConstants::CANCELED, BookingConstants::DELETED])->count();
        
        // Available rooms (not booked today by confirmed/completed bookings)
        $availableRooms = DB::table(Room::getTableName().' as r')
        ->leftJoin(Booking::getTableName().' as b', function($join) use ($today) {
            $join->on('b.room_id', 'r.uuid')
                 ->whereDate('b.start_date', '<=', $today)
                 ->whereDate('b.end_date', '>=', $today)
                 ->whereNotIn('b.status', [BookingConstants::CANCELED, BookingConstants::DELETED]);
        })
        ->whereNull('b.room_id')
        ->count();
        
        // Total revenue from confirmed and completed bookings (excluding cancelled)
        $totalRevenue = Booking::whereNotIn('status', [BookingConstants::CANCELED, BookingConstants::DELETED])
                              ->sum('amount');
        
        // Average daily rate (average room rate)
        $averageDailyRate = Room::avg('rate');
        
        // Occupancy rate calculation (confirmed/completed booked rooms / total rooms * 100)
        $bookedRoomsToday = Booking::whereDate('start_date', '<=', $today)
                                  ->whereDate('end_date', '>=', $today)
                                  ->whereNotIn('status', [BookingConstants::CANCELED, BookingConstants::DELETED])
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
                                  ->whereNotIn('status', [BookingConstants::CANCELED, BookingConstants::DELETED])
                                  ->sum('amount');
            $revenueData[] = $dailyRevenue ?? 0;
        }
        
        // Booking status distribution for pie chart (based on new status system)
        $confirmedBookings = Booking::where('status', BookingConstants::CONFIRMED)->count(); // Confirmed
        $completedBookings = Booking::where('status', BookingConstants::COMPLETED)->count(); // Completed
        $cancelledBookings = Booking::where('status', BookingConstants::CANCELED)->count(); // Canceled
        
        // Recent bookings for the table (using proper field names)
        $recentBookings = DB::table(Booking::getTableName().' as b')
                           ->select('b.uuid', 'b.number', 'b.start_date', 'b.end_date', 'b.amount', 'b.status', 
                                   'u.name as user_name', 'r.name as room_name')
                           ->join(User::getTableName().' as u', 'u.uuid', 'b.user_id')
                           ->join(Room::getTableName().' as r', 'r.uuid', 'b.room_id')
                           ->orderBy('b.created_at', 'desc')
                           ->limit(5)
                           ->get();
        
        // Monthly bookings count (excluding cancelled and deleted)
        $monthlyBookings = Booking::whereMonth('created_at', Carbon::now()->month)
                                 ->whereYear('created_at', Carbon::now()->year)
                                 ->whereNotIn('status', [BookingConstants::CANCELED, BookingConstants::DELETED])
                                 ->count();
        
        // Monthly revenue (excluding cancelled and deleted bookings)
        $monthlyRevenue = Booking::whereMonth('created_at', Carbon::now()->month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->whereNotIn('status', [BookingConstants::CANCELED, BookingConstants::DELETED])
                                ->sum('amount');
        
        // New users this month
        $newUsersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
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
            'completedBookings',
            'cancelledBookings',
            'recentBookings',
            'monthlyBookings',
            'monthlyRevenue',
            'newUsersThisMonth'
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

    /**
     * Show the form for creating a new admin.
     */
    public function createAdmin()
    {
        return $this->renderView('admin.user.create_admin', [], 'Add New Admin');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'mobile' => 'nullable|string|max:20',
            'username' => 'required|string|min:3|max:255|unique:admins,username',
            'password' => 'required|string|min:6|confirmed',
        ]);

        \App\Models\Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->mobile, // Note: admin table uses phone_number, form uses mobile
            'username' => $request->username,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Admin created successfully!');
    }
}
