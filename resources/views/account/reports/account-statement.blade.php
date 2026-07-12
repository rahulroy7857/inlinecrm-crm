@extends('account.layouts.portal')
@section('title', 'Account Statement')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page account-reports-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Account Statement</h4>
            <p class="text-muted mb-0">Opening balance, period entries, and closing balance for one ledger.</p>
        </div>
        <a href="{{ account_route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>Back to Reports
        </a>
    </div>

    <div class="card account-report-toolbar mb-4">
        <div class="card-body">
            <form method="GET" class="account-filter-grid" style="grid-template-columns: 2fr 1fr 1fr auto;">
                <div class="account-filter-grid__field">
                    <label class="form-label">Account</label>
                    <select name="ledger_account_id" class="form-control" required>
                        @foreach($ledgerAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('ledger_account_id', $account?->id) == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="account-filter-grid__field">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="account-filter-grid__field">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="account-filter-grid__actions">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>

    @if($account)
    <div class="account-txn-summary mt-4">
        <div class="account-txn-summary__item">
            <span class="account-txn-summary__label">Account</span>
            <span class="account-txn-summary__value" style="font-size:1.05rem;">{{ $account->name }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--credit">
            <span class="account-txn-summary__label">Opening Balance</span>
            <span class="account-txn-summary__value">₹{{ number_format($openingBalance, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--net">
            <span class="account-txn-summary__label">Closing Balance</span>
            <span class="account-txn-summary__value">₹{{ number_format($closingBalance, 2) }}</span>
        </div>
    </div>

    <div class="card account-report-table-card mt-4">
        <div class="card-header border-bottom">
            <h5 class="mb-0">{{ $account->name }} — Statement</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table crm-table account-cashflow-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Date</th>
                            <th class="text-start">Details</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $running = $openingBalance; @endphp
                        <tr class="table-light">
                            <td colspan="4" class="text-start fw-semibold">Opening Balance</td>
                            <td class="text-end fw-bold">₹{{ number_format($running, 2) }}</td>
                        </tr>
                        @forelse($transactions as $txn)
                        @php
                            $running += $txn->entry_type === 'credit' ? $txn->amount : -$txn->amount;
                        @endphp
                        <tr>
                            <td class="text-start text-nowrap">{{ $txn->transaction_date->format('d M Y') }}</td>
                            <td class="text-start">
                                <div class="fw-semibold">{{ $txn->party_name ?? '—' }}</div>
                                @if($txn->description)
                                    <div class="account-txn-desc">{{ $txn->description }}</div>
                                @endif
                            </td>
                            <td class="text-end text-success">
                                @if($txn->entry_type === 'credit') ₹{{ number_format($txn->amount, 2) }} @else — @endif
                            </td>
                            <td class="text-end text-danger">
                                @if($txn->entry_type === 'debit') ₹{{ number_format($txn->amount, 2) }} @else — @endif
                            </td>
                            <td class="text-end fw-semibold">₹{{ number_format($running, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No transactions in this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
