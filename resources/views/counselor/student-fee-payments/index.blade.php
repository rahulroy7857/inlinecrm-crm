@extends('counselor.layouts.app')
@section('title', 'Student Fee Payments')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  @include('partials.student-fee-payments-table', [
    'title' => 'My Student Fee Payments',
    'payments' => $payments,
    'purposeLabels' => $purposeLabels,
  ])
</div>
@endsection
