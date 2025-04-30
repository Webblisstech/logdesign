@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">All Transactions</h2>

    <div class="bg-white shadow-md rounded-xl overflow-x-auto">
        <div class="p-6">

            <table class="min-w-full table-auto">
                <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Transaction ID</th>
                        <th class="px-4 py-3 text-left">User Name</th>
                        <th class="px-4 py-3 text-left">Amount</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-left">Created At</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $transaction->id }}</td>
                            <td class="px-4 py-3">{{ $transaction->user->name }}</td>
                            <td class="px-4 py-3">₦{{ number_format($transaction->amount, 2) }}</td>
                            <td class="px-4 py-3 capitalize">{{ $transaction->type }}</td>
                            <td class="px-4 py-3">{{ $transaction->description }}</td>
                            <td class="px-4 py-3">{{ $transaction->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $transactions->links() }}
            </div>

        </div>
    </div>

</div>
@endsection
