@extends('account.layouts.portal')
@section('title', 'Account Statement')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select name="ledger_account_id" class="form-control" required>
                        @foreach($ledgerAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('ledger_account_id', $account?->id) == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Generate</button>
                    <a href="{{ account_route('reports.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

    @if($account)
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="mb-0">{{ $account->name }} — Statement</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4"><strong>Opening Balance:</strong> ₹{{ number_format($openingBalance, 2) }}</div>
                <div class="col-md-4"><strong>Closing Balance:</strong> ₹{{ number_format($closingBalance, 2) }}</div>
            </div>
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Party</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $running = $openingBalance; @endphp
                        <tr class="table-light">
                            <td colspan="5" class="fw-bold">Opening Balance</td>
                            <td class="fw-bold">₹{{ number_format($running, 2) }}</td>
                        </tr>
                        @foreach($transactions as $txn)
                        @php
                            $running += $txn->entry_type === 'credit' ? $txn->amount : -$txn->amount;
                        @endphp
                        <tr>
                            <td>{{ $txn->transaction_date->format('d M Y') }}</td>
                            <td>{{ $txn->description ?? '—' }}</td>
                            <td>{{ $txn->party_name ?? '—' }}</td>
                            <td>@if($txn->entry_type === 'credit') ₹{{ number_format($txn->amount, 2) }} @else — @endif</td>
                            <td>@if($txn->entry_type === 'debit') ₹{{ number_format($txn->amount, 2) }} @else — @endif</td>
                            <td>₹{{ number_format($running, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
