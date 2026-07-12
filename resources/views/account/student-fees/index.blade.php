@extends('account.layouts.portal')
@section('title', 'Student Fees')
@section('content')
@php $plans = $plans ?? []; @endphp
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Student Fees</h4>
            <p class="text-muted mb-0">Set registration plan, admission fee, and college fee. Counselors can only view these amounts.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header border-bottom"><h6 class="mb-0">Registration Fee  (Non-Refundable)</h6></div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($plans as $plan)
                <div class="col-md-3">
                    <div class="border rounded p-3 h-100">
                        <div class="fw-semibold">{{ $plan['label'] }}</div>
                        <div class="small text-muted">Base ₹{{ number_format($plan['base'], 2) }} + {{ $plan['gst_percent'] }}% GST</div>
                        <div class="fw-bold mt-1">₹{{ number_format($plan['total'], 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label">Search student</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, email, or lead ID">
                </div>
                <div class="col-auto">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary" type="submit">Search</button>
                    @if(request('q'))
                        <a href="{{ account_route('student-fees.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @foreach($students as $student)
    @php $summary = $feeService->feeSummary($student); @endphp
    <div class="card mt-3">
        <div class="card-header border-bottom d-flex flex-wrap justify-content-between gap-2">
            <div>
                <h5 class="mb-0">{{ $student->name }}</h5>
                <small class="text-muted">
                    Lead {{ $student->lead_ref }} · {{ $student->email }}
                    · Counselor: {{ $student->counselor?->name ?? '—' }}
                </small>
            </div>
            <div class="text-end small">
                <div>Paid ₹{{ number_format($summary['total_paid'], 2) }}</div>
                <div class="fw-semibold {{ $summary['total_remaining'] > 0 ? 'text-warning' : 'text-success' }}">
                    Remaining ₹{{ number_format($summary['total_remaining'], 2) }}
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(account_can_manage())
            <form method="POST" action="{{ account_route('student-fees.update', $student->id) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-4">
                    <label class="form-label">Registration Fee Plan *</label>
                    <select name="registration_fee_plan" class="form-control" required>
                        <option value="">Select plan</option>
                        @foreach($plans as $key => $plan)
                            <option value="{{ $key }}" {{ old('registration_fee_plan', $student->registration_fee_plan) === $key ? 'selected' : '' }}>
                                {{ $plan['label'] }} — ₹{{ number_format($plan['total'], 2) }} (₹{{ number_format($plan['base'], 0) }} + {{ $plan['gst_percent'] }}% GST)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Admission Fee</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" name="counselor_fee" class="form-control"
                               value="{{ old('counselor_fee', $student->counselor_fee ?? 0) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Admission Due Date</label>
                    <input type="date" name="counselor_fee_due_date" class="form-control"
                           value="{{ old('counselor_fee_due_date', optional($student->counselor_fee_due_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">College Fee</label>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" step="0.01" min="0" name="college_fee" class="form-control"
                               value="{{ old('college_fee', $student->college_fee ?? 0) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">College Due Date</label>
                    <input type="date" name="college_fee_due_date" class="form-control"
                           value="{{ old('college_fee_due_date', optional($student->college_fee_due_date)->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Receive In (Bank / Cash) *</label>
                    <select name="fee_ledger_account_id" class="form-control" required>
                        <option value="">Select account</option>
                        @foreach($ledgerAccounts as $ledger)
                            <option value="{{ $ledger->id }}" {{ (string) old('fee_ledger_account_id', $student->fee_ledger_account_id) === (string) $ledger->id ? 'selected' : '' }}>
                                {{ $ledger->name }} · {{ ucfirst($ledger->type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>Save Fees</button>
                </div>
            </form>
            @else
                <div class="row g-3">
                    <div class="col-md-4"><strong>Registration:</strong> {{ $summary['registration_plan']['label'] ?? '—' }} · ₹{{ number_format($summary['registration_fee'], 2) }}</div>
                    <div class="col-md-4"><strong>Admission:</strong> ₹{{ number_format($summary['counselor_fee'], 2) }}</div>
                    <div class="col-md-4"><strong>College:</strong> ₹{{ number_format($summary['college_fee'], 2) }}</div>
                </div>
            @endif
        </div>
    </div>
    @endforeach

    <div class="mt-3">{{ $students->links() }}</div>
</div>
@endsection
