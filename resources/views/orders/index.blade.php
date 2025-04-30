@extends('layouts.app')
@section('title', 'My Orders')

@section('content')

<div class="container mx-auto px-4 py-12">

    <div class="bg-white shadow-2xl rounded-3xl overflow-hidden transition-all duration-500">
        <div class="p-8 sm:p-10">

            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-10">
                <div class="text-center sm:text-left">
                    <h2 class="text-3xl font-extrabold text-primary mb-1">My Orders</h2>
                    <p class="text-gray-500 text-sm">View all your recent purchases and track their status here.</p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 mt-6 sm:mt-0 bg-primary/10 text-primary text-sm font-semibold px-5 py-2.5 rounded-full hover:bg-primary/20 transition-all">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
            </div>

            {{-- Orders List --}}
            @if($orders->count())

                <div class="overflow-x-auto border rounded-2xl">
                    <table class="min-w-full table-auto text-sm text-center">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Transaction ID</th>
                                <th class="py-4 px-6">Product</th>
                                <th class="py-4 px-6">Quantity</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Date</th>
                                <th class="py-4 px-6">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">

                            @foreach($orders as $index => $order)
                                <tr class="opacity-0 transform translate-y-5 scale-95 transition-all duration-700 ease-out delay-{{ $index * 75 }} hover:scale-100 hover:bg-gray-50">
                                    <td class="py-4 px-6 break-words">{{ $order->api_order_id ?? 'Manual Order' }}</td>
                                    <td class="py-4 px-6 font-semibold text-gray-700">{{ $order->product_name ?? 'Unknown Product' }}</td>
                                    <td class="py-4 px-6">{{ $order->quantity }}</td>
                                    <td class="py-4 px-6">
                                        @switch($order->status)
                                            @case('completed')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Completed</span>
                                                @break
                                            @case('pending')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Pending</span>
                                                @break
                                            @case('failed')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">Failed</span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($order->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="py-4 px-6 text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="py-4 px-6">
                                        @if($order->api_order_id)
                                            <a href="{{ route('order', ['api_order_id' => $order->api_order_id]) }}" 
                                               class="inline-flex items-center gap-2 bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-semibold hover:bg-primary/20 transition-all">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @else
                                            <a href="{{ route('manual.order', ['id' => $order->id]) }}" 
                                               class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-4 py-2 rounded-full text-sm font-semibold hover:bg-gray-200 transition-all">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-10 flex justify-center">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>

            @else
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center text-center py-24">
                    <div class="animate-bounce">
                        <i class="fas fa-box-open fa-6x text-primary mb-6"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-primary mb-2">No Orders Yet</h3>
                    <p class="text-gray-500 mb-6">You haven't placed any orders yet. When you do, they'll appear here.</p>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-full text-sm font-semibold hover:bg-primary-dark transition">
                        <i class="fas fa-store"></i> Start Shopping
                    </a>
                </div>
            @endif

        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('tbody tr').forEach(function(row) {
        setTimeout(() => {
            row.classList.remove('opacity-0', 'translate-y-5', 'scale-95');
        }, 300);
    });
});
</script>
@endsection
