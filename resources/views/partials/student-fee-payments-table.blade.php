@php
  $tableId = $tableId ?? 'studentFeePaymentsTable';
@endphp
<div class="card">
  <div class="card-header border-bottom d-flex flex-wrap gap-2 justify-content-between align-items-center">
    <h5 class="mb-0">{{ $title ?? 'Student Fee Payments' }}</h5>
  </div>
  <div class="card-body mt-3">
    <form method="GET" class="row g-2 mb-3">
      <div class="col-md-3">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search student / lead / email" />
      </div>
      <div class="col-md-3">
        <select name="purpose" class="form-control">
          <option value="">All purposes</option>
          @foreach($purposeLabels as $key => $label)
            <option value="{{ $key }}" {{ request('purpose') === $key ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      @if(!empty($counselors))
      <div class="col-md-3">
        <select name="counselor_id" class="form-control">
          <option value="">All counselors</option>
          @foreach($counselors as $counselor)
            <option value="{{ $counselor->id }}" {{ (string) request('counselor_id') === (string) $counselor->id ? 'selected' : '' }}>
              {{ $counselor->name }}
            </option>
          @endforeach
        </select>
      </div>
      @endif
      <div class="col-md-3">
        <button class="btn btn-primary" type="submit">Filter</button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table crm-table" id="{{ $tableId }}">
        <thead>
          <tr>
            <th>Date</th>
            <th>Student</th>
            <th>Lead ID</th>
            <th>Purpose</th>
            <th>Amount</th>
            <th>Counselor</th>
            <th>Txn ID</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $payment)
            <tr>
              <td>{{ optional($payment->paid_at)->format('d M Y, h:i A') }}</td>
              <td>
                <div>{{ $payment->student?->name }}</div>
                <small class="text-muted">{{ $payment->student?->email }}</small>
              </td>
              <td>{{ $payment->student?->lead_ref }}</td>
              <td>{{ $purposeLabels[$payment->purpose] ?? $payment->purpose }}</td>
              <td>₹{{ number_format($payment->amount, 2) }}</td>
              <td>{{ $payment->counselor?->name ?? '—' }}</td>
              <td>{{ $payment->transaction_id }}</td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted">No student fee payments found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $payments->links() }}</div>
  </div>
</div>
