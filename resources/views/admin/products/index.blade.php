@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">All Products</h2>
        <a href="{{ route('admin.products.upload') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded shadow">+ Add New</a>
    </div>

    {{-- Search --}}
    <div class="mb-6 flex justify-between items-center">
        <input type="text" id="searchInput" placeholder="Search products..." class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block bg-white rounded-xl shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm" id="productTable">
            <thead class="bg-indigo-600 text-white text-xs uppercase font-semibold tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Name | Category</th>
                    <th class="px-6 py-3 text-left">Price</th>
                    <th class="px-6 py-3 text-center">Stock</th>
                    <th class="px-6 py-3 text-center">Status</th>
                    <th class="px-6 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium product-name">{{ Str::limit($product->name, 60) }}</div>
                            <div class="text-xs text-gray-500 uppercase mt-1 product-category">{{ $product->category->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4">₦{{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $product->stock }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $enabled = $product->stock > 0;
                            @endphp
                            <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $enabled ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $enabled ? 'Enabled' : 'Disabled' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right relative z-50">
                            <div x-data="{ open: false }" class="inline-block text-left w-full">
                                <button @click="open = !open"
                                        class="inline-flex justify-center w-full px-4 py-2 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700 transition">
                                    ⋮ Action
                                </button>

                                <div x-show="open" x-cloak @click.outside="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg overflow-hidden z-50">
                                    <div class="py-1 text-sm text-gray-800">
                                        @if($product->productStocks()->exists())
                                            <a href="{{ route('admin.products.addStockForm', $product->id) }}"
                                               class="block px-4 py-2 hover:bg-indigo-100 text-indigo-600 font-medium">
                                                ➕ Add Stock
                                            </a>
                                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST"
                                                  onsubmit="return confirm('Delete this manual product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 hover:bg-red-100 text-red-600 font-medium">
                                                    🗑️ Delete Manual
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.products.api.delete', $product->id) }}" method="POST"
                                                  onsubmit="return confirm('Delete this API product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="w-full text-left px-4 py-2 hover:bg-red-100 text-red-600 font-medium">
                                                    🗑️ Delete API Product
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile Cards --}}
    <div class="md:hidden space-y-4" id="mobileProductList">
        @foreach($products as $product)
            <div class="bg-white shadow rounded-xl p-4">
                <div class="text-sm font-bold text-gray-800 mb-1 product-name">{{ $product->name }}</div>
                <div class="text-xs text-gray-500 uppercase mb-2 product-category">{{ $product->category->name ?? 'N/A' }}</div>

                <div class="text-sm text-gray-700 mb-1">
                    <strong>Price:</strong> ₦{{ number_format($product->price, 2) }}
                </div>
                <div class="text-sm text-gray-700 mb-1">
                    <strong>In Stock:</strong> 
                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">{{ $product->stock }}</span>
                </div>
                <div class="text-sm text-gray-700 mb-3">
                    <strong>Status:</strong>
                    <span class="inline-block {{ $product->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }} text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $product->stock > 0 ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>

                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            class="w-full text-xs px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-medium">
                        ⋮ Action
                    </button>

                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-full bg-white border border-gray-200 rounded shadow z-50 text-sm">

                        @if($product->productStocks()->exists())
                            <a href="{{ route('admin.products.addStockForm', $product->id) }}"
                               class="block px-4 py-2 hover:bg-indigo-50 text-indigo-700 font-semibold">
                                ➕ Add Stock
                            </a>
                            <form action="{{ route('admin.products.delete', $product->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this manual product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 font-semibold">
                                    🗑️ Delete Manual
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.products.api.delete', $product->id) }}" method="POST"
                                  onsubmit="return confirm('Delete this API product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 hover:bg-red-50 text-red-600 font-semibold">
                                    🗑️ Delete API Product
                                </button>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div> {{-- closes max-w-7xl --}}
@endsection

@section('scripts')
<script>
    // Live search filter for both desktop and mobile
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();

        document.querySelectorAll('.product-name').forEach((el, i) => {
            const category = document.querySelectorAll('.product-category')[i];
            const row = el.closest('tr') || el.closest('.bg-white.shadow');

            const match = el.textContent.toLowerCase().includes(keyword) || (category?.textContent.toLowerCase().includes(keyword));
            row.style.display = match ? '' : 'none';
        });
    });
</script>
@endsection
