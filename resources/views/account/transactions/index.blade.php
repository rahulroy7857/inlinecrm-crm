@extends('account.layouts.portal')
@section('title', 'Transactions')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select name="ledger_account_id" class="form-control">
                        <option value="">All</option>
                        @foreach($ledgerAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('ledger_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="entry_type" class="form-control">
                        <option value="">All</option>
                        <option value="credit" {{ request('entry_type') === 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ request('entry_type') === 'debit' ? 'selected' : '' }}>Debit</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ account_route('transactions.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @if(account_can_manage())
                    <a href="{{ account_route('lead-payments.index', ['add' => 1]) }}" class="btn btn-success">Add Payment</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom"><h5 class="mb-0">Transaction List</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Party</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Mode</th>
                            <th class="text-end">Amount</th>
                            <th>CRM</th>
                            @if(account_can_manage())<th>Action</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td>{{ $txn->transaction_date->format('d M Y') }}</td>
                            <td>{{ $txn->ledgerAccount->name }}</td>
                            <td>{{ $txn->party_name ?? '—' }}</td>
                            <td class="text-capitalize">{{ $txn->category }}</td>
                            <td><span class="badge bg-label-{{ $txn->entry_type === 'credit' ? 'success' : 'danger' }}">{{ $txn->entry_type }}</span></td>
                            <td>{{ $txn->payment_mode ?? '—' }}</td>
                            <td class="text-end">₹{{ number_format($txn->amount, 2) }}</td>
                            <td>@if($txn->is_crm_synced)<span class="badge bg-info">Synced</span>@else — @endif</td>
                            @if(account_can_manage())
                            <td>
                                @unless($txn->is_crm_synced)
                                <form action="{{ account_route('transactions.destroy', $txn->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger btn-sm" data-confirm-delete="Delete this transaction?">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                                @endunless
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted">No transactions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
