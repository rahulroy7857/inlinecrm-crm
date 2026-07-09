@extends('account.layouts.portal')
@section('title', 'Cash Flow Report')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Financial Year</label>
                    <select name="academic_year_id" class="form-control">
                        <option value="">All Years</option>
                        @foreach(academic_years() as $year)
                        <option value="{{ $year->id }}" {{ $yearId == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ account_route('reports.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header border-bottom"><h5 class="mb-0">Monthly Cash Flow</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-end">Income (Credit)</th>
                            <th class="text-end">Expense (Debit)</th>
                            <th class="text-end">Net</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthly as $row)
                        <tr>
                            <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->format('M Y') }}</td>
                            <td class="text-end text-success">₹{{ number_format($row->income, 2) }}</td>
                            <td class="text-end text-danger">₹{{ number_format($row->expense, 2) }}</td>
                            <td class="text-end">₹{{ number_format($row->income - $row->expense, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted">No data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
