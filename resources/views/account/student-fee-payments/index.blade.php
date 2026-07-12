@extends('account.layouts.portal')
@section('title', 'Student Fee Payments')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
  @include('partials.student-fee-payments-table', [
    'title' => 'Student Fee Payments',
    'payments' => $payments,
    'purposeLabels' => $purposeLabels,
    'counselors' => $counselors,
  ])
</div>
@endsection
