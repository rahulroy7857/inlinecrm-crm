@extends('counselor.layouts.app')
@section('title', 'Student Fees')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Student Fees</h4>
            <p class="text-muted mb-0">Set the registration plan and registration due date for students assigned to you.</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header border-bottom"><h6 class="mb-0">Registration Fee (Non-Refundable)</h6></div>
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

    <div class="card">
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
                        <a href="{{ route('counselor.student-fees.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @forelse($students as $student)
        <div class="card mt-3">
            <div class="card-header border-bottom d-flex flex-wrap justify-content-between gap-2 student-fee-student-header">
                <div>
                    <h5 class="mb-0">{{ $student->name }}</h5>
                    <small class="text-muted">Lead {{ $student->lead_ref }} · {{ $student->email }}</small>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('counselor.student-fees.update', $student->id) }}" class="row g-3">
                    @csrf
                    @method('PUT')
                    <div class="col-md-4">
                        <label class="form-label">Registration Plan *</label>
                        <select name="registration_fee_plan" class="form-control" required>
                            <option value="">Select plan</option>
                            @foreach($plans as $key => $plan)
                                <option value="{{ $key }}" {{ old('registration_fee_plan', $student->registration_fee_plan) === $key ? 'selected' : '' }}>
                                    {{ $plan['label'] }} — ₹{{ number_format($plan['total'], 2) }}
                                    (₹{{ number_format($plan['base'], 0) }} + {{ $plan['gst_percent'] }}% GST)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Registration Due Date *</label>
                        <input type="date" name="registration_fee_due_date" class="form-control"
                               value="{{ old('registration_fee_due_date', optional($student->registration_fee_due_date)->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>Save Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="card mt-3">
            <div class="card-body text-center text-muted">No registered students found.</div>
        </div>
    @endforelse

    <div class="mt-3">{{ $students->links() }}</div>
</div>
@endsection
