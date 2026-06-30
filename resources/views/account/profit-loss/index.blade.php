@extends('account.layouts.portal')
@section('title', 'Profit & Loss')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Financial Year</label>
                    <select name="academic_year_id" class="form-control">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                        <option value="{{ $year->id }}" {{ $yearId == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>

    <div class="dashboard-header mb-4">
        <h1 style="color: #fff !important;">Profit & Loss Statement</h1>
        <p>{{ $selectedYear?->name ?? 'All Financial Years' }}</p>
    </div>

    <div class="stats-grid mb-4">
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Total Income</div>
            <h3 class="text-success">₹{{ number_format($totalIncome, 2) }}</h3>
        </div></div>
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Total Expense</div>
            <h3 class="text-danger">₹{{ number_format($totalExpense, 2) }}</h3>
        </div></div>
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Net {{ $netProfit >= 0 ? 'Profit' : 'Loss' }}</div>
            <h3 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">₹{{ number_format(abs($netProfit), 2) }}</h3>
        </div></div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header border-bottom"><h5 class="mb-0">Income by Payment Mode</h5></div>
                <div class="card-body">
                    <table class="table crm-table">
                        <thead><tr><th>Mode</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @forelse($incomeBreakdown as $row)
                            <tr>
                                <td>{{ $row->payment_mode ?? 'Unspecified' }}</td>
                                <td class="text-end">₹{{ number_format($row->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-muted text-center">No income data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header border-bottom"><h5 class="mb-0">Expense by Payment Mode</h5></div>
                <div class="card-body">
                    <table class="table crm-table">
                        <thead><tr><th>Mode</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @forelse($expenseBreakdown as $row)
                            <tr>
                                <td>{{ $row->payment_mode ?? 'Unspecified' }}</td>
                                <td class="text-end">₹{{ number_format($row->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-muted text-center">No expense data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
