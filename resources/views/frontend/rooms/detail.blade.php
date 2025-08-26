@extends('layouts.master')
@section('content')
    <div>
        <!-- Hero Section with Room Image -->
        <div class="page-header min-vh-75 position-relative"
            style="background-image: url('{{ $room->first_image ? asset($room->first_image) : 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80' }}');">
            <span class="mask bg-dark opacity-4"></span>
        </div>

        <!-- Room Details Card -->
        <div class="container mt-n6">
            <div class="card shadow-xl">
                <div class="card-body p-5" style="background-color: #f8f9fa;">
                    
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
                                            <h6 class="mb-0 font-weight-bold">{{ $room->admin->name ?? 'Admin' }}</h6>
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
                            @if($room->images && count($room->images) > 0)
                                <div id="roomCarousel" class="carousel slide shadow-lg rounded-3" data-bs-ride="carousel">
                                    <!-- Carousel Indicators -->
                                    <div class="carousel-indicators">
                                        @foreach($room->images as $index => $image)
                                            <button type="button" data-bs-target="#roomCarousel" data-bs-slide-to="{{ $index }}" 
                                                    class="{{ $index === 0 ? 'active' : '' }}" 
                                                    aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                                    aria-label="Slide {{ $index + 1 }}"></button>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Carousel Items -->
                                    <div class="carousel-inner rounded-3">
                                        @foreach($room->images as $index => $image)
                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                <img src="{{ asset($image) }}" 
                                                     class="d-block w-100" 
                                                     alt="Room Image {{ $index + 1 }}"
                                                     style="height: 400px; object-fit: cover; cursor: pointer;"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#imageModal"
                                                     onclick="showFullImage('{{ asset($image) }}', 'Room Image {{ $index + 1 }}')">
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Carousel Controls -->
                                    @if(count($room->images) > 1)
                                        <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <div class="avatar avatar-xl bg-gradient-secondary rounded-circle mx-auto mb-3">
                                        <i class="fa fa-image text-white text-lg"></i>
                                    </div>
                                    <h5 class="text-muted">No images available</h5>
                                    <p class="text-muted">This room doesn't have any uploaded images yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Fullscreen Image Modal -->
                    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="imageModalLabel">Room Image</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center p-0">
                                    <img id="fullscreenImage" src="" alt="" class="img-fluid w-100">
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
<script>
function showFullImage(imageSrc, imageAlt) {
    console.log('Showing image:', imageSrc); // Debug log
    const fullscreenImg = document.getElementById('fullscreenImage');
    const modalTitle = document.getElementById('imageModalLabel');
    
    if (fullscreenImg) {
        fullscreenImg.src = imageSrc;
        fullscreenImg.alt = imageAlt;
    }
    
    if (modalTitle) {
        modalTitle.textContent = imageAlt;
    }
    
    // Try to show modal - check for different Bootstrap versions
    const modal = document.getElementById('imageModal');
    if (modal) {
        // For Bootstrap 5
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
        // For Bootstrap 4 or jQuery
        else if (typeof $ !== 'undefined' && $.fn.modal) {
            $(modal).modal('show');
        }
        // Fallback
        else {
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
    }
}

// Close modal when clicking outside or on close button
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                // Close modal
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $(modal).modal('hide');
                } else {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                }
            }
        });
    }
});
</script>
@endpush