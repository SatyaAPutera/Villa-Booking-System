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
                            <h1 class="text-white">New Booking</h1>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="card card-body shadow-xl mx-3 mx-md-4 mt-n6">
            @if ($errors->any())
                <p class="alert alert-danger text-white">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </p>
            @endif
            <form class="row" action="{{ route('user.booking.store') }}" method="POST">
                @csrf
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="start_date">From Date</label>
                        <input type="date" name="start_date" min="<?= date('Y-m-d') ?>"
                            class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ old('start_date') }}" id="start_date" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="end_date">To Date</label>
                        <input type="date" name="end_date" min="<?= date('Y-m-d') ?>" class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date') }}" id="end_date" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="room_id">Room Name</label>
                        <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id">
                            <option value="">Select a room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->uuid }}" data-rate="{{ $room->rate ?? 0 }}">
                                    {{ $room->name }} - ₹{{ number_format($room->rate ?? 0, 2) }}/night
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="no_of_guests">No. of Guests</label>
                        <input class="form-control @error('no_of_guests') is-invalid @enderror" id="guests"
                            type="text" name="no_of_guests" value="{{ old('no_of_guests') }}" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="remarks">Remarks</label>
                        <input type="text" class="form-control @error('remarks') is-invalid @enderror" id="remarks"
                            name="remarks" value="{{ old('remarks') }}">
                    </div>
                </div>
                <div class="col-12">
                    <div class="input-group input-group-static my-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Booking Summary</h6>
                                <div id="bookingSummary" style="display: none;">
                                    <p class="mb-1"><strong>Room:</strong> <span id="selectedRoomName">-</span></p>
                                    <p class="mb-1"><strong>Check-in:</strong> <span id="checkInDate">-</span></p>
                                    <p class="mb-1"><strong>Check-out:</strong> <span id="checkOutDate">-</span></p>
                                    <p class="mb-1"><strong>Number of Nights:</strong> <span id="numberOfNights">-</span></p>
                                    <p class="mb-1"><strong>Rate per Night:</strong> <span id="ratePerNight">-</span></p>
                                    <hr>
                                    <p class="mb-0"><strong>Total Amount:</strong> <span id="totalAmount" class="text-primary fw-bold">Rp 0</span></p>
                                </div>
                                <div id="selectRoomMessage" class="text-muted">
                                    Please select a room and dates to see booking summary.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-sm btn-primary" id="submitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('script')
<script type="text/javascript">
// Calculate number of nights
function calculateNights() {
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    
    if (startDate && endDate) {
        var start = new Date(startDate);
        var end = new Date(endDate);
        var timeDiff = end.getTime() - start.getTime();
        var nights = Math.ceil(timeDiff / (1000 * 3600 * 24));
        return nights > 0 ? nights : 0;
    }
    return 0;
}

// Update booking summary
function updateBookingSummary() {
    var roomSelect = document.getElementById('room_id');
    var startDate = document.getElementById('start_date').value;
    var endDate = document.getElementById('end_date').value;
    var selectedOption = roomSelect.options[roomSelect.selectedIndex];
    
    if (roomSelect.value && startDate && endDate && selectedOption) {
        var nights = calculateNights();
        var rate = parseFloat(selectedOption.getAttribute('data-rate')) || 0;
        var totalAmount = rate * nights;
        
        // Update summary display
        document.getElementById('selectedRoomName').textContent = selectedOption.text.split(' - ')[0];
        document.getElementById('checkInDate').textContent = new Date(startDate).toLocaleDateString('id-ID');
        document.getElementById('checkOutDate').textContent = new Date(endDate).toLocaleDateString('id-ID');
        document.getElementById('numberOfNights').textContent = nights + (nights === 1 ? ' night' : ' nights');
        document.getElementById('ratePerNight').textContent = 'Rp ' + rate.toLocaleString('id-ID');
        document.getElementById('totalAmount').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
        
        // Show summary, hide message
        document.getElementById('bookingSummary').style.display = 'block';
        document.getElementById('selectRoomMessage').style.display = 'none';
    } else {
        // Hide summary, show message
        document.getElementById('bookingSummary').style.display = 'none';
        document.getElementById('selectRoomMessage').style.display = 'block';
    }
}

// Event listeners
document.getElementById('room_id').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var submitBtn = document.getElementById('submitBtn');
    
    if (selectedOption && selectedOption.disabled) {
        submitBtn.disabled = true;
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');
        submitBtn.textContent = 'Room Not Available';
    } else if (this.value) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
        submitBtn.textContent = 'Submit';
    } else {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
        submitBtn.textContent = 'Submit';
    }
    
    updateBookingSummary();
});

// Add event listeners for date changes
document.getElementById('start_date').addEventListener('change', updateBookingSummary);
document.getElementById('end_date').addEventListener('change', updateBookingSummary);

// Validate form before submission
document.getElementById('submitBtn').addEventListener('click', function(e) {
    var roomSelect = document.getElementById('room_id');
    var selectedOption = roomSelect.options[roomSelect.selectedIndex];
    
    if (selectedOption && selectedOption.disabled) {
        e.preventDefault();
        alert('Please select an available room. The selected room is already booked for these dates.');
        return false;
    }
    
    if (!roomSelect.value) {
        e.preventDefault();
        alert('Please select a room.');
        return false;
    }
    
    var nights = calculateNights();
    if (nights <= 0) {
        e.preventDefault();
        alert('Please select valid check-in and check-out dates.');
        return false;
    }
});
</script>
@endpush
