@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">Add Stock to: {{ $product->name }}</h2>

    <div class="bg-white shadow-md rounded-xl p-8">

        <form action="{{ route('admin.products.storeStock', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- File Upload --}}
            <div>
                <label for="file" class="block text-sm font-semibold mb-2">Upload New Stock (.txt file)</label>
                <input type="file" name="file" id="file" accept=".txt" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none">
            </div>

            {{-- Submit Button --}}
            <div>
                <button type="submit"
                    class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-primary-dark transition duration-300">
                    Upload Stock
                </button>
            </div>

        </form>

    </div>

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
