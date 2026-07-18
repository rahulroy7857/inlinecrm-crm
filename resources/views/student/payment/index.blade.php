@extends('student.layouts.app')
@section('title', 'Payments')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  @include('student.partials.alerts')

  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card h-100 border {{ ($summary['registration_required_first'] ?? false) ? 'border-primary' : '' }}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div class="text-muted small">Registration Fee</div>
            @if($summary['registration_required_first'] ?? false)
              <span class="badge bg-primary">Pay first</span>
            @endif
          </div>
          <h4 class="mb-1">₹{{ number_format($summary['registration_fee'], 2) }}</h4>
          <div class="small text-muted">{{ $summary['registration_plan']['label'] ?? 'Plan not set' }}
            @if(!empty($summary['registration_plan']))
              (₹{{ number_format($summary['registration_plan']['base'], 0) }} + {{ $summary['registration_plan']['gst_percent'] }}% GST)
            @endif
          </div>
          <div>Paid: ₹{{ number_format($summary['registration_paid'], 2) }}</div>
          <div class="fw-semibold {{ $summary['registration_remaining'] > 0 ? 'text-warning' : 'text-success' }}">
            Remaining: ₹{{ number_format($summary['registration_remaining'], 2) }}
          </div>
          @if($summary['registration_complete'])
            <div class="mt-2"><span class="badge bg-success">Completed</span></div>
          @endif
          @if($summary['registration_fee_due_date'])
            <small class="text-muted">Due: {{ $summary['registration_fee_due_date']->format('d M Y') }}</small>
          @endif
          <div class="small text-muted mt-1">Non-refundable</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-muted small">Processing Fee</div>
          <h4 class="mb-1">₹{{ number_format($summary['counselor_fee'], 2) }}</h4>
          <div>Paid: ₹{{ number_format($summary['counselor_paid'], 2) }}</div>
          <div class="fw-semibold {{ $summary['counselor_remaining'] > 0 ? 'text-warning' : 'text-success' }}">
            Remaining: ₹{{ number_format($summary['counselor_remaining'], 2) }}
          </div>
          @if($summary['counselor_fee_due_date'])
            <small class="text-muted">Due: {{ $summary['counselor_fee_due_date']->format('d M Y') }}</small>
          @endif
          @if($summary['counselor_complete'])
            <div class="mt-2"><span class="badge bg-success">Completed</span></div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-muted small">College Fee</div>
          <h4 class="mb-1">₹{{ number_format($summary['college_fee'], 2) }}</h4>
          <div>Paid: ₹{{ number_format($summary['college_paid'], 2) }}</div>
          <div class="fw-semibold {{ $summary['college_remaining'] > 0 ? 'text-warning' : 'text-success' }}">
            Remaining: ₹{{ number_format($summary['college_remaining'], 2) }}
          </div>
          @if($summary['college_fee_due_date'])
            <small class="text-muted">Due: {{ $summary['college_fee_due_date']->format('d M Y') }}</small>
          @endif
          @if($summary['college_complete'])
            <div class="mt-2"><span class="badge bg-success">Completed</span></div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-muted small">Total Remaining</div>
          <h4 class="mb-1">₹{{ number_format($summary['total_remaining'], 2) }}</h4>
          <div>Total Fee: ₹{{ number_format($summary['total_fee'], 2) }}</div>
          <div>Total Paid: ₹{{ number_format($summary['total_paid'], 2) }}</div>
          <div class="mt-2 text-muted small">Counselor: {{ $student->counselor?->name ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>

  @if(!$summary['fees_set'])
    <div class="alert alert-warning">Accounts team has not set your fees yet. Please contact your counselor.</div>
  @elseif($summary['total_remaining'] > 0)
    <div class="alert alert-info mb-3">
      Payments are collected by the <strong>Accounts</strong> team. You can track transaction IDs and amounts paid below.
    </div>
  @endif

  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="mb-0">Payment History</h5>
    </div>
    <div class="card-body mt-3">
      <div class="table-responsive">
        <table class="table crm-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Purpose</th>
              <th>Amount</th>
              <th>Txn ID</th>
              <th>Status</th>
              <th>Receipt</th>
            </tr>
          </thead>
          <tbody>
            @forelse($student->payments as $payment)
              <tr>
                <td>{{ optional($payment->paid_at ?? $payment->created_at)->format('d M Y, h:i A') }}</td>
                <td>{{ $purposeLabels[$payment->purpose] ?? $payment->purpose }}</td>
                <td>₹{{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->transaction_id ?? '—' }}</td>
                <td>
                  <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning') }}">
                    {{ ucfirst($payment->status) }}
                  </span>
                </td>
                <td>{{ $payment->receipt_sent_at ? 'Emailed' : '—' }}</td>
              </tr>
            @empty
              <tr><td colspan="6" class="text-center text-muted">No payments yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
