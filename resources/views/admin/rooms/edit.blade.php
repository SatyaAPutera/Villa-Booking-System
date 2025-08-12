@extends('layouts.admin')
@section('content')
    <div class="position-relative border-radius-xl overflow-hidden shadow-lg mb-7 card">
        <div class="container border-bottom">
            <div class="row justify-space-between py-2">
                <div class="col-4 me-auto">
                    <p class="lead text-dark pt-1 mb-0">Edit Room Details</p>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <p class="alert alert-danger text-white">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </p>
            @endif
            <form class="row" action="{{ route('admin.rooms.update', $room->uuid) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="room_no">Room No</label>
                        <input type="text" class="form-control disabled @error('room_no') is-invalid @enderror"
                            name="room_no" value='{{ $room->room_no }}'>
                    </div>
                    @error('room_no')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="name">Room Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        value="{{ $room->name }}" />
                    </div>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="description">Room Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" name="description" 
                                  rows="3" placeholder="Enter room description">{{ $room->description }}</textarea>
                    </div>
                    @error('description')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="col-6">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="rate">Room Rate (per night)</label>
                        <input type="number" step="0.01" class="form-control @error('rate') is-invalid @enderror" 
                               name="rate" value="{{ $room->rate }}" placeholder="0.00"/>
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
                               name="occupancy" value="{{ $room->occupancy ?? '' }}" placeholder="2"/>
                    </div>
                    @error('occupancy')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                
                <!-- Current Images Display -->
                @if($room->image && is_array($room->image) && count($room->image) > 0)
                <div class="col-12">
                    <div class="my-3">
                        <label class="form-label">Current Images:</label>
                        <div class="d-flex flex-wrap gap-2" id="currentImagesContainer">
                            @foreach($room->image as $index => $imagePath)
                            <div class="position-relative" id="image-{{ $index }}">
                                <img src="{{ asset($imagePath) }}" 
                                     class="img-thumbnail" 
                                     style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;"
                                     alt="Room Image"
                                     onclick="openImageModal('{{ asset($imagePath) }}', '{{ basename($imagePath) }}')"
                                     title="Click to view fullscreen">
                                <small class="d-block text-center mt-1">{{ basename($imagePath) }}</small>
                                
                                <!-- Delete Button -->
                                <button type="button" 
                                        class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                        style="width: 25px; height: 25px; padding: 0; font-size: 12px; border-radius: 50%;"
                                        onclick="deleteImage({{ $index }}, '{{ $imagePath }}')"
                                        title="Delete Image">
                                    <i class="fa fa-times"></i>
                                </button>
                                
                                <!-- Hidden input to track this image for deletion -->
                                <input type="hidden" name="existing_images[]" value="{{ $imagePath }}" id="existing-{{ $index }}">
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Hidden inputs for images to delete -->
                        <div id="deleteImageInputs"></div>
                    </div>
                </div>
                @endif
                
                <!-- Image Upload Section -->
                <div class="col-12">
                    <div class="input-group input-group-static my-3">
                        <label class="" for="image">Add New Images</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror @error('image.*') is-invalid @enderror" 
                               name="image[]" multiple accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" id="imageInput"/>
                        <small class="form-text text-muted">You can select multiple images to add. Accepted formats: JPG, PNG, GIF, WEBP (Max: 10MB each)</small>
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
                        <label class="form-label">New Image Preview:</label>
                        <div id="previewContainer" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>

                <div class="col-12 text-end">
                    <a href="{{ route('admin.rooms.index') }}" class="btn btn-sm btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-sm btn-primary">Update Room</button>
                </div>
            </form>
        </div>
    </div>

<!-- Image Fullscreen Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-0">
                <img id="modalImage" src="" alt="Fullscreen Image" class="img-fluid" style="max-height: 80vh; width: auto;">
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <p class="text-white mb-0" id="modalImageName"></p>
            </div>
        </div>
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

        // Delete image functionality
        function deleteImage(index, imagePath) {
            if (confirm('Are you sure you want to delete this image?')) {
                // Hide the image container
                const imageContainer = document.getElementById('image-' + index);
                if (imageContainer) {
                    imageContainer.style.display = 'none';
                }
                
                // Remove the existing image input
                const existingInput = document.getElementById('existing-' + index);
                if (existingInput) {
                    existingInput.remove();
                }
                
                // Add to delete list
                const deleteInputs = document.getElementById('deleteImageInputs');
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_images[]';
                deleteInput.value = imagePath;
                deleteInputs.appendChild(deleteInput);
                
                // Show success message
                showNotification('Image marked for deletion. Save the form to permanently remove it.', 'warning');
            }
        }

        // Open fullscreen image modal
        function openImageModal(imageSrc, imageName) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalImageName').textContent = imageName || 'Room Image';
            document.getElementById('imageModalLabel').textContent = 'Image Preview - ' + (imageName || 'Room Image');
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        }

        // Show notification function
        function showNotification(message, type = 'info') {
            // Remove existing notifications
            const existingNotification = document.querySelector('.notification-toast');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create notification
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} notification-toast`;
            notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
            notification.innerHTML = `
                <button type="button" class="btn-close" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 4000);
        }

        // Keyboard support for image modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
                if (modal) {
                    modal.hide();
                }
            }
        });
    </script>
@endsection
