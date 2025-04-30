@extends('layouts.app')

@section('content')
<div class="container py-5">

    <h2 class="fw-bold text-primary mb-4">Purchase Virtual Number</h2>

    <div class="card p-4 shadow-sm mb-5">
        <form id="purchaseForm">
            @csrf
            <div class="mb-3">
                <label for="service" class="form-label">Service</label>
                <input type="text" class="form-control" id="service" name="service" required placeholder="Enter service e.g. go">
            </div>

            <div class="mb-3">
                <label for="max_price" class="form-label">Max Price</label>
                <input type="number" class="form-control" id="max_price" name="max_price" value="5.5" step="0.01">
            </div>

            <button type="button" class="btn btn-primary w-100 fw-bold" onclick="purchaseNumber()">Purchase Number</button>
        </form>
    </div>

    <div id="activationArea" class="card p-4 shadow-sm d-none">
        <h4 class="text-primary fw-bold mb-3">Activation Info</h4>

        <p><strong>Phone Number:</strong> <span id="phoneNumber"></span></p>
        <p><strong>Activation ID:</strong> <span id="activationId"></span></p>
        <p><strong>Status:</strong> <span id="activationStatus" class="badge bg-warning text-dark">Waiting for SMS</span></p>

        <div class="mt-4">
            <button class="btn btn-success w-100 fw-bold mb-2" onclick="checkStatus()">Check for SMS Code</button>
            <button class="btn btn-danger w-100 fw-bold" onclick="cancelActivation()">Cancel Activation</button>
        </div>

        <div id="smsCodeArea" class="alert alert-success mt-3 d-none">
            <h5 class="text-center mb-0" id="smsCode">SMS Code: --</h5>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    let currentActivationId = null;

    function purchaseNumber() {
        const service = document.getElementById('service').value;
        const maxPrice = document.getElementById('max_price').value;

        fetch("{{ route('number.purchase') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ service, max_price: maxPrice })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('activationArea').classList.remove('d-none');
                document.getElementById('phoneNumber').innerText = data.activation.phone_number;
                document.getElementById('activationId').innerText = data.activation.activation_id;
                currentActivationId = data.activation.activation_id;
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function checkStatus() {
        if (!currentActivationId) return alert('No Activation ID');

        fetch(`{{ route('number.status') }}?activation_id=${currentActivationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.response.includes('STATUS_OK')) {
                    document.getElementById('activationStatus').innerText = 'Code Received!';
                    document.getElementById('smsCodeArea').classList.remove('d-none');
                    document.getElementById('smsCode').innerText = data.response;
                } else {
                    alert('Still waiting: ' + data.response);
                }
            });
    }

    function cancelActivation() {
        if (!currentActivationId) return alert('No Activation ID');

        fetch("{{ route('number.cancel') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ activation_id: currentActivationId, status: 6 })
        })
        .then(response => response.json())
        .then(data => {
            alert('Cancelled Activation');
            location.reload();
        });
    }
</script>
@endsection
