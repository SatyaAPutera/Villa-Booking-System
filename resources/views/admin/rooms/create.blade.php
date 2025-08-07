@extends('layouts.admin')
@section('content')
    <div class="position-relative border-radius-xl overflow-hidden shadow-lg mb-7 card">
        <div class="container border-bottom">
            <div class="row justify-space-between py-2">
                <div class="col-4 me-auto">
                    <p class="lead text-dark pt-1 mb-0">Add New Unit Details</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <p class="alert alert-danger text-white">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </p>
            @endif
            <form class="row" action="{{ route('admin.rooms.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="room_no">Unit No</label>
                        <input type="text" class="form-control disabled @error('room_no') is-invalid @enderror"
                            name="room_no" value='{{ $roomNo }}'>
                    </div>
                    @error('room_no')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="name">Unit Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ old('name') }}" />
                    </div>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-12">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="description">Unit Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" 
                                  rows="3" placeholder="Enter unit description">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="rate">Unit Rate (per night)</label>
                        <input type="number" step="0.01" class="form-control @error('rate') is-invalid @enderror" 
                               name="rate" value="{{ old('rate') }}" placeholder="0.00"/>
                    </div>
                    @error('rate')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="occupancy">Max Occupancy</label>
                        <input type="number" class="form-control @error('occupancy') is-invalid @enderror" 
                               name="occupancy" value="{{ old('occupancy') }}" placeholder="2"/>
                    </div>
                    @error('occupancy')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <!-- Image Upload Section -->
                <div class="col-12">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="image">Unit Images</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror @error('image.*') is-invalid @enderror" 
                               name="image[]" multiple accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" id="imageInput"/>
                        <small class="form-text text-muted">You can select multiple images. Accepted formats: JPG, PNG, GIF, WEBP (Max: 2MB each)</small>
                    </div>
                    
                    @error('image')
                        <span class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    
                    @if ($errors->has('image.*'))
                        @foreach ($errors->get('image.*') as $messages)
                            @foreach ($messages as $message)
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @endforeach
                        @endforeach
                    @endif
                </div>

                <!-- Image Preview Section -->
                <div class="col-12">
                    <div id="imagePreview" class="row g-3 my-2" style="display: none;">
                        <label class="form-label">Image Preview:</label>
                        <div id="previewContainer" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-sm btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.getElementById('previewContainer');
            const imagePreview = document.getElementById('imagePreview');
            
            // Clear previous previews
            previewContainer.innerHTML = '';
            
            if (files.length > 0) {
                imagePreview.style.display = 'block';
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imageDiv = document.createElement('div');
                            imageDiv.className = 'position-relative';
                            imageDiv.innerHTML = `
                                <img src="${e.target.result}" 
                                     class="img-thumbnail" 
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <small class="d-block text-center mt-1">${file.name}</small>
                            `;
                            previewContainer.appendChild(imageDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                imagePreview.style.display = 'none';
            }
        });
    </script>
@endsection
