@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">User Details</h2>

    {{-- User Info Card --}}
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $user->name }}</h3>
        <p class="text-gray-600 mb-1"><span class="font-medium">Email:</span> {{ $user->email }}</p>
        <p class="text-gray-600 mb-1"><span class="font-medium">Wallet Balance:</span> ₦{{ number_format($user->wallet, 2) }}</p>
        <p class="text-gray-600"><span class="font-medium">Joined:</span> {{ $user->created_at->format('d M Y, h:i A') }}</p>

        {{-- Ban/Unban Button --}}
        <div class="mt-4">
            @if(!$user->is_banned)
                <form action="{{ route('admin.user.ban', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-5 py-2 rounded">
                        Ban User
                    </button>
                </form>
            @else
                <form action="{{ route('admin.user.unban', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-5 py-2 rounded">
                        Unban User
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Fund and Debit Wallet --}}
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        {{-- Fund Wallet --}}
        <div class="bg-white shadow-md rounded-xl p-6">
            <h4 class="text-lg font-semibold mb-4 text-gray-800">Fund Wallet</h4>
            <form method="POST" action="{{ route('admin.user.fund', $user->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <input type="number" name="amount" min="1" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg">
                    Fund Wallet
                </button>
            </form>
        </div>

        {{-- Debit Wallet --}}
        <div class="bg-white shadow-md rounded-xl p-6">
            <h4 class="text-lg font-semibold mb-4 text-gray-800">Debit Wallet</h4>
            <form method="POST" action="{{ route('admin.user.debit', $user->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Amount</label>
                    <input type="number" name="amount" min="1" required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded-lg">
                    Debit Wallet
                </button>
            </form>
        </div>
    </div>

    {{-- Reset Password --}}
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h4 class="text-lg font-semibold mb-4 text-gray-800">Reset User Password</h4>
        <form method="POST" action="{{ route('admin.user.reset-password', $user->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">New Password</label>
                <input type="password" name="new_password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-yellow-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-lg">
                Reset Password
            </button>
        </form>
    </div>

    {{-- Action Links --}}
    <div class="flex flex-wrap items-center gap-4 mb-8">
        <a href="{{ route('admin.user.orders', $user->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded">
            View Orders
        </a>
        <a href="{{ route('admin.user.transactions', $user->id) }}" class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold px-5 py-2 rounded">
            View Wallet Transactions
        </a>
        <a href="{{ route('admin.users') }}" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-5 py-2 rounded">
            ← Back to Users
        </a>
    </div>

</div>
@endsection
