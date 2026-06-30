@extends('account.layouts.portal')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="dashboard-header mb-4">
        <h1 style="color: #fff !important;">Welcome, {{ account_user_name() }}!</h1>
        <p>Financial overview for {{ session('academic_year_name', 'all years') }}</p>
    </div>

    <div class="stats-grid mb-4">
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-wallet"></i></div>
                <div class="card-title">Total Balance</div>
                <h3>₹{{ number_format($totalBalance, 2) }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-trending-up"></i></div>
                <div class="card-title">Total Income</div>
                <h3>₹{{ number_format($totalIncome, 2) }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-trending-down"></i></div>
                <div class="card-title">Total Expense</div>
                <h3>₹{{ number_format($totalExpense, 2) }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-book-open"></i></div>
                <div class="card-title">Today's Entries</div>
                <h3>{{ $todayTransactions }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header border-bottom"><h5 class="mb-0">Account Balances</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th class="text-end">Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ledgerAccounts as $account)
                                <tr>
                                    <td>{{ $account->name }}</td>
                                    <td><span class="badge bg-label-{{ $account->type === 'cash' ? 'warning' : 'info' }} text-capitalize">{{ $account->type }}</span></td>
                                    <td class="text-end">₹{{ number_format($account->current_balance, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">No accounts yet. <a href="{{ account_route('ledger-accounts.index') }}">Add one</a></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Transactions</h5>
                    @if(account_can_manage())
                    <a href="{{ account_route('transactions.create') }}" class="btn btn-sm btn-primary">New Entry</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $txn)
                                <tr>
                                    <td>{{ $txn->transaction_date->format('d M Y') }}</td>
                                    <td>{{ $txn->ledgerAccount->name }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $txn->entry_type === 'credit' ? 'success' : 'danger' }} text-capitalize">{{ $txn->entry_type }}</span>
                                    </td>
                                    <td class="text-end">₹{{ number_format($txn->amount, 2) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted">No transactions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
