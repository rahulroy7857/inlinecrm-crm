@extends('account.layouts.portal')
@section('title', 'Cash Flow Report')
@section('content')
@php
    $totalIncome = collect($monthly)->sum('income');
    $totalExpense = collect($monthly)->sum('expense');
    $totalNet = $totalIncome - $totalExpense;
@endphp
<div class="container-xxl flex-grow-1 container-p-y crm-page account-reports-page">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Monthly Cash Flow</h4>
            <p class="text-muted mb-0">Income vs expense by month for the selected financial year.</p>
        </div>
        <a href="{{ account_route('reports.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i>Back to Reports
        </a>
    </div>

    <div class="card account-report-toolbar mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label">Financial Year</label>
                    <select name="academic_year_id" class="form-control">
                        <option value="">All Years</option>
                        @foreach(academic_years() as $year)
                        <option value="{{ $year->id }}" {{ (string) $yearId === (string) $year->id ? 'selected' : '' }}>
                            {{ $year->display_name ?? $year->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <div class="account-txn-summary mt-4">
        <div class="account-txn-summary__item account-txn-summary__item--credit">
            <span class="account-txn-summary__label">Total Income</span>
            <span class="account-txn-summary__value">+₹{{ number_format($totalIncome, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--debit">
            <span class="account-txn-summary__label">Total Expense</span>
            <span class="account-txn-summary__value">-₹{{ number_format($totalExpense, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--net">
            <span class="account-txn-summary__label">Net Cash Flow</span>
            <span class="account-txn-summary__value">₹{{ number_format($totalNet, 2) }}</span>
        </div>
    </div>

    <div class="card account-report-table-card mt-4">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Month-wise Breakdown</h5>
            <span class="text-muted small">{{ count($monthly) }} month(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table crm-table account-cashflow-table mb-0">
                    <thead>
                        <tr>
                            <th class="text-start">Month</th>
                            <th class="text-end">Income</th>
                            <th class="text-end">Expense</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthly as $row)
                        @php $net = (float) $row->income - (float) $row->expense; @endphp
                        <tr>
                            <td class="text-start fw-semibold">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</td>
                            <td class="text-end text-success fw-semibold">₹{{ number_format($row->income, 2) }}</td>
                            <td class="text-end text-danger fw-semibold">₹{{ number_format($row->expense, 2) }}</td>
                            <td class="text-end fw-bold {{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                                ₹{{ number_format($net, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">No cash flow data for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(count($monthly))
                    <tfoot>
                        <tr>
                            <td class="text-start fw-bold">Total</td>
                            <td class="text-end fw-bold text-success">₹{{ number_format($totalIncome, 2) }}</td>
                            <td class="text-end fw-bold text-danger">₹{{ number_format($totalExpense, 2) }}</td>
                            <td class="text-end fw-bold {{ $totalNet >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format($totalNet, 2) }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
