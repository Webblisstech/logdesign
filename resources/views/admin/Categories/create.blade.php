@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6">
    <h2 class="text-3xl font-bold mb-8 text-primary">Create New Category</h2>

    <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        @csrf

        <div>
            <label class="block font-semibold mb-2">Category Name</label>
            <input type="text" name="name"
                   value="{{ old('name') }}" {{-- old() for repopulating if validation error --}}
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none"
                   required>
        </div>

        <div>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold px-6 py-2 rounded-lg transition">
                Create Category
            </button>
        </div>
    </form>
</div>



<script>
    @if(session('message'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('message') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif
</script>

@endsection

