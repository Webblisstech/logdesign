@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-6">

    <h2 class="text-3xl font-bold text-primary mb-8">Order Details</h2>

    {{-- Order Info Card --}}
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-xl font-semibold mb-2 text-gray-800">Product Name: {{ $order->product_name }}</h3>
        <p class="text-gray-600 mb-1"><span class="font-medium">Quantity:</span> {{ $order->quantity }}</p>
        <p class="text-gray-600 mb-1"><span class="font-medium">Total Price:</span> ₦{{ number_format($order->total_price, 2) }}</p>
        <p class="text-gray-600 mb-1"><span class="font-medium">Status:</span> {{ ucfirst($order->status) }}</p>
        <p class="text-gray-600"><span class="font-medium">Created At:</span> {{ $order->created_at->format('d M Y, h:i A') }}</p>
    </div>

    {{-- Credentials Section --}}
    @if($order->order_details)
        <div class="bg-white shadow-md rounded-xl mb-6">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h4 class="text-lg font-semibold text-gray-800">Credentials (Purchased)</h4>
                <button type="button" onclick="copyAllCredentials()"
    class="inline-flex items-center text-sm font-semibold text-primary border border-primary px-4 py-1.5 rounded hover:bg-primary hover:text-white transition">
    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V6a2 2 0 00-2-2h-6l-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2h2" />
    </svg>
    Copy All
</button>

            </div>
            <div class="px-6 py-4">
                <pre id="credentialsText" class="bg-gray-50 p-4 rounded text-sm text-gray-800 whitespace-pre-wrap overflow-x-auto">{{ $order->order_details }}</pre>
            </div>
        </div>
    @endif

    {{-- Back Button --}}
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center text-sm font-semibold text-primary border border-primary px-4 py-2 rounded hover:bg-primary hover:text-white transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 19l-7-7 7-7" />
        </svg>
        Back
    </a>

</div>


<script>
    function copyAllCredentials() {
        const credentialsEl = document.getElementById('credentialsText');
        if (credentialsEl) {
            const text = credentialsEl.innerText || credentialsEl.textContent;
            navigator.clipboard.writeText(text.trim()).then(() => {
                alert("Credentials copied successfully!");
            }).catch(err => {
                console.error("Copy failed", err);
                alert("Failed to copy credentials.");
            });
        }
    }
</script>
@endsection
