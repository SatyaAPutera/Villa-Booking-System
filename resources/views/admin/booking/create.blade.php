@extends('layouts.admin')
@section('content')
    <div class="position-relative border-radius-xl overflow-hidden shadow-lg mb-7 card">
        <div class="container border-bottom">
            <div class="row justify-space-between py-2">
                <div class="col-4 me-auto">
                    <p class="lead text-dark pt-1 mb-0">Book a Room</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form class="row" action="{{ route('admin.booking.store') }}" method="POST">
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
                        <input type="date" name="end_date" min="<?= date('Y-m-d') ?>" 
                            class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ old('end_date') }}" id="end_date" />
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="user_id">User</label>
                        <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">Select a user</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->uuid }}" {{ old('user_id') == $user->uuid ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="room_id">Room Name</label>
                        <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id">
                            <option value="">Select a room</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->uuid }}" data-rate="{{ $room->rate ?? 0 }}" {{ old('room_id') == $room->uuid ? 'selected' : '' }}>
                                    {{ $room->name }} - Rp {{ number_format($room->rate ?? 0, 0, ',', '.') }}/night
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
});

// Validate form before submission
document.getElementById('submitBtn').addEventListener('click', function(e) {
    var userSelect = document.getElementById('user_id');
    var roomSelect = document.getElementById('room_id');
    var selectedOption = roomSelect.options[roomSelect.selectedIndex];
    
    if (!userSelect.value) {
        e.preventDefault();
        alert('Please select a user.');
        return false;
    }
    
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
