@extends('account.layouts.portal')
@section('title', 'Salary Details — ' . $counselor->name)
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card mb-3">
        <div class="card-body py-3">
            @include('account.counselor-salaries.partials.month-filter', [
                'filterAction' => account_route('counselor-salaries.show', $counselor->id),
                'monthUrl' => fn ($month) => account_route('counselor-salaries.show', ['id' => $counselor->id, 'month' => $month]),
            ])
        </div>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h5 class="mb-1">{{ $counselor->name }} — {{ $monthLabel }}</h5>
            <p class="text-muted mb-0 small">{{ $counselor->email }} · {{ $counselor->mobile }}</p>
        </div>
        <a href="{{ account_route('counselor-salaries.index', ['month' => $selectedMonth]) }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted">Base Salary</small>
                    <h4 class="mb-0">₹{{ number_format($salary['base_salary'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted">Daily Rate (÷ {{ $salary['days_in_month'] }} days)</small>
                    <h4 class="mb-0">₹{{ number_format($salary['daily_rate'], 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <small class="text-muted">Attendance</small>
                    <h4 class="mb-0">{{ $salary['attended_days'] }} / {{ $salary['expected_working_days'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-primary">
                <div class="card-body">
                    <small class="text-muted">Net Salary</small>
                    <h4 class="mb-0 text-primary">₹{{ number_format($salary['net_salary'], 2) }}</h4>
                    @if($salary['deduction'] > 0)
                        <small class="text-danger">Deduction: ₹{{ number_format($salary['deduction'], 2) }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h6 class="mb-0">Daily Attendance</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table table-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>Login</th>
                            <th>Logout</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salary['day_details'] as $day)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($day['date'])->format('d-m-Y') }}</td>
                            <td>{{ $day['day'] }}</td>
                            <td>{{ $day['login_at'] ?? '—' }}</td>
                            <td>{{ $day['logout_at'] ?? '—' }}</td>
                            <td>
                                @if($day['present'])
                                    <span class="badge bg-success">Present</span>
                                @elseif($day['login_at'] || $day['logout_at'])
                                    <span class="badge bg-warning">Incomplete / Late</span>
                                @else
                                    <span class="badge bg-danger">Absent</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No scheduled working days for this month.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
