@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg rounded-4 border-0">
        <div class="card-body">
            <h2 class="card-title text-primary fw-bold mb-4">Admin Login</h2>

            <form method="POST" action="{{ route('admin.login.submit') }}">
    @csrf
    <input type="email" name="email" placeholder="Admin Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
        </div>
    </div>
</div>
@endsection
