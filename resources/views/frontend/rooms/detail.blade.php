@extends('layouts.master')
@section('content')
    <div>
        <!-- Hero Section with Room Image -->
        <div class="page-header min-vh-75 position-relative"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80');">
            <span class="mask bg-dark opacity-4"></span>
        </div>

        <!-- Room Details Card -->
        <div class="container mt-n6">
            <div class="card shadow-xl bg-light">
                <div class="card-body p-5">
                    
                    <!-- Contact Info Section -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <h1 class="display-4 font-weight-bold mb-3">{{ $room->name ?? 'Gedung Auditorium Widya Sabha Universitas Udayana' }}</h1>
                            <p class="lead mb-4">Professional meeting space with panoramic city views</p>
                            <h3 class="font-weight-bold mb-4">Contact Info</h3>
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3">
                                            <i class="fa fa-user text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">Siti</h6>
                                            <small class="text-muted">Partner</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex gap-3">
                                        <a href="#" class="btn btn-outline-primary btn-sm rounded-circle">
                                            <i class="fa fa-info text-sm"></i>
                                        </a>
                                        <a href="#" class="btn btn-outline-success btn-sm rounded-circle">
                                            <i class="fa fa-phone text-sm"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overview Section -->
                    <div class="row">
                        <div class="col-12">
                            <h3 class="font-weight-bold mb-4">Overview</h3>
                            <div class="row">
                                <div class="col-lg-8">
                                    <p class="text-muted mb-4">
                                        {{ $room->description }}
                                    </p>
                                </div>
                                <div class="col-lg-4">
                                    <div class="card bg-light border">
                                        <div class="card-body text-center">
                                            <h4 class="font-weight-bold text-primary">₹ {{ $room->rate }}</h4>
                                            <small class="text-muted">per night</small>
                                            <div class="mt-3">
                                                <a href="{{ route('user.booking.create', ['room' => $room->uuid ?? '#']) }}" 
                                                   class="btn btn-primary w-100">
                                                    Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gallery Section -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <h3 class="font-weight-bold mb-4">Room Gallery</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                         class="img-fluid rounded-3 shadow" alt="Conference Room View">
                                </div>
                                <div class="col-md-3">
                                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                         class="img-fluid rounded-3 shadow mb-3" alt="Meeting Setup">
                                    <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                         class="img-fluid rounded-3 shadow" alt="Presentation Area">
                                </div>
                                <div class="col-md-3">
                                    <img src="https://images.unsplash.com/photo-1556761175-4b46a572b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                         class="img-fluid rounded-3 shadow mb-3" alt="City View">
                                    <img src="https://images.unsplash.com/photo-1582653291997-079a1c04e5a1?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                                         class="img-fluid rounded-3 shadow" alt="Seating Arrangement">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Section -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <div class="card bg-gradient-primary">
                                <div class="card-body text-center text-white">
                                    <h4 class="text-white font-weight-bold">Ready to Book?</h4>
                                    <p class="mb-4 opacity-8">Reserve this premium conference room for your next important meeting</p>
                                    <a href="{{ route('user.booking.create', ['room' => $room->uuid ?? '#']) }}" 
                                       class="btn btn-white btn-lg">
                                        <i class="fa fa-calendar-check me-2"></i>
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush