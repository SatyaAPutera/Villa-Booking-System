@extends('layouts.admin')

@php
use App\Http\Constants\BookingConstants;
@endphp

@section('content')
    <div class="position-relative border-radius-xl overflow-hidden shadow-lg mb-7 card">
        <div class="container border-bottom">
            <div class="row justify-space-between py-2">
                <div class="col-4 me-auto">
                    <p class="lead text-dark pt-1 mb-0">Booking Details</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Status Alert for Admin Actions -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
                    <dt class="">Guest Email</dt>
                    <dd class="">{{ $booking->user_email ?? 'N/A' }}</dd>
                </div>
                <div class="col-6">
                    <dt class="">Guest Phone</dt>
                    <dd class="">{{ $booking->user_mobile ?? 'N/A' }}</dd>
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
                                BookingConstants::CONFIRMED => 'bg-gradient-success',
                                BookingConstants::COMPLETED => 'bg-gradient-info',
                                BookingConstants::CANCELED => 'bg-gradient-danger',
                                default => 'bg-gradient-secondary'
                            };
                            $statusText = BookingConstants::STATUS[$booking->status] ?? 'Unknown';
                        @endphp
                        <span class="badge {{ $statusClass }} px-3 py-2">
                            {{ $statusText }}
                        </span>
                    </dd>
                </div>
            </dl>
            
            <!-- Action Buttons Section -->
            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('admin.booking.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fa fa-arrow-left"></i> Back to Bookings
                    </a>
                    
                    @if($booking->status == BookingConstants::CONFIRMED)
                        <form method="POST" action="{{ route('admin.booking.cancel', $booking->uuid) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-times"></i> Cancel Booking
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection