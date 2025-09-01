@extends('layouts.admin')
@php
use App\Http\Constants\BookingConstants;
@endphp
@section('content')
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Rooms Available
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRooms ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Daily Rate
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($averageDailyRate ?? 0, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Occupancy Rate
                            </div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ number_format($occupancyRate ?? 0, 1) }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-warning" role="progressbar"
                                             style="width: {{ $occupancyRate ?? 0 }}%" aria-valuenow="{{ $occupancyRate ?? 0 }}" aria-valuemin="0"
                                             aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Recent Bookings and Quick Stats -->
    <div class="row">
        <!-- Recent Bookings Table -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Guest Name</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentBookings ?? [] as $booking)
                                <tr>
                                    <td>#{{ $booking->number ?? $booking->uuid ?? 'N/A' }}</td>
                                    <td>{{ $booking->user_name ?? 'N/A' }}</td>
                                    <td>{{ $booking->room_name ?? 'N/A' }}</td>
                                    <td>{{ $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('M d, Y') : 'N/A' }}</td>
                                    <td>Rp {{ number_format($booking->amount ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($booking->status) {
                                                1 => 'bg-success text-white',  // Confirmed
                                                2 => 'bg-danger text-white',   // Canceled
                                                3 => 'bg-primary text-white',  // Completed
                                                0 => 'bg-secondary text-white', // Deleted
                                                default => 'bg-secondary text-white'
                                            };
                                            $statusText = BookingConstants::STATUS[$booking->status] ?? 'Unknown';
                                        @endphp
                                        <span class="badge {{ $statusClass }} px-2 py-1">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No recent bookings found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Monthly Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="border-left-primary p-3">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    This Month Bookings
                                </div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $monthlyBookings ?? 0 }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border-left-success p-3">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    This Month Revenue
                                </div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($monthlyRevenue ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="border-left-info p-3">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    New Users This Month
                                </div>
                                <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $newUsersThisMonth ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
