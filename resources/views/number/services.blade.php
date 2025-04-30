@extends('layouts.app')

@section('title', 'USA Number Services')

@section('content')
<div class="container py-5">
    <h2 class="mb-5 fw-bold text-center text-primary">USA Numbers (Available Services)</h2>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    @if (session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
    @endif
    @if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
    @endif


    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4 justify-content-center" id="serviceTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="server1-tab" data-bs-toggle="tab" data-bs-target="#server1"
                type="button" role="tab">SERVER 1</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tellabot-tab" data-bs-toggle="tab" data-bs-target="#tellabot"
                type="button" role="tab">SERVER 2</button>
        </li>

        <li class="nav-item" role="presentation">
    <button class="nav-link" id="beesms-tab" data-bs-toggle="tab" data-bs-target="#beesms"
        type="button" role="tab">SERVER 3</button>
</li>

    </ul>

    {{-- Tab Content --}}
    <div class="tab-content shadow-sm bg-white p-4 rounded border">
        {{-- server1 Tab --}}
        <div class="tab-pane fade show active" id="server1">
            <form action="{{ route('number.purchase') }}" method="POST" class="purchase-form">
                @csrf
                <div class="mb-3">
    <input type="text" class="form-control" placeholder="Search Server 1 Services..." id="searchServer1">
</div>

                <div class="mb-3">
                    <label for="server1_service" class="form-label">Choose Server 1 Service</label>
                    <select class="form-select" name="service" id="server1_service" required>
                        <option value="">-- Select --</option>
                        @foreach($usaServices as $code => $item)
                            <option value="{{ $code }}"
                                    data-cost="{{ $item['naira'] }}"
                                    data-name="{{ $item['name'] }}">
                                {{ $item['name'] }} (₦{{ number_format($item['naira'], 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="cost" id="server1_cost">
                <input type="hidden" name="service_name_daisy" id="server1_name">
                <button type="submit" class="btn btn-primary w-100 submit-button">Buy Number</button>
            </form>
        </div>

        {{-- Tellabot Tab --}}
        <div class="tab-pane fade" id="tellabot">
            <form action="{{ route('server2.purchase') }}" method="POST" class="purchase-form">
                @csrf
                <div class="mb-3">
    <input type="text" class="form-control" placeholder="Search Server 2 Services..." id="searchServer2">
</div>

                <div class="mb-3">
                    <label for="tellabot_service" class="form-label">Choose Server 2 Service</label>
                    <select id="tellabot_service" class="form-select mb-3" required>
                        <option value="">-- Choose Server 2 Service --</option>
                        @foreach ($tellabotServices as $service)
                            <option value="{{ $service['code'] }}"
                                data-name="{{ $service['name'] }}"
                                data-naira="{{ $service['naira'] }}"
                                data-markup="{{ $service['markup'] }}">
                                {{ $service['name'] }} (₦{{ number_format($service['naira'], 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="service" id="tellabot_service_hidden">
                <input type="hidden" name="service_name" id="tellabot_name">
                <input type="hidden" name="markup" id="tellabot_markup">
                <input type="hidden" name="cost" id="tellabot_cost">

                <button type="submit" class="btn btn-primary w-100 submit-button">Buy Number</button>
            </form>
        </div>
    </div>

    {{-- Purchase History --}}
    @if($activations->count())
    <div class="mt-5 card shadow-sm p-4 border rounded">
        <h4 class="mb-4 fw-bold">My Purchases</h4>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Number</th>
                        <th>Service</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th>Code</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($activations as $activation)
                    <tr data-activation-id="{{ $activation->id }}" data-source="{{ $activation->source ?? 'server1' }}">
                        <td class="tellabot-mdn">
                            {{ $activation->phone_number ?? 'Waiting...' }}
                        </td>
                        <td>{{ $usaServices[$activation->service]['name'] ?? strtoupper($activation->service) }}</td>


                        <td>₦{{ number_format($activation->cost, 2) }}</td>
                        <td>
                            <span class="badge status-badge {{ $activation->status == 'Received' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ $activation->status }}
                            </span>
                        </td>
                        <td class="sms-code">
                            @if($activation->code)
                                <span class="text-success fw-bold">{{ $activation->code }}</span>
                            @else
                                <em class="text-muted">--</em>
                            @endif
                        </td>
                        <td>{{ $activation->created_at->format('d M, Y H:i') }}</td>
                        <td>
                            @if($activation->status !== 'Received')
                            <form method="POST" action="{{ route($activation->source === 'tellabot' ? 'tellabot.cancel' : 'number.cancel', $activation->id) }}" class="cancel-form">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger cancel-button">Cancel</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Server 1 auto-fill
    const server1Select = document.getElementById('server1_service');
    if (server1Select) {
        server1Select.addEventListener('change', () => {
            const option = server1Select.selectedOptions[0];
            document.getElementById('server1_cost').value = parseFloat(option.dataset.cost || 0);
            document.getElementById('server1_name').value = option.dataset.name || '';
        });
    }

    // Tellabot auto-fill
    const tellabotSelect = document.getElementById('tellabot_service');
    if (tellabotSelect) {
        tellabotSelect.addEventListener('change', () => {
            const selected = tellabotSelect.selectedOptions[0];
            document.getElementById('tellabot_name').value = selected.dataset.name || '';
            document.getElementById('tellabot_cost').value = parseFloat(selected.dataset.naira || 0);
            document.getElementById('tellabot_markup').value = parseFloat(selected.dataset.markup || 0);
            document.getElementById('tellabot_service_hidden').value = selected.value;
        });
    }

    // Prevent multiple submissions
    document.querySelectorAll('form.purchase-form, form.cancel-form').forEach(form => {
        form.addEventListener('submit', () => {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Processing...';
            }
        });
    });

    // MDN + Code Polling (optimized)
    setInterval(() => {
        let checkCount = 0;
        document.querySelectorAll('tr[data-activation-id]').forEach(row => {
            if (checkCount >= 10) return; // ✅ Limit to 10 checks per interval

            const id = row.dataset.activationId;
            const source = row.dataset.source;
            if (source !== 'tellabot') return;

            checkCount++;

            const phoneCell = row.querySelector('.tellabot-mdn');
            const codeCell = row.querySelector('.sms-code');
            const statusBadge = row.querySelector('.status-badge');
            const cancelForm = row.querySelector('.cancel-form');

            const phoneText = phoneCell?.textContent.trim();
            const codeText = codeCell?.textContent.trim();

            fetch(`/number/check-code/tellabot/${id}`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'updated') {
                        if (data.data.phone_number && (phoneText === '' || phoneText.includes('Waiting'))) {
                            phoneCell.textContent = data.data.phone_number;
                        }

                        if (data.data.code && (codeText === '' || codeText === '--')) {
                            codeCell.innerHTML = `<span class="text-success fw-bold">${data.data.code}</span>`;
                            statusBadge?.classList.remove('bg-warning', 'text-dark');
                            statusBadge?.classList.add('bg-success');
                            statusBadge.textContent = 'Received';
                            cancelForm?.remove();
                        }

                        if (data.api_status && statusBadge) {
                            const badgeClass = data.api_status.toLowerCase() === 'reserved'
                                ? 'bg-info'
                                : (data.api_status.toLowerCase() === 'expired' ? 'bg-danger' : 'bg-warning text-dark');

                            statusBadge.className = 'badge status-badge ' + badgeClass;
                            statusBadge.textContent = data.api_status;
                        }
                    }
                })
                .catch(console.error);
        });
    }, 8000); // ✅ Optimized from 5s to 8s

    // Global sync fallback every 30s
    setInterval(() => {
        fetch('/number/sync-tellabot-orders')
            .then(res => res.json())
            .then(data => console.log('[Tellabot Sync]', data.message || data.status))
            .catch(console.error);
    }, 30000); // ✅ Reduced from 10s to 30s

    // Server 1 search filter
    document.getElementById('searchServer1')?.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const options = document.getElementById('server1_service').options;

        for (let option of options) {
            const text = option.text.toLowerCase();
            option.style.display = text.includes(filter) ? 'block' : 'none';
        }
    });

    // Server 2 search filter
    document.getElementById('searchServer2')?.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const options = document.getElementById('tellabot_service').options;

        for (let option of options) {
            const text = option.text.toLowerCase();
            option.style.display = text.includes(filter) ? 'block' : 'none';
        }
    });
});
</script>

@endsection
