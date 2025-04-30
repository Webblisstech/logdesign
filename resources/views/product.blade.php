@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="small">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success Message --}}
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- Product Details Card --}}
    <div class="card shadow-lg rounded-4 border-0 mb-5">
        <div class="card-body p-5">

            @if(isset($product) && !isset($product['error']) && isset($product['id']))
                @php
                    $apiPrice = isset($product['price']) ? (float) $product['price'] : 0;
                    $priceInNaira = ($apiPrice * $conversionRate) + $adminGain;
                    $isOutOfStock = $product['amount'] == 0;
                @endphp

                <h2 class="card-title text-primary fw-bold mb-4">
                    {{ $product['name'] ?? 'Unnamed Product' }}
                </h2>

                <p class="text-muted mb-5">
                    {{ $product['description'] ?? 'No description available.' }}
                </p>

                <div class="row mb-5">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="bg-light rounded-3 p-4 h-100 d-flex flex-column justify-content-center">
                            <small class="text-muted mb-1">Price (Naira)</small>
                            <h3 class="text-success fw-bold">
                                ₦{{ number_format($priceInNaira, 2) }}
                            </h3>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-light rounded-3 p-4 h-100 d-flex flex-column justify-content-center">
                            <small class="text-muted mb-1">Available Stock</small>
                            <h4 class="text-primary fw-bold">
                                {{ $product['amount'] ?? '0' }} units
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Buy Now Form --}}
                <form method="POST" action="{{ route('purchase', $product['id']) }}" class="row g-3">
                    @csrf

                    <div class="col-12 col-md-6">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input 
                            type="number" 
                            name="quantity" 
                            id="quantity" 
                            value="{{ $product['min'] ?? 1 }}" 
                            min="{{ $product['min'] ?? 1 }}" 
                            max="{{ $product['amount'] ?? 1 }}" 
                            required
                            class="form-control form-control-lg"
                            @if($isOutOfStock) disabled @endif
                        >
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-lg w-100 @if($isOutOfStock) btn-secondary @else btn-primary @endif" @if($isOutOfStock) disabled @endif>
                            @if($isOutOfStock) Out of Stock @else <i class="fas fa-shopping-cart me-2"></i> Buy Now @endif
                        </button>
                    </div>
                </form>

            @else
                {{-- Product Not Found --}}
                <div class="text-center">
                    <h2 class="text-danger fw-bold mb-3">
                        Unable to load product details.
                    </h2>
                    <a href="{{ route('dashboard') }}" class="btn btn-dark btn-lg">
                        ← Back to Dashboard
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- Back Button --}}
    <div class="text-center">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary rounded-pill px-5 py-2">
            ← Back to Dashboard
        </a>
    </div>

</div>
@endsection
