<x-guest-layout>

    <div class="flex items-center justify-center py-12">
        <div class="w-full max-w-sm p-8 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center mb-6">Log into Facebook</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <input 
                        type="email" 
                        name="email" 
                        required 
                        placeholder="Email address" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <input 
                        type="password" 
                        name="password" 
                        required 
                        placeholder="Password" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Submit -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-md text-lg"
                    >
                        Log In
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-guest-layout>
