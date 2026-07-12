@extends('account.layouts.portal')
@section('title', 'CRM Payment Sync')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    @if(account_can_manage())
    <div class="card mb-4">
        <div class="card-header border-bottom"><h5 class="mb-0">Sync Settings</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ account_route('crm-sync.sync-all') }}" class="row g-3 align-items-end"
                  onsubmit="return confirm('Sync all pending CRM payments?')">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Target Ledger Account *</label>
                    <select name="ledger_account_id" class="form-control" required>
                        @foreach($ledgerAccounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-sync me-1"></i> Sync All Pending</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card mt-4">
        <div class="card-header border-bottom"><h5 class="mb-0">Pending CRM Payments</h5></div>
        <div class="card-body">
            @if(account_can_manage() && $pendingPayments->count())
            <form method="POST" action="{{ account_route('crm-sync.sync') }}" id="syncForm">
                @csrf
                <div class="mb-3 row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Target Account</label>
                        <select name="ledger_account_id" class="form-control" required>
                            @foreach($ledgerAccounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success">Sync Selected</button>
                    </div>
                </div>
            @endif
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            @if(account_can_manage())<th><input type="checkbox" id="checkAll"></th>@endif
                            <th>Date</th>
                            <th>Lead</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th>Txn Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingPayments as $payment)
                        <tr>
                            @if(account_can_manage())
                            <td><input type="checkbox" name="lead_payment_ids[]" value="{{ $payment->id }}" form="syncForm" class="payment-check"></td>
                            @endif
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td>{{ $payment->lead?->name ?? '—' }}</td>
                            <td>{{ $payment->payment_type }}</td>
                            <td>{{ $payment->payment_mode }}</td>
                            <td>{{ transaction_types($payment->transaction_type) }}</td>
                            <td class="text-end">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted">All CRM payments are synced.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $pendingPayments->links() }}
            @if(account_can_manage() && $pendingPayments->count())
            </form>
            @endif
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header border-bottom"><h5 class="mb-0">Synced Payments</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Lead</th>
                            <th>Type</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($syncedPayments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td>{{ $payment->lead?->name ?? '—' }}</td>
                            <td>{{ $payment->payment_type }}</td>
                            <td class="text-end">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No synced payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $syncedPayments->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$('#checkAll').on('change', function() {
    $('.payment-check').prop('checked', this.checked);
});
</script>
@endsection
