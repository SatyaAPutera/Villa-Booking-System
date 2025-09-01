@extends('layouts.master')
@section('content')
    <div>
        <header class="bg-gradient-dark  py-2">
            <div class="page-header min-vh-50 "
                style="background-image: url('../assets/img/bg9.jpg'); transform: translate3d(0px, 2.5e-06px, 0px);">
                <span class="mask bg-gradient-dark opacity-6"></span>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 text-center mx-auto my-auto">
                            <h1 class="text-white">My Bookings</h1>
                            @if (auth()->guard('user')->check())
                                <a href="{{ route('user.booking.create') }}" class="btn bg-white text-dark mt-4">Book Now</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="card card-body shadow-xl mx-3 mx-md-4 mt-n6">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <span class="alert-icon"><i class="fas fa-check"></i></span>
                    <span class="alert-text">{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('booking_details'))
                <!-- Booking Success Modal -->
                <div class="modal fade" id="bookingSuccessModal" tabindex="-1" role="dialog" aria-labelledby="bookingSuccessModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title text-white" id="bookingSuccessModalLabel">
                                    <i class="fas fa-check-circle me-2"></i>Booking Confirmed!
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12 text-center mb-3">
                                        <h4 class="text-success">Your booking has been confirmed successfully!</h4>
                                        <p class="text-muted">Please save your booking details for future reference.</p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Booking Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        @php $details = session('booking_details'); @endphp
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Booking ID:</strong> <span class="text-primary">#{{ $details['booking_number'] ?? 'N/A' }}</span></p>
                                                <p><strong>Room:</strong> {{ $details['room_name'] ?? 'N/A' }}</p>
                                                <p><strong>Check-in:</strong> {{ $details['start_date'] ?? 'N/A' }}</p>
                                                <p><strong>Check-out:</strong> {{ $details['end_date'] ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Number of Guests:</strong> {{ $details['no_of_guests'] ?? 'N/A' }}</p>
                                                <p><strong>Number of Nights:</strong> {{ $details['nights'] ?? 'N/A' }}</p>
                                                <p><strong>Rate per Night:</strong> Rp {{ number_format($details['room_rate'] ?? 0, 0, ',', '.') }}</p>
                                                <p><strong class="text-success">Total Amount:</strong> <span class="text-success h5">Rp {{ number_format($details['total_amount'] ?? 0, 0, ',', '.') }}</span></p>
                                            </div>
                                        </div>
                                        @if($details['remarks'] ?? false)
                                            <hr>
                                            <p><strong>Remarks:</strong> {{ $details['remarks'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <a href="{{ route('user.booking.show', $details['booking_uuid'] ?? '#') }}" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table align-items-center mb-0 text-center">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Booking ID</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">From Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">To Date</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No. of
                                Guests</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Room Name
                            </th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (empty($bookings))
                            <tr>
                                <td class="text-sm font-weight-normal mb-0 text-center text-warning" colspan="7">No
                                    Bookings Found!!</td>
                            </tr>
                        @else
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td class="text-sm font-weight-normal mb-0">
                                        <strong>#{{ $booking->number ?? substr($booking->uuid, 0, 8) }}</strong>
                                    </td>
                                    <td class="text-sm font-weight-normal mb-0">
                                        {{ DateHelper::format($booking->start_date) }}
                                    </td>
                                    <td class="text-sm font-weight-normal mb-0">
                                        {{ DateHelper::format($booking->end_date) }}
                                    </td>
                                    <td class="text-sm font-weight-normal mb-0 text-center">
                                        {{ $booking->no_of_guests }}</td>
                                    <td class="text-sm font-weight-normal mb-0">{{ $booking->room_name }}</td>
                                    <td class="text-sm font-weight-normal mb-0">
                                        @php
                                            $statusClass = match($booking->status) {
                                                1 => 'bg-success text-white',  // Confirmed
                                                2 => 'bg-danger text-white',   // Canceled
                                                3 => 'bg-primary text-white',  // Completed
                                                0 => 'bg-secondary text-white', // Deleted
                                                default => 'bg-secondary text-white'
                                            };
                                            $statusText = \App\Http\Constants\BookingConstants::STATUS[$booking->status] ?? 'Unknown';
                                        @endphp
                                        <span class="badge {{ $statusClass }} px-2 py-1">
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td class="justify-content-center d-flex">
                                        <a href="{{ route('user.booking.show', $booking->uuid) }}" 
                                           class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fa fa-eye" aria-hidden="true"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('script')
@if(session('booking_details'))
<script>
$(document).ready(function() {
    console.log('Booking details session found');
    
    // Test if modal exists
    if ($('#bookingSuccessModal').length > 0) {
        console.log('Modal element found, showing modal...');
        $('#bookingSuccessModal').modal('show');
    } else {
        console.log('Modal element not found!');
        alert('Booking confirmed successfully!\nBooking ID: {{ session("booking_details.booking_number", "N/A") }}\nTotal: Rp {{ number_format(session("booking_details.total_amount", 0), 0, ",", ".") }}');
    }
});
</script>
@else
<script>
console.log('No booking details in session');
</script>
@endif
@endpush
@push('script')
@endpush
