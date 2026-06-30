@extends('account.layouts.portal')
@section('title', 'Daybook')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">View Daybook</button>
                    <a href="{{ account_route('daybook.index', ['date' => now()->format('Y-m-d')]) }}" class="btn btn-outline-secondary">Today</a>
                </div>
            </form>
        </div>
    </div>

    <div class="stats-grid mb-4">
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Date</div>
            <h3 class="fs-5">{{ $date->format('d M Y, l') }}</h3>
        </div></div>
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Total Credit</div>
            <h3>₹{{ number_format($totalCredit, 2) }}</h3>
        </div></div>
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Total Debit</div>
            <h3>₹{{ number_format($totalDebit, 2) }}</h3>
        </div></div>
        <div class="stats-card"><div class="card-body">
            <div class="card-title">Net</div>
            <h3>₹{{ number_format($totalCredit - $totalDebit, 2) }}</h3>
        </div></div>
    </div>

    <div class="card">
        <div class="card-header border-bottom"><h5 class="mb-0">Day Transactions</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Account</th>
                            <th>Party</th>
                            <th>Description</th>
                            <th>Credit</th>
                            <th>Debit</th>
                            <th>Mode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $txn->ledgerAccount->name }}</td>
                            <td>{{ $txn->party_name ?? '—' }}</td>
                            <td>{{ Str::limit($txn->description, 50) }}</td>
                            <td>@if($txn->entry_type === 'credit') ₹{{ number_format($txn->amount, 2) }} @else — @endif</td>
                            <td>@if($txn->entry_type === 'debit') ₹{{ number_format($txn->amount, 2) }} @else — @endif</td>
                            <td>{{ $txn->payment_mode ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted">No transactions on this date.</td></tr>
                        @endforelse
                    </tbody>
                    @if($transactions->count())
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end">Totals</td>
                            <td>₹{{ number_format($totalCredit, 2) }}</td>
                            <td>₹{{ number_format($totalDebit, 2) }}</td>
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
