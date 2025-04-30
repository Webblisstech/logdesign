@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    {{-- Flash Message --}}
    @if (session('message'))
        <div class="bg-green-500 text-white px-6 py-4 rounded-lg mb-8 flex justify-between items-center shadow-md">
            <span>{{ session('message') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-2xl leading-none hover:text-green-200">&times;</button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-12">
        <h2 class="text-4xl font-bold text-gray-900">Dashboard Overview</h2>
        <p class="text-gray-500 mt-2">Welcome back, Administrator</p>
    </div>

    {{-- Dashboard Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        @php
            $metrics = [
                ['title' => 'Total Orders', 'value' => $totalOrders, 'color' => 'blue', 'icon' => 'shopping-cart'],
                ['title' => 'Total Transactions', 'value' => $totalTransactions, 'color' => 'green', 'icon' => 'credit-card'],
                ['title' => 'Total Revenue', 'value' => '₦' . number_format($totalRevenue, 2), 'color' => 'purple', 'icon' => 'cash'],
            ];
        @endphp
        @foreach ($metrics as $metric)
            <div class="bg-gradient-to-br from-{{ $metric['color'] }}-500 to-{{ $metric['color'] }}-600 rounded-2xl p-6 shadow-lg hover:scale-105 transition transform">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-white/70 uppercase">{{ $metric['title'] }}</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $metric['value'] }}</p>
                    </div>
                    <div class="p-4 bg-{{ $metric['color'] }}-400 rounded-full">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @if ($metric['icon'] == 'shopping-cart')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13h10m-6 9a1 1 0 100-2 1 1 0 000 2zm6-2a1 1 0 100 2 1 1 0 000-2z" />
                            @elseif ($metric['icon'] == 'credit-card')
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @endif
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <div class="px-6 py-4 flex justify-between items-center bg-gray-50 border-b">
            <h5 class="text-lg font-semibold text-gray-800">Recent Orders</h5>
            <span class="text-sm text-gray-500">{{ $orders->count() }} orders found</span>
        </div>

        <div class="overflow-x-auto">
            @if ($orders->count())
                <table class="min-w-full text-sm divide-y divide-gray-200">
                    <thead class="bg-gray-100 text-gray-700 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4 text-left">Order ID</th>
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-left">User</th>
                            <th class="px-6 py-4 text-left">Quantity</th>
                            <th class="px-6 py-4 text-left">Total</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Details</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold">#{{ $order->id }}</td>
                                <td class="px-6 py-4">{{ $order->product_name ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $order->user->email ?? 'Guest' }}</td>
                                <td class="px-6 py-4">{{ $order->quantity }}</td>
                                <td class="px-6 py-4 font-semibold">₦{{ number_format($order->total_price, 2) }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $badgeColor = match($order->status) {
                                            'completed' => 'bg-green-100 text-green-700',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'failed' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                    @endphp
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $badgeColor }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <button class="text-indigo-600 hover:underline font-medium copy-details"
                                            data-details="{{ $order->order_details ?? 'No details available' }}">
                                        {{ \Illuminate\Support\Str::limit($order->order_details ?? 'No details', 25) }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 bg-gray-50 border-t">
                    {{ $orders->links('pagination::tailwind') }}
                </div>

            @else
                <div class="p-12 text-center text-gray-400">
                    <p class="text-lg">No orders available</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-12 flex justify-center space-x-6">
        <a href="{{ route('admin.orders') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-lg transform hover:scale-105">
            Manage Orders
        </a>
        <a href="{{ route('admin.transactions') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold shadow-lg transform hover:scale-105">
            View Transactions
        </a>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-details').forEach(button => {
            button.addEventListener('click', function () {
                const text = this.dataset.details;
                navigator.clipboard.writeText(text).then(() => {
                    // Floating Tailwind toast
                    const toast = document.createElement('div');
                    toast.className = 'fixed bottom-5 right-5 bg-gray-800 text-white px-5 py-3 rounded-lg shadow-lg animate-bounce';
                    toast.textContent = 'Copied to clipboard!';
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.remove();
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy:', err);
                });
            });
        });
    });
</script>
@endsection
