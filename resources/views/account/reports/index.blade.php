@extends('account.layouts.portal')
@section('title', 'Reports')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page account-reports-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Reports</h4>
            <p class="text-muted mb-0">Statements, monthly cash flow, and ledger balances.</p>
        </div>
    </div>

    <div class="account-txn-summary mb-4">
        <div class="account-txn-summary__item account-txn-summary__item--net">
            <span class="account-txn-summary__label">Total Bank Balance</span>
            <span class="account-txn-summary__value">₹{{ number_format($stats['total_balance'] ?? 0, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--credit">
            <span class="account-txn-summary__label">Income (This Year)</span>
            <span class="account-txn-summary__value">+₹{{ number_format($stats['income'] ?? 0, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--debit">
            <span class="account-txn-summary__label">Expense (This Year)</span>
            <span class="account-txn-summary__value">-₹{{ number_format($stats['expense'] ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ account_route('reports.account-statement') }}" class="account-report-card">
                <div class="account-report-card__icon account-report-card__icon--statement">
                    <i class="bx bx-file"></i>
                </div>
                <div class="account-report-card__body">
                    <h5 class="account-report-card__title">Account Statement</h5>
                    <p class="account-report-card__text">Ledger-wise statement with opening and closing balance.</p>
                    <span class="account-report-card__link">Open report <i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ account_route('reports.cash-flow') }}" class="account-report-card">
                <div class="account-report-card__icon account-report-card__icon--cashflow">
                    <i class="bx bx-line-chart"></i>
                </div>
                <div class="account-report-card__body">
                    <h5 class="account-report-card__title">Cash Flow</h5>
                    <p class="account-report-card__text">Monthly income vs expense for the financial year.</p>
                    <span class="account-report-card__link">Open report <i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ account_route('reports.ledger-summary') }}" class="account-report-card">
                <div class="account-report-card__icon account-report-card__icon--ledger">
                    <i class="bx bx-wallet"></i>
                </div>
                <div class="account-report-card__body">
                    <h5 class="account-report-card__title">Ledger Summary</h5>
                    <p class="account-report-card__text">All bank and cash account balances at a glance.</p>
                    <span class="account-report-card__link">Open report <i class="bx bx-right-arrow-alt"></i></span>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
