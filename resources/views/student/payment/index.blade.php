@extends('student.layouts.app')
@section('title', 'Payment')
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
          <div class="small text-muted mt-1">Non-refundable</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100">
        <div class="card-body">
          <div class="text-muted small">Admission Fee</div>
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
  @elseif($summary['registration_required_first'])
    <div class="alert alert-info">Please pay the <strong>Registration Fee</strong> first. Other fees unlock after registration is completed.</div>
  @endif

  <div class="card mb-3">
    <div class="card-header border-bottom">
      <h5 class="mb-0">Pay Now</h5>
    </div>
    <div class="card-body mt-3">
      <form method="POST" action="{{ route('student.payment.initiate') }}" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-4">
          <label class="form-label">Fee Type</label>
          <select name="purpose" id="pay_purpose" class="form-control" required>
            @if($summary['registration_remaining'] > 0)
              <option value="registration_fee" data-max="{{ $summary['registration_remaining'] }}" data-fixed="1">
                Registration Fee — {{ $summary['registration_plan']['label'] ?? '' }} (₹{{ number_format($summary['registration_remaining'], 2) }})
              </option>
            @endif
            @if(!$summary['registration_required_first'])
              @if($summary['counselor_remaining'] > 0)
                <option value="counselor_fee" data-max="{{ $summary['counselor_remaining'] }}">Admission Fee (₹{{ number_format($summary['counselor_remaining'], 2) }} left)</option>
              @endif
              @if($summary['college_remaining'] > 0)
                <option value="college_fee" data-max="{{ $summary['college_remaining'] }}">College Fee (₹{{ number_format($summary['college_remaining'], 2) }} left)</option>
              @endif
            @endif
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Amount (₹)</label>
          <input type="number" step="0.01" min="1" name="amount" id="pay_amount" class="form-control" required />
          <small class="text-muted" id="pay_amount_hint"></small>
        </div>
        <div class="col-md-4">
          @if($summary['registration_remaining'] > 0 || ((!$summary['registration_required_first']) && ($summary['counselor_remaining'] > 0 || $summary['college_remaining'] > 0)))
            <button type="submit" class="btn btn-primary w-100">
              <i class="bx bx-credit-card me-1"></i> Pay Now
            </button>
          @else
            <button type="button" class="btn btn-success w-100" disabled>All payments completed</button>
          @endif
        </div>
      </form>
      @if($testMode)
        <p class="text-muted small mb-0 mt-3"><i class="bx bx-info-circle"></i> Test mode is enabled — payment will be simulated. Receipt email will still be sent.</p>
      @endif
    </div>
  </div>

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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const purpose = document.getElementById('pay_purpose');
  const amount = document.getElementById('pay_amount');
  const hint = document.getElementById('pay_amount_hint');
  if (!purpose || !amount) return;

  function syncMax() {
    const option = purpose.options[purpose.selectedIndex];
    if (!option) return;
    const max = option.getAttribute('data-max');
    const fixed = option.getAttribute('data-fixed') === '1';
    amount.max = max;
    amount.value = max;
    amount.readOnly = fixed;
    if (hint) {
      hint.textContent = fixed ? 'Registration fee must be paid in full (non-refundable).' : 'You can pay a partial installment.';
    }
  }

  purpose.addEventListener('change', syncMax);
  syncMax();
});
</script>
@endsection
