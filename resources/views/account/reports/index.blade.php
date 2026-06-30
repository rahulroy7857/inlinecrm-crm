@extends('account.layouts.portal')
@section('title', 'Reports')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-file bx-lg text-primary mb-3"></i>
                    <h5>Account Statement</h5>
                    <p class="text-muted">View ledger-wise statement with opening and closing balance.</p>
                    <a href="{{ account_route('reports.account-statement') }}" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-line-chart bx-lg text-success mb-3"></i>
                    <h5>Cash Flow</h5>
                    <p class="text-muted">Monthly income vs expense for the financial year.</p>
                    <a href="{{ account_route('reports.cash-flow') }}" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="bx bx-wallet bx-lg text-warning mb-3"></i>
                    <h5>Ledger Summary</h5>
                    <p class="text-muted">All bank and cash account balances at a glance.</p>
                    <a href="{{ account_route('reports.ledger-summary') }}" class="btn btn-primary">Open</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
