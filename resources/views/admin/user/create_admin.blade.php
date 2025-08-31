@extends('layouts.admin')
@section('content')
    <div class="position-relative border-radius-xl overflow-hidden shadow-lg mb-7 card">
        <div class="container border-bottom">
            <div class="row justify-space-between py-2">
                <div class="col-4 me-auto">
                    <p class="lead text-dark pt-1 mb-0">Add New Admin</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            @if ($errors->any())
                <div class="alert alert-danger text-white">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif
            
            <form class="row" action="{{ route('admin.admin.store') }}" method="POST">
                @csrf
                
                <!-- Name Field -->
                <div class="col-md-6">
                    <div class="input-group input-group-static my-3">
                        <label for="name">Full Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               name="name" 
                               id="name"
                               value="{{ old('name') }}" 
                               required>
                    </div>
                    @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Username Field -->
                <div class="col-md-6">
                    <div class="input-group input-group-static my-3">
                        <label for="username">Username <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('username') is-invalid @enderror" 
                               name="username" 
                               id="username"
                               value="{{ old('username') }}" 
                               required>
                    </div>
                    @error('username')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="col-md-6">
                    <div class="input-group input-group-static my-3">
                        <label for="email">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               name="email" 
                               id="email"
                               value="{{ old('email') }}" 
                               required>
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Mobile Field -->
                <div class="col-md-6">
                    <div class="input-group input-group-static my-3">
                        <label for="mobile">Mobile Number</label>
                        <input type="text" 
                               class="form-control @error('mobile') is-invalid @enderror" 
                               name="mobile" 
                               id="mobile"
                               value="{{ old('mobile') }}" 
                               placeholder="e.g., +62812345678">
                    </div>
                    @error('mobile')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="col-md-6">
                    <div class="input-group input-group-static my-3">
                        <label for="password">Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               name="password" 
                               id="password"
                               required
                               minlength="6">
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                    <small class="text-muted">Password must be at least 6 characters long.</small>
                </div>

                <!-- Confirm Password Field -->
                <div class="col-md-6">
                    <div class="input-group input-group-static my-3">
                        <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               class="form-control @error('password_confirmation') is-invalid @enderror" 
                               name="password_confirmation" 
                               id="password_confirmation"
                               required
                               minlength="6">
                    </div>
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="col-12">
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.user.index') }}" class="btn btn-outline-secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Create Admin
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show password validation
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
@endsection
