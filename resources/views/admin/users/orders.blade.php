@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-6">Orders for {{ $user->name }}</h2>

    {{-- Back to Users --}}
    <div class="mb-6">
        <a href="{{ route('admin.users') }}"
           class="inline-flex items-center gap-2 text-sm font-semibold text-primary border border-primary px-4 py-2 rounded hover:bg-primary hover:text-white transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 19l-7-7 7-7" />
            </svg>
            Back to Users
        </a>
    </div>

    @if($orders->count())
        <div class="bg-white shadow-md rounded-xl overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Order ID</th>
                        <th class="px-4 py-3 text-left">Product Name</th>
                        <th class="px-4 py-3 text-left">Quantity</th>
                        <th class="px-4 py-3 text-left">Total Price</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Created At</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $order->id }}</td>
                            <td class="px-4 py-3">{{ $order->product_name ?? 'Unknown Product' }}</td>
                            <td class="px-4 py-3">{{ $order->quantity }}</td>
                            <td class="px-4 py-3">₦{{ number_format($order->total_price, 2) }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClasses = match($order->status) {
                                        'completed' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'failed' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="inline-block text-xs font-medium px-3 py-1 rounded-full {{ $badgeClasses }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.order.view', $order->id) }}"
                                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-3 rounded">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $orders->links() }}
        </div>

    @else
        <div class="text-center py-10">
            <h4 class="text-gray-400 text-lg font-semibold">No orders found for this user.</h4>
        </div>
    @endif

</div>
@endsection
