@extends('student.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('student.partials.alerts')

    <div class="dashboard-header mb-4">
        <h1 style="color: #fff !important;">Welcome, {{ $student->name }}!</h1>
        <p>Lead ID: {{ $student->lead_ref }} | Application: {{ $student->applicationStatusLabel() }}</p>
    </div>

    <div class="stats-grid mb-4">
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-user"></i></div>
                <div class="card-title">Profile</div>
                <h3>{{ $student->isProfileComplete() ? 'Complete' : 'Incomplete' }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-credit-card"></i></div>
                <div class="card-title">Payment</div>
                <h3>{{ $student->hasPaid() ? 'Paid' : 'Pending' }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-file"></i></div>
                <div class="card-title">Application</div>
                <h3>{{ $student->applicationStatusLabel() }}</h3>
            </div>
        </div>
        <div class="stats-card">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-book"></i></div>
                <div class="card-title">Course</div>
                <h3 style="font-size: 1rem;">{{ $student->course?->name ?? 'N/A' }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @include('student.partials.application-status')
        </div>
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header border-bottom"><h5 class="mb-0">Counselor</h5></div>
                <div class="card-body">
                    @if($student->counselor)
                        <p class="mb-1"><strong>Name:</strong> {{ $student->counselor->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $student->counselor->email }}</p>
                        <p class="mb-0"><strong>Mobile:</strong> {{ $student->counselor->mobile }}</p>
                    @else
                        <p class="text-muted mb-0">No counselor assigned yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
