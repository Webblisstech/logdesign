@extends('layouts.app')

@section('content')

<!-- Bootstrap 5 CSS CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Animate.css for smooth effects -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
        <br>
        <br>
            <!-- Profile Header -->
            <div class="text-center mb-5">
                <h1 class="fw-bold text-primary mb-3 animate__animated animate__fadeInDown">
                    {{ __('Profile Settings') }}
                </h1>
                <p class="text-muted">Manage your account details, update your password, or delete your profile.</p>
            </div>

            <!-- Update Profile Information -->
            <div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="text-primary mb-0">Update Profile Information</h5>
                </div>
                <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    {{-- Full Name Field (Editable) --}}
    <div class="mb-4">
        <label for="name" class="form-label">Full Name</label>
        <input 
            type="text" 
            id="name" 
            name="name" 
            class="form-control" 
            value="{{ old('name', auth()->user()->name) }}" 
            required
        >
    </div>

    {{-- Email Field (Read Only / Disabled) --}}
    <div class="mb-4">
        <label for="email" class="form-label">Email Address</label>
        <input 
            type="email" 
            id="email" 
            class="form-control" 
            value="{{ auth()->user()->email }}" 
            disabled
        >
    </div>

    {{-- Save Button --}}
    <div class="d-flex justify-content-center">
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    </div>
</form>

                </div>
            </div>

            <!-- Update Password -->
            <div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="text-success mb-0">Update Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Current Password -->
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" required class="form-control form-control-lg">
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" required class="form-control form-control-lg">
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required class="form-control form-control-lg">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card mb-4 shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-header bg-transparent border-bottom-0">
                    <h5 class="text-danger mb-0">Delete Account</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-4" role="alert">
                        <strong>Warning:</strong> Once you delete your account, there is no going back. Please be certain.
                    </div>

                    <form method="POST" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('DELETE')

                        <div class="mb-3">
                            <label class="form-label">Enter Password to Confirm</label>
                            <input type="password" name="password" required class="form-control form-control-lg">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg">Delete Account</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#3085d6'
        });
    @endif

    @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: "{{ session('error') }}",
            confirmButtonColor: '#d33'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error!',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#d33'
        });
    @endif
</script>


@endsection
