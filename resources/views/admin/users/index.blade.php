@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">All Users</h2>

    {{-- Search Bar --}}
    {{-- Search Form --}}
<form method="GET" action="{{ route('admin.users') }}" class="flex items-center gap-2 mb-6">
    <div class="relative w-full md:w-1/3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search users by name or email..."
               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">

        {{-- Search Icon inside the input --}}
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 103.5 3.5a7.5 7.5 0 0013.15 13.15z"/>
            </svg>
        </div>
    </div>

    {{-- Submit Button --}}
    <button type="submit"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow">
        Search
    </button>
</form>



    <div class="bg-white shadow-md rounded-xl overflow-x-auto">
        <div class="p-6">

            <table class="min-w-full table-auto" id="usersTable">
                <thead class="bg-gray-100 text-gray-700 text-sm font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">User ID</th>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Wallet Balance</th>
                        <th class="px-4 py-3 text-left">Joined</th>
                        <th class="px-4 py-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $user->id }}</td>
                            <td class="px-4 py-3 user-name">{{ $user->name }}</td>
                            <td class="px-4 py-3 user-email">{{ $user->email }}</td>
                            <td class="px-4 py-3">₦{{ number_format($user->wallet, 2) }}</td>
                            <td class="px-4 py-3">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.user.view', $user->id) }}"
                                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 px-4 rounded">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $users->links() }}
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    // Live search for users
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase();

        document.querySelectorAll('#usersTable tbody tr').forEach((row) => {
            const name = row.querySelector('.user-name')?.textContent.toLowerCase() || '';
            const email = row.querySelector('.user-email')?.textContent.toLowerCase() || '';

            const match = name.includes(keyword) || email.includes(keyword);
            row.style.display = match ? '' : 'none';
        });
    });
</script>
@endsection
