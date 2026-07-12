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
        <div class="d-flex align-items-center gap-2">
            @if($salary['is_paid'])
                <span class="badge bg-success">Paid</span>
                <span class="text-muted small">on {{ $salary['paid_at']->format('d M Y, h:i A') }}</span>
            @else
                <span class="badge bg-warning">Unpaid</span>
            @endif
            <a href="{{ account_route('counselor-salaries.index', ['month' => $selectedMonth]) }}" class="btn btn-sm btn-outline-secondary">Back to List</a>
        </div>
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

    @if($salary['is_paid'] && $salary['payment'])
    <div class="card mb-4">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h6 class="mb-0">Payment Details</h6>
                <small class="text-muted">Salary expense recorded for {{ $monthLabel }}</small>
            </div>
            <span class="badge bg-success">Paid</span>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom">
                <div>
                    <div class="text-muted small">Paid Amount</div>
                    <div class="fs-4 fw-semibold text-primary mb-0">₹{{ number_format($salary['paid_amount'], 2) }}</div>
                </div>
                <div class="text-md-end">
                    <div class="text-muted small">Paid Date</div>
                    <div class="fw-semibold">{{ $salary['paid_at']->format('d M Y') }}</div>
                    <div class="text-muted small">{{ $salary['paid_at']->format('h:i A') }}</div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="text-muted small">Paid From</div>
                    <div class="fw-semibold">{{ $salary['payment']->ledgerAccount?->name ?? '—' }}</div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="text-muted small">Payment Mode</div>
                    <div class="fw-semibold">{{ $salary['payment']->payment_mode ?: '—' }}</div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="text-muted small">Reference No</div>
                    <div class="fw-semibold">{{ $salary['payment']->reference_no ?: '—' }}</div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="text-muted small">Notes</div>
                    <div class="fw-semibold">{{ $salary['payment']->notes ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>
    @elseif(account_can_manage() && $salary['net_salary'] > 0)
    <div class="card mb-4">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <h6 class="mb-0">Pay Salary</h6>
                <small class="text-muted">Record payment and post expense to ledger</small>
            </div>
            <span class="badge bg-warning">Unpaid</span>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4 p-3 rounded border" style="background: rgba(105, 108, 255, 0.06);">
                <div>
                    <div class="text-muted small">Amount to pay</div>
                    <div class="fs-4 fw-semibold text-primary mb-0">₹{{ number_format($salary['net_salary'], 2) }}</div>
                </div>
                @if($salary['deduction'] > 0)
                    <div class="text-md-end">
                        <div class="text-muted small">After deduction</div>
                        <div class="text-danger small">− ₹{{ number_format($salary['deduction'], 2) }} from base</div>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ account_route('counselor-salaries.pay', $counselor->id) }}">
                @csrf
                <input type="hidden" name="month" value="{{ $selectedMonth }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Pay From (Bank / Cash) *</label>
                        <select name="ledger_account_id" class="form-control" required>
                            <option value="">Select account</option>
                            @foreach($ledgerAccounts as $ledger)
                                <option value="{{ $ledger->id }}" {{ (string) old('ledger_account_id') === (string) $ledger->id ? 'selected' : '' }}>
                                    {{ $ledger->name }} · {{ ucfirst($ledger->type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Paid Date *</label>
                        <input type="date" name="paid_at" class="form-control" value="{{ old('paid_at', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control">
                            <option value="">Select mode</option>
                            @foreach(['Cash', 'UPI', 'NEFT', 'Bank Transfer', 'Cheque', 'Other'] as $mode)
                                <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reference No</label>
                        <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no') }}" placeholder="Txn / cheque no.">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Notes</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Optional remark">
                    </div>
                    <div class="col-12 pt-2 d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-primary"
                                onclick="return confirm('Mark salary as paid and record expense of ₹{{ number_format($salary['net_salary'], 2) }}?')">
                            <i class="bx bx-check-circle me-1"></i>Mark as Paid
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card mt-4">
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
