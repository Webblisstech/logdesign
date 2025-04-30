@extends('layouts.app')
@section('title', 'Order Details')

@section('content')
<div class="container py-5">

    <div class="card shadow rounded-4 border-0">
        <div class="card-body p-5">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h2 class="fw-bold text-primary mb-0">Order Summary</h2>
                <a href="{{ route('orders') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-2"></i> My Orders
                </a>
            </div>

            {{-- Order Info --}}
            <div class="row gy-4 mb-5">

                <div class="col-md-6">
                    <div class="p-4 bg-light rounded-3 shadow-sm h-100">
                        <h5 class="text-muted mb-2">Product Name</h5>
                        <h4 class="fw-bold text-dark">{{ $order->product_name }}</h4>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-4 bg-light rounded-3 shadow-sm h-100 text-center">
                        <h5 class="text-muted mb-2">Quantity</h5>
                        <h4 class="fw-bold">{{ $order->quantity }}</h4>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-4 bg-light rounded-3 shadow-sm h-100 text-center">
                        <h5 class="text-muted mb-2">Total Price</h5>
                        <h4 class="fw-bold text-success">₦{{ number_format($order->total_price, 2) }}</h4>
                    </div>
                </div>

            </div>

            {{-- Credentials Section --}}
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="text-muted fw-bold mb-0">Purchased Credentials</h5>
                    <button id="copyButton" class="btn btn-primary btn-sm rounded-pill transition-all" onclick="copyAllCredentials()">
                        <span id="copyButtonText">
                            <i class="fas fa-copy me-1"></i> Copy All
                        </span>
                    </button>
                </div>

                <div class="position-relative">
                    <pre id="credentialsText" class="bg-white border rounded-3 p-4 mt-3 shadow-sm" style="white-space: pre-wrap; font-size: 1rem;">{{ $order->order_details }}</pre>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
function copyAllCredentials() {
    const credentialsText = document.getElementById('credentialsText').innerText.trim();
    const copyButton = document.getElementById('copyButton');
    const copyButtonText = document.getElementById('copyButtonText');

    navigator.clipboard.writeText(credentialsText)
        .then(() => {
            // Slide out
            copyButtonText.style.transform = 'translateY(-100%)';
            copyButtonText.style.opacity = '0';

            setTimeout(() => {
                copyButtonText.innerHTML = '<i class="fas fa-check-circle me-1"></i> Copied!';
                copyButton.classList.remove('btn-primary');
                copyButton.classList.add('btn-success');

                // Slide in
                copyButtonText.style.transform = 'translateY(0)';
                copyButtonText.style.opacity = '1';
            }, 300);

            // Reset after 2 seconds
            setTimeout(() => {
                copyButtonText.style.transform = 'translateY(-100%)';
                copyButtonText.style.opacity = '0';

                setTimeout(() => {
                    copyButtonText.innerHTML = '<i class="fas fa-copy me-1"></i> Copy All';
                    copyButton.classList.remove('btn-success');
                    copyButton.classList.add('btn-primary');

                    copyButtonText.style.transform = 'translateY(0)';
                    copyButtonText.style.opacity = '1';
                }, 300);
            }, 2300);
        })
        .catch(err => {
            alert('Failed to copy credentials.');
        });
}
</script>

<style>
#copyButtonText {
    display: inline-block;
    transition: all 0.3s ease-in-out;
}
</style>
@endsection
