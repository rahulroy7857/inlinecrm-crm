@extends('account.layouts.portal')
@section('title', 'Ledger Summary')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page account-reports-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Ledger Summary</h4>
            <p class="text-muted mb-0">Current balances across all bank and cash accounts.</p>
        </div>
        <a href="{{ account_route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>Back to Reports
        </a>
    </div>

    <div class="account-txn-summary mb-4">
        <div class="account-txn-summary__item account-txn-summary__item--net">
            <span class="account-txn-summary__label">Total Balance</span>
            <span class="account-txn-summary__value">₹{{ number_format($totalBalance, 2) }}</span>
        </div>
        <div class="account-txn-summary__item">
            <span class="account-txn-summary__label">Active Accounts</span>
            <span class="account-txn-summary__value">{{ $accounts->where('status', 'Active')->count() }}</span>
        </div>
        <div class="account-txn-summary__item">
            <span class="account-txn-summary__label">Total Accounts</span>
            <span class="account-txn-summary__value">{{ $accounts->count() }}</span>
        </div>
    </div>

    <div class="card account-report-table-card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Ledger Accounts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table crm-table account-cashflow-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Account</th>
                            <th class="text-center">Type</th>
                            <th class="text-end">Transactions</th>
                            <th class="text-end">Opening</th>
                            <th class="text-end">Current Balance</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td class="text-start fw-semibold">{{ $account->name }}</td>
                            <td class="text-center text-capitalize">{{ $account->type }}</td>
                            <td class="text-end">{{ $account->transactions_count }}</td>
                            <td class="text-end">₹{{ number_format($account->opening_balance, 2) }}</td>
                            <td class="text-end fw-bold">₹{{ number_format($account->balance, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $account->status === 'Active' ? 'success' : 'secondary' }}">
                                    {{ $account->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-5">No ledger accounts found.</td></tr>
                        @endforelse
                    </tbody>
                    @if($accounts->count())
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold">₹{{ number_format($totalBalance, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
