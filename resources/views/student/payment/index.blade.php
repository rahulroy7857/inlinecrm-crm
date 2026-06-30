@extends('student.layouts.app')
@section('title', 'Payment')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  @include('student.partials.alerts')

  <div class="card">
    <div class="card-header border-bottom">
      <h5 class="mb-0">Application Fee Payment</h5>
    </div>
    <div class="card-body mt-3">
      <div class="row align-items-center">
        <div class="col-md-8">
          <p class="mb-2"><strong>Amount:</strong> ₹{{ number_format($amount, 2) }}</p>
          <p class="mb-2"><strong>Gateway:</strong> {{ ucfirst($gateway) }}</p>
          <p class="mb-2"><strong>Status:</strong>
            @if($student->hasPaid())
              <span class="badge bg-success">Paid</span>
              @if($student->payment_reference)
                <small class="text-muted">(Ref: {{ $student->payment_reference }})</small>
              @endif
            @else
              <span class="badge bg-warning">Pending</span>
            @endif
          </p>
          @if($testMode)
            <p class="text-muted small mb-0"><i class="bx bx-info-circle"></i> Test mode is enabled — payment will be simulated.</p>
          @endif
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
          @if(!$student->hasPaid())
            <form method="POST" action="{{ route('student.payment.initiate') }}">
              @csrf
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="bx bx-credit-card me-1"></i> Pay Now
              </button>
            </form>
          @else
            <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">Go to Dashboard</a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
