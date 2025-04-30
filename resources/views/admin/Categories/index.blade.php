@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    {{-- Page Header --}}
    <div class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-4xl font-bold text-gray-900">Categories Management</h2>
            <p class="text-gray-500 mt-1">View and manage all product categories here</p>
        </div>

        {{-- Add New Category Button --}}
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-lg transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New
        </a>
    </div>
    {{-- Search Bar --}}
<div class="mb-6 flex items-center gap-2">
    <div class="relative w-full md:w-1/3">
        <input type="text" id="searchCategoryInput" placeholder="Search categories..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">

        {{-- Search Icon inside input --}}
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 3.5a7.5 7.5 0 0013.15 13.15z" />
            </svg>
        </div>
    </div>
</div>


    {{-- Categories Table --}}
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200" id="categoriesTable">
                <thead class="bg-gray-100 text-gray-700 text-xs font-semibold uppercase">
                    <tr>
                        <th class="px-6 py-4 text-left">ID</th>
                        <th class="px-6 py-4 text-left">Category Name</th>
                        <th class="px-6 py-4 text-left">Created</th>
                        <th class="px-6 py-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-900">#{{ $category->id }}</td>
                            <td class="px-6 py-4 category-name">{{ $category->name }}</td>
                            <td class="px-6 py-4">{{ $category->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if(optional($category)->is_admin_created)
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                           Edit
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold transition">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-4 py-2 bg-gray-200 text-gray-600 text-xs font-semibold rounded-lg">
                                            System Category
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-400">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $categories->links('pagination::tailwind') }}
        </div>
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

    // Live search categories
    document.getElementById('searchCategoryInput').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('#categoriesTable tbody tr').forEach((row) => {
            const name = row.querySelector('.category-name')?.textContent.toLowerCase() || '';
            row.style.display = name.includes(keyword) ? '' : 'none';
        });
    });
</script>

@endsection
