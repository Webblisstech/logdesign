@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">All Orders</h2>

    <div class="bg-white shadow-md rounded-xl overflow-x-auto">
        <div class="p-6">

            {{-- Orders Table --}}
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Order ID</th>
                        <th class="px-4 py-3 text-left">Product Name</th>
                        <th class="px-4 py-3 text-left">User Email</th>
                        <th class="px-4 py-3 text-left">Quantity</th>
                        <th class="px-4 py-3 text-left">Total Price</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Created At</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $order->id }}</td>
                            <td class="px-4 py-3">{{ $order->product_name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">{{ $order->user->email ?? 'Unknown' }}</td>
                            <td class="px-4 py-3">{{ $order->quantity }}</td>
                            <td class="px-4 py-3">₦{{ number_format($order->total_price, 2) }}</td>
                            <td class="px-4 py-3 capitalize">{{ $order->status }}</td>
                            <td class="px-4 py-3">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $orders->links() }}
            </div>

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
</script>

@endsection
