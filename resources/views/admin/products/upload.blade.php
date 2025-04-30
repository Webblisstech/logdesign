@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">Upload Products (.txt)</h2>

    {{-- Success Message --}}
    @if(session('message'))
        <div class="bg-green-100 text-green-700 p-4 rounded-lg mb-6">
            {{ session('message') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-xl p-8">

        <form action="{{ route('admin.products.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Select Category --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Select Category</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none" required>
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <a href="{{ route('admin.categories.create') }}" class="text-primary text-sm mt-2 inline-block hover:underline">+ Create New Category</a>
            </div>

            {{-- Product Name --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Product Name</label>
                <input type="text" name="product_name" placeholder="Enter product title" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none" required>
            </div>

            {{-- Product Price --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Product Price (₦)</label>
                <input type="number" name="price" step="0.01" placeholder="Enter product price" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none" required>
            </div>

            {{-- Default Description --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Default Description (optional)</label>
                <textarea name="default_description" rows="3" placeholder="Enter default description (optional)" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none"></textarea>
            </div>

            {{-- Upload File --}}
            <div>
                <label class="block text-sm font-semibold mb-2">Upload .txt File (one line per product)</label>
                <input type="file" name="file" accept=".txt" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:outline-none" required>
            </div>

            {{-- Submit Button --}}
            <div>
                <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-primary-dark transition duration-300">
                    Upload Products
                </button>
            </div>

        </form>

    </div>

</div>
@endsection
