@extends('account.layouts.portal')
@section('title', 'Ledger Summary')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1">Total Balance Across All Accounts</h5>
                <h3 class="mb-0 text-primary">₹{{ number_format($totalBalance, 2) }}</h3>
            </div>
            <a href="{{ account_route('reports.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom"><h5 class="mb-0">All Ledger Accounts</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Type</th>
                            <th>Transactions</th>
                            <th>Opening</th>
                            <th class="text-end">Current Balance</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td>{{ $account->name }}</td>
                            <td class="text-capitalize">{{ $account->type }}</td>
                            <td>{{ $account->transactions_count }}</td>
                            <td>₹{{ number_format($account->opening_balance, 2) }}</td>
                            <td class="text-end fw-semibold">₹{{ number_format($account->balance, 2) }}</td>
                            <td>{{ $account->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Total</td>
                            <td class="text-end">₹{{ number_format($totalBalance, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
