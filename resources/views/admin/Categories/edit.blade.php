@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6">
    <h2 class="text-3xl font-bold mb-8 text-primary">Edit Category</h2>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-600">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li class="mt-1">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-6 bg-white p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <div>
            <label class="block font-semibold mb-2">Category Name</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                   required>
        </div>

        <div>
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-lg transition">
                Update Category
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
