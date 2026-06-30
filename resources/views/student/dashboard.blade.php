@extends('student.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page portal-dashboard portal-dashboard--student">
    @include('student.partials.alerts')

    <div class="portal-dashboard-header mb-4">
        <h1>Welcome, {{ $student->name }}! 👋</h1>
        <p class="mb-0">Lead ID: {{ $student->lead_ref }} | Course: {{ $student->course?->name ?? 'N/A' }}</p>
    </div>

    <div class="stats-grid portal-stats-grid portal-stats-grid--4 mb-4">
        <div class="portal-stat-card portal-stat-card--profile">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-user"></i></div>
                <div class="card-title">Profile</div>
                <h3>{{ $student->isProfileComplete() ? 'Complete' : 'Incomplete' }}</h3>
            </div>
        </div>
        <div class="portal-stat-card {{ $student->hasPaid() ? 'portal-stat-card--payment-done' : 'portal-stat-card--payment-pending' }}">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-credit-card"></i></div>
                <div class="card-title">Payment</div>
                <h3>{{ $student->hasPaid() ? 'Paid' : 'Pending' }}</h3>
            </div>
        </div>
        <div class="portal-stat-card {{ $student->hasRequiredDocuments() ? 'portal-stat-card--documents-done' : 'portal-stat-card--documents-pending' }}">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-folder"></i></div>
                <div class="card-title">Documents</div>
                <h3>{{ $student->documentsProgressLabel() }}</h3>
            </div>
        </div>
        <div class="portal-stat-card {{ $student->isApplicationSubmitted() ? 'portal-stat-card--application-done' : 'portal-stat-card--application-pending' }}">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-file"></i></div>
                <div class="card-title">Application</div>
                <h3>{{ $student->applicationStatusLabel() }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            @include('student.partials.application-status')
        </div>
        <div class="col-lg-4 d-flex flex-column gap-4">
            <div class="portal-chart-card">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--status"><i class="bx bx-target-lock"></i></span> Application Progress</h5>
                </div>
                <div class="card-body">
                    <div id="studentProgressChart" class="chart-host"></div>
                    <div id="studentStepsChart" class="chart-host mt-2"></div>
                </div>
            </div>

            <div class="portal-chart-card flex-grow-1">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--list"><i class="bx bx-user-voice"></i></span> Counselor</h5>
                </div>
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

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;

    const progressEl = document.querySelector('#studentProgressChart');
    if (!progressEl) return;

    const percent = {{ $student->applicationProgressPercent() }};
    const stepLabels = ['Profile', 'Payment', 'Documents', 'Submit'];
    const stepValues = [
        {{ $student->isProfileComplete() ? 100 : 35 }},
        {{ $student->hasPaid() ? 100 : 35 }},
        {{ $student->hasRequiredDocuments() ? 100 : 35 }},
        {{ $student->isApplicationSubmitted() ? 100 : 35 }},
    ];

    new ApexCharts(progressEl, {
        series: [percent],
        chart: { type: 'radialBar', height: 280, fontFamily: '"Golos Text", sans-serif' },
        plotOptions: {
            radialBar: {
                hollow: { size: '62%' },
                track: { background: '#f1f5f9', strokeWidth: '100%' },
                dataLabels: {
                    name: { fontSize: '14px', color: '#64748b', offsetY: -8 },
                    value: { fontSize: '28px', fontWeight: 700, color: '#334155', formatter: (v) => Math.round(v) + '%' },
                },
            },
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                gradientToColors: ['#f093fb'],
                stops: [0, 100],
            },
        },
        colors: ['#667eea'],
        labels: ['Overall Complete'],
    }).render();

    const stepsEl = document.querySelector('#studentStepsChart');
    if (stepsEl) {
        new ApexCharts(stepsEl, {
        series: [{ name: 'Step progress', data: stepValues }],
        chart: { type: 'bar', height: 180, toolbar: { show: false }, fontFamily: '"Golos Text", sans-serif' },
        plotOptions: { bar: { horizontal: true, barHeight: '55%', distributed: true, borderRadius: 6 } },
        colors: ['#4facfe', '#f7971e', '#a18cd1', '#11998e'],
        dataLabels: { enabled: true, formatter: (v) => (v >= 100 ? 'Done' : 'Pending') },
        xaxis: { categories: stepLabels, max: 100 },
        legend: { show: false },
        grid: { borderColor: '#f1f5f9' },
        }).render();
    }
});
</script>
@endsection
