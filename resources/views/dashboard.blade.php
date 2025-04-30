@extends('layouts.app')

@section('content')
<div class="container-fluid px-0 py-4">

    {{-- Full-width Slider --}}
    <div id="promoCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-inner rounded-0">
            <div class="carousel-item active">
                <img src="{{ asset('images/slide1.jpg') }}" class="d-block w-100" alt="Promotion 1" style="height: 200px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block">
                    <h5 class="fw-bold fs-6">Welcome to Our Store</h5>
                    <p class="fs-6">Enjoy great deals on all products today!</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="{{ asset('images/slide2.jpg') }}" class="d-block w-100" alt="Promotion 2" style="height: 200px; object-fit: cover;">
                <div class="carousel-caption d-none d-md-block">
                    <h5 class="fw-bold fs-6">Special Offers</h5>
                    <p class="fs-6">Don't miss out on exclusive discounts.</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
    </div>

    {{-- Categories and Products --}}
    <div class="px-0">
        <h3 class="fw-bold text-primary mb-4">Categories & Products</h3>

        @if($categories->count())
        <div class="accordion" id="categoriesAccordion">
            @foreach($categories as $index => $category)
            <div class="accordion-item border-0 mb-4 shadow-sm rounded-0">
                <h2 class="accordion-header" id="heading{{ $index }}">
                    <button class="accordion-button fw-bold text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="true" aria-controls="collapse{{ $index }}" style="background-color: #152c5b;">
                        {{ $category->name }}
                    </button>
                </h2>
                <div id="collapse{{ $index }}" class="accordion-collapse collapse show" data-bs-parent="#categoriesAccordion">
                    <div class="accordion-body px-1">
                        @if($category->products->count())
                        <div class="row g-2">
                            @foreach($category->products as $product)
                            <div class="col-12 col-md-6 col-lg-4 px-1">
                                <div class="card h-100 shadow-sm rounded-0 border-0 p-2 product-card">
                                    <h6 class="fw-bold mb-2">
                                        <a href="{{ route('product', $product->api_product_id ?? $product->id) }}" class="text-decoration-none text-dark">
                                            {{ $product->name }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-2">Format: ID | Password | Mail | Recovery Info</p>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-light border text-primary px-3 py-2">
                                            ₦{{ number_format(
                                                !empty($product->api_product_id) ? 
                                                ($product->price * $conversionRate + $adminGain) : 
                                                $product->price, 2) }}
                                        </span>
                                        <span class="badge bg-light border text-success px-3 py-2">
                                            🟢 Stock: {{ $product->stock }}
                                        </span>
                                    </div>

                                    @if($product->stock > 0)
                                    <button class="btn btn-sm btn-primary w-100 fw-bold" 
                                        onclick="openBuyNowModal(
                                            {{ $product->api_product_id ?? $product->id }}, 
                                            '{{ addslashes($product->name) }}', 
                                            {{ $product->stock }}, 
                                            {{ !empty($product->api_product_id) ? ($product->price * $conversionRate + $adminGain) : $product->price }}
                                        )">
                                        🛒 BUY NOW
                                    </button>
                                    @else
                                    <button class="btn btn-sm btn-secondary w-100" disabled>
                                        😞 OUT OF STOCK
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted">No products under this category.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="alert alert-warning" role="alert">
            No categories found.
        </div>
        @endif
    </div>
</div>

{{-- Buy Now Modal --}}
<div class="modal fade" id="buyNowModal" tabindex="-1" aria-labelledby="buyNowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <form method="POST" action="{{ route('purchase', ['id' => 0]) }}" id="buyNowForm" class="w-100">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Purchase Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="productName" class="fw-bold text-primary"></p>
                    <label for="quantity" class="form-label">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control mb-2" required>
                    <div id="stockInfo" class="text-muted small"></div>
                    <div id="priceInfo" class="text-muted small"></div>
                    <div id="totalPrice" class="fw-bold text-danger"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Purchase Now</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- CSS Fixes --}}
<style>
@media (max-width: 576px) {
    .modal-dialog {
        margin: 0;
        max-width: 100%;
    }
    .modal-content {
        border-radius: 0;
    }
    .card {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        border-radius: 0 !important;
    }
    .accordion-body {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
    .col-12 {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}

/* Hover effect for product cards (desktop only) */
@media (min-width: 577px) {
    .product-card:hover {
        transform: scale(1.01);
        transition: all 0.2s ease-in-out;
        box-shadow: 0 0 12px rgba(0, 0, 0, 0.12);
    }
}

/* Button hover */
.btn-primary:hover {
    background-color: #0d6efd;
    opacity: 0.9;
    transition: background-color 0.2s ease-in-out, opacity 0.2s ease-in-out;
}

/* Reduce font size for all product card content */
.product-card * {
    font-size: 12px !important;
    line-height: 1.4;
}
</style>

{{-- Scripts --}}
<script>
    function openBuyNowModal(productId, productName, stock, price) {
        const form = document.getElementById('buyNowForm');
        form.action = "{{ url('/purchase') }}/" + productId;
        document.getElementById('productName').innerText = productName;
        document.getElementById('stockInfo').innerText = "Stock Available: " + stock;
        document.getElementById('priceInfo').innerHTML = "Price: ₦" + price.toFixed(2);
        const quantityInput = document.getElementById('quantity');

        quantityInput.addEventListener('input', function () {
            const total = price * this.value;
            document.getElementById('totalPrice').innerText = "Total: ₦" + total.toFixed(2);
        });

        document.getElementById('totalPrice').innerText = "Total: ₦" + (price * quantityInput.value).toFixed(2);
        new bootstrap.Modal(document.getElementById('buyNowModal')).show();
    }

    @if(session('message'))
    Swal.fire({
        title: 'Success!',
        text: "{{ session('message') }}",
        icon: 'success',
        confirmButtonText: 'OK'
    });
    @endif

    @if(session('error'))
    Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonText: 'OK'
    });
    @endif
</script>
@endsection
