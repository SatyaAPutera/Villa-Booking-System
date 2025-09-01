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
                            <h1 class="text-white">Booking
                                @if (!is_null($booking->number))
                                    <span> #{{ $booking->number }}</span>
                                @endif
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="card shadow-xl mx-3 mx-md-4 mt-n6 ">
            <div class="card-body p-4">
                <dl class="row">
                    @if (!is_null($booking->number))
                        <div class="col-6">
                            <dt>Booking ID</dt>
                            <dd>{{ $booking->number }}</dd>
                        </div>
                    @endif
                    <div class="col-6">
                        <dt>Booked On</dt>
                        <dd>{{ $booking->created_at }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Guest Name</dt>
                        <dd class="">{{ $booking->booking_user }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Room Name</dt>
                        <dd class="">{{ $booking->room_name }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Check-In Date</dt>
                        <dd class="">{{ DateHelper::format($booking->start_date) }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Check-Out Date</dt>
                        <dd class="">{{ DateHelper::format($booking->end_date) }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">No. of Guests</dt>
                        <dd class="">{{ $booking->no_of_guests }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Number of Nights</dt>
                        <dd class="">
                            @php
                                $startDate = \Carbon\Carbon::parse($booking->start_date);
                                $endDate = \Carbon\Carbon::parse($booking->end_date);
                                $nights = $startDate->diffInDays($endDate);
                            @endphp
                            {{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}
                        </dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Room Rate per Night</dt>
                        <dd class="">Rp {{ number_format($booking->room_rate ?? 0, 0, ',', '.') }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="text-success">Total Amount</dt>
                        <dd class=""><span class="h5 text-success">Rp {{ number_format($booking->amount ?? 0, 0, ',', '.') }}</span></dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Remarks</dt>
                        <dd class="">{{ $booking->remarks }}</dd>
                    </div>
                    <div class="col-6">
                        <dt class="">Status</dt>
                        <dd class="">
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
                            <span class="badge {{ $statusClass }} px-3 py-2">
                                {{ $statusText }}
                            </span>
                        </dd>
                    </div>
                </dl>
                
                <!-- Cancel Button Section -->
                @if($booking->status == \App\Http\Constants\BookingConstants::CONFIRMED)
                <div class="row mt-4">
                    <div class="col-12">
                        <form action="{{ route('user.booking.cancel', $booking->uuid) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="btn btn-outline-danger" 
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                <i class="fa fa-times"></i> Cancel Booking
                            </button>
                        </form>
                        <a href="{{ route('user.booking.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fa fa-arrow-left"></i> Back to Bookings
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
