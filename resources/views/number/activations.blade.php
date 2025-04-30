@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold">Your Purchased Numbers</h2>

    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    @if($activations->count())
        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Code</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <table class="table table-hover">
    <thead>
        <tr>
            <th>Phone Number</th>
            <th>Service Name</th>
            <th>Status</th>
            <th>Code</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($activations as $activation)
        <tr data-activation-id="{{ $activation->id }}">
    <td>{{ $activation->phone_number }}</td>
    <td>{{ $services[$activation->service]['name'] ?? strtoupper($activation->service) }}</td>
    <td>
        <span class="badge {{ $activation->status == 'Received' ? 'bg-success' : 'bg-warning' }}">
            {{ $activation->status }}
        </span>
    </td>
    <td class="code-field" id="code-{{ $activation->id }}">
        @if($activation->code)
            {{ $activation->code }}
        @else
            <em>Waiting...</em>
        @endif
    </td>
    <td>
    @if(!$activation->code)
        <a href="{{ route('number.cancel', $activation->id) }}" class="btn btn-danger btn-sm">
            Cancel
        </a>
    @endif
</td>

</tr>
        @endforeach
    </tbody>
</table>


        </table>
    @else
        <p>No activations yet.</p>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    setInterval(checkForCodes, 10000); // Check every 10 seconds

    function checkForCodes() {
        let activations = document.querySelectorAll('tr[data-activation-id]');

        activations.forEach(function(row) {
            let activationId = row.getAttribute('data-activation-id');

            fetch('/number/check-code/' + activationId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'Received') {
                        // Update the code field
                        document.getElementById('code-' + activationId).innerText = data.code;

                        // Update the status badge
                        let badge = row.querySelector('.badge');
                        badge.classList.remove('bg-warning');
                        badge.classList.add('bg-success');
                        badge.innerText = 'Received';

                        // Remove cancel button if code received
                        let cancelBtn = row.querySelector('.cancel-btn');
                        if (cancelBtn) {
                            cancelBtn.remove();
                        }
                    }
                })
                .catch(error => console.error('Error checking code:', error));
        });
    }
});
</script>

@endsection
