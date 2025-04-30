@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- Page Header --}}
    <div class="text-center mb-5">
        <h1 class="fw-bold text-primary animate__animated animate__fadeInDown">
            My Wallet
        </h1>
        <p class="text-muted">Track your transactions and check your current balance easily.</p>
    </div>

    {{-- Wallet Balance Card --}}
    <div class="row justify-content-center mb-5">
        <div class="col-md-6">
            <div class="card text-center shadow-sm border-0 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h5 class="card-title text-muted">Current Wallet Balance</h5>
                    <h2 class="fw-bold text-success mt-3">
                        ₦{{ number_format($walletBalance, 2) }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Transaction History --}}
    <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="card-body">
            <h4 class="mb-4 text-primary">Transaction History</h4>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Amount (₦)</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>
                                    <span class="badge 
                                        @if($transaction->type == 'purchase') bg-danger 
                                        @elseif($transaction->type == 'fund') bg-success 
                                        @elseif($transaction->type == 'refund') bg-warning 
                                        @else bg-secondary @endif">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-success">
                                    ₦{{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="text-muted">
                                    {{ $transaction->description ?? '-' }}
                                </td>
                                <td>
                                    {{ $transaction->created_at->format('d M Y, h:i A') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-wallet fa-2x mb-3"></i>
                                    <p>No transactions yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>

{{-- Bootstrap & SweetAlert already included in layout --}}
@endsection
