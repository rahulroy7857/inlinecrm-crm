@extends('counselor.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page portal-dashboard portal-dashboard--counselor">

    <div class="portal-dashboard-header mb-4">
        <h1>Welcome back, {{ auth()->guard('counselor')->user()->name }}! 👋</h1>
        <p>Here's your lead performance overview for {{ session('academic_year_name') }}</p>
    </div>

    <div class="stats-grid portal-stats-grid mb-4">
        <div class="portal-stat-card portal-stat-card--warm">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-sun"></i></div>
                <div class="card-title">Warm Leads</div>
                <h3>{{ number_format($leadsCount['warm']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['warm'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['warm'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'warm']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--hot">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-flame"></i></div>
                <div class="card-title">Hot Leads</div>
                <h3>{{ number_format($leadsCount['hot']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['hot'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['hot'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'hot']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--application">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-file"></i></div>
                <div class="card-title">Applications</div>
                <h3>{{ number_format($leadsCount['application']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['application'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['application'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'application']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--admission">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-graduation"></i></div>
                <div class="card-title">Admissions</div>
                <h3>{{ number_format($leadsCount['admission']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['admission'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['admission'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'admission']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--total">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-group"></i></div>
                <div class="card-title">Total Leads</div>
                <h3>{{ number_format($leadsCount['total']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['total'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['total'] }}% from last year</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="portal-chart-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5><span class="chart-icon chart-icon--leads"><i class="bx bx-line-chart"></i></span> Monthly Leads {{ date('Y') }}</h5>
                    <span class="conversion-pill"><i class="bx bx-trending-up"></i> {{ $conversionRate }}% conversion</span>
                </div>
                <div class="card-body"><div id="monthlyLeadsChart" class="chart-host"></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--status"><i class="bx bx-pie-chart-alt-2"></i></span> Lead Status</h5>
                </div>
                <div class="card-body"><div id="leadsStatusChart" class="chart-host chart-host--tall"></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--funnel"><i class="bx bx-filter-alt"></i></span> Lead Conversion Funnel</h5>
                </div>
                <div class="card-body"><div id="leadFunnelChart" class="chart-host"></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--followups"><i class="bx bx-calendar-check"></i></span> {{ date('Y') }} Follow-up Performance</h5>
                </div>
                <div class="card-body"><div id="todaysFollowupsChart" class="chart-host"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const baseChart = { fontFamily: '"Golos Text", sans-serif', toolbar: { show: false }, animations: { enabled: true, speed: 700 } };
    const noData = { text: 'No data available', align: 'center', verticalAlign: 'middle', style: { color: '#94a3b8' } };
    const gradientFill = (from, to) => ({
        type: 'gradient',
        gradient: { shade: 'light', type: 'vertical', gradientToColors: [to], opacityFrom: 0.9, opacityTo: 0.25, stops: [0, 100] },
        colors: [from],
    });

    const monthlyEl = document.querySelector('#monthlyLeadsChart');
    if (monthlyEl) {
        new ApexCharts(monthlyEl, {
            series: [{ name: 'Leads', data: @json(array_values($monthlyLeadsData)) }],
            chart: { ...baseChart, type: 'area', height: 320 },
            colors: ['#4776e6'],
            fill: gradientFill('#4776e6', '#8e54e9'),
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: months },
            tooltip: { y: { formatter: (v) => v + ' leads' } },
            noData,
        }).render();
    }

    const statusEl = document.querySelector('#leadsStatusChart');
    if (statusEl) {
        const labels = @json(array_keys($leadStatusDistribution));
        const values = @json(array_values($leadStatusDistribution));
        new ApexCharts(statusEl, {
            series: values.length ? values : [1],
            chart: { ...baseChart, type: 'donut', height: 380 },
            labels: labels.length ? labels : ['No data'],
            colors: @json($statusColors).length ? @json($statusColors) : ['#cbd5e1'],
            fill: { type: 'gradient' },
            plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => @json(number_format($leadsCount['total'])) } } } } },
            legend: { position: 'bottom' },
            dataLabels: { enabled: false },
            noData,
        }).render();
    }

    const funnelEl = document.querySelector('#leadFunnelChart');
    if (funnelEl) {
        const funnelData = @json($funnelData);
        const funnelPercentages = @json($funnelPercentages);
        new ApexCharts(funnelEl, {
            series: [{ name: 'Leads', data: [funnelData.total, funnelData.positive, funnelData.application, funnelData.admission] }],
            chart: { ...baseChart, type: 'radar', height: 320 },
            colors: ['#8e54e9'],
            fill: { opacity: 0.2 },
            stroke: { width: 2 },
            markers: { size: 4 },
            xaxis: { categories: ['Total', 'Positive', 'Application', 'Admission'] },
            yaxis: { show: false },
            tooltip: { y: { formatter: (v, opts) => {
                const keys = ['total', 'positive', 'application', 'admission'];
                return v + ' (' + (funnelPercentages[keys[opts.dataPointIndex]] ?? 0) + '%)';
            } } },
            noData,
        }).render();
    }

    const followupsEl = document.querySelector('#todaysFollowupsChart');
    if (followupsEl) {
        new ApexCharts(followupsEl, {
            series: [
                { name: 'Positive', data: @json($followupsData->pluck('positive_count')) },
                { name: 'Negative', data: @json($followupsData->pluck('negative_count')) },
            ],
            chart: { ...baseChart, type: 'line', height: 320 },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#10b981', '#f43f5e'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            xaxis: { categories: @json($months) },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: { y: { formatter: (v) => v + ' follow-ups' } },
            noData,
        }).render();
    }
});
</script>
@endsection
