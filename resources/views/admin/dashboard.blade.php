@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page admin-dashboard">

    <div class="admin-dashboard-header mb-4">
        <h1>Welcome back, {{ auth()->guard('admin')->user()->name }}! 👋</h1>
        <p>Here's your comprehensive overview for {{ session('academic_year_name') }}</p>
    </div>

    <div class="stats-grid admin-stats-grid mb-4">
        <div class="admin-stat-card admin-stat-card--warm">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-sun"></i></div>
                <div class="card-title">Warm Leads</div>
                <h3>{{ number_format($leadsCount['warm']) }}</h3>
                <div class="trend">
                    <i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['warm'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['warm'] }}% from last year
                </div>
                <a href="{{ route('admin.leads.status', ['status' => 'warm']) }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="admin-stat-card admin-stat-card--hot">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-flame"></i></div>
                <div class="card-title">Hot Leads</div>
                <h3>{{ number_format($leadsCount['hot']) }}</h3>
                <div class="trend">
                    <i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['hot'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['hot'] }}% from last year
                </div>
                <a href="{{ route('admin.leads.status', ['status' => 'hot']) }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="admin-stat-card admin-stat-card--application">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-file"></i></div>
                <div class="card-title">Applications</div>
                <h3>{{ number_format($leadsCount['application']) }}</h3>
                <div class="trend">
                    <i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['application'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['application'] }}% from last year
                </div>
                <a href="{{ route('admin.leads.status', ['status' => 'application']) }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="admin-stat-card admin-stat-card--admission">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-graduation"></i></div>
                <div class="card-title">Admissions</div>
                <h3>{{ number_format($leadsCount['admission']) }}</h3>
                <div class="trend">
                    <i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['admission'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['admission'] }}% from last year
                </div>
                <a href="{{ route('admin.leads.status', ['status' => 'admission']) }}" class="stretched-link"></a>
            </div>
        </div>

        <div class="admin-stat-card admin-stat-card--total">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-group"></i></div>
                <div class="card-title">Total Leads</div>
                <h3>{{ number_format($leadsCount['total']) }}</h3>
                <div class="trend">
                    <i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['total'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['total'] }}% from last year
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="admin-chart-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5>
                        <span class="chart-icon chart-icon--leads"><i class="bx bx-line-chart"></i></span>
                        Leads Overview {{ date('Y') }}
                    </h5>
                    <span class="conversion-pill"><i class="bx bx-trending-up"></i> {{ $conversionRate }}% conversion</span>
                </div>
                <div class="card-body">
                    <div id="monthlyLeadsChart" class="chart-host"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="admin-chart-card h-100">
                <div class="card-header">
                    <h5>
                        <span class="chart-icon chart-icon--funnel"><i class="bx bx-filter-alt"></i></span>
                        Lead Funnel {{ session('academic_year_name') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div id="leadFunnelChart" class="chart-host"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="admin-chart-card h-100">
                <div class="card-header">
                    <h5>
                        <span class="chart-icon chart-icon--followups"><i class="bx bx-calendar-check"></i></span>
                        {{ date('Y') }} Follow-ups
                    </h5>
                </div>
                <div class="card-body">
                    <div id="todaysFollowupsChart" class="chart-host"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="admin-chart-card h-100">
                <div class="card-header">
                    <h5>
                        <span class="chart-icon chart-icon--revenue"><i class="bx bx-rupee"></i></span>
                        {{ date('Y') }} Revenue
                    </h5>
                </div>
                <div class="card-body">
                    <div id="receivedPaymentsChart" class="chart-host"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="admin-chart-card admin-list-card h-100">
                <div class="card-header">
                    <h5>
                        <span class="chart-icon chart-icon--list"><i class="bx bx-user-check"></i></span>
                        Recent Admissions
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0 list-unstyled d-flex flex-column gap-3">
                        @forelse ($recentAdmissions as $admission)
                            <li class="list-item d-flex align-items-center gap-3">
                                <div class="avatar flex-shrink-0">
                                    @include('admin.partials.lead-avatar', [
                                        'photo' => $admission->photo,
                                        'name' => $admission->name,
                                    ])
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <h6 class="mb-0 text-truncate">{{ $admission->name }}</h6>
                                    <small class="text-muted">{{ $admission->college->name ?? 'N/A' }}, {{ $admission->course->name ?? 'N/A' }}</small>
                                </div>
                                <small class="text-muted fw-semibold">{{ $admission->created_at->format('d/m/Y') }}</small>
                            </li>
                        @empty
                            <li class="text-muted text-center py-4">No admissions yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="admin-chart-card h-100">
                <div class="card-header">
                    <h5>
                        <span class="chart-icon chart-icon--status"><i class="bx bx-pie-chart-alt-2"></i></span>
                        Leads Status {{ session('academic_year_name') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div id="leadsStatusPieChart" class="chart-host chart-host--tall"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="admin-chart-card admin-list-card h-100">
                <div class="card-header">
                    <h5>
                        <span class="chart-icon chart-icon--transactions"><i class="bx bx-transfer"></i></span>
                        Recent Transactions
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0 list-unstyled d-flex flex-column gap-3">
                        @forelse ($recentTransactions as $transaction)
                            <li class="list-item d-flex align-items-center gap-3">
                                <div class="avatar flex-shrink-0">
                                    @include('admin.partials.lead-avatar', [
                                        'photo' => $transaction->lead->photo ?? null,
                                        'name' => $transaction->lead->name ?? 'User',
                                    ])
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <small class="text-muted d-block">{{ $transaction->payment_mode }}</small>
                                    <h6 class="mb-0 text-truncate">{{ $transaction->lead->name ?? 'N/A' }}</h6>
                                </div>
                                <div class="text-end">
                                    <h6 class="mb-0">₹{{ number_format($transaction->amount, 2) }}</h6>
                                </div>
                            </li>
                        @empty
                            <li class="text-muted text-center py-4">No transactions yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') {
        console.error('ApexCharts failed to load.');
        return;
    }

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const chartToolbar = { show: false };
    const noData = { text: 'No data available', align: 'center', verticalAlign: 'middle', style: { color: '#94a3b8', fontSize: '14px' } };

    const gradientFill = (from, to) => ({
        type: 'gradient',
        gradient: {
            shade: 'light',
            type: 'vertical',
            shadeIntensity: 0.4,
            gradientToColors: [to],
            opacityFrom: 0.95,
            opacityTo: 0.35,
            stops: [0, 100],
        },
        colors: [from],
    });

    const baseChart = { fontFamily: '"Golos Text", sans-serif', toolbar: chartToolbar, animations: { enabled: true, easing: 'easeinout', speed: 700 } };

    // 1. Leads overview — smooth area chart with gradient
    const monthlyLeadsEl = document.querySelector('#monthlyLeadsChart');
    if (monthlyLeadsEl) {
        new ApexCharts(monthlyLeadsEl, {
            series: [{ name: 'New Leads', data: @json(array_values($monthlyLeadsData)) }],
            chart: { ...baseChart, type: 'area', height: 320 },
            colors: ['#667eea'],
            fill: gradientFill('#667eea', '#764ba2'),
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            xaxis: { categories: months, labels: { style: { colors: '#64748b' } } },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            tooltip: { theme: 'light', y: { formatter: (val) => val + ' leads' } },
            markers: { size: 4, strokeWidth: 2, hover: { size: 7 } },
            noData,
        }).render();
    }

    // 2. Lead funnel — horizontal gradient bars
    const funnelEl = document.querySelector('#leadFunnelChart');
    if (funnelEl) {
        const funnelData = @json($funnelData);
        const funnelPercentages = @json($funnelPercentages);
        new ApexCharts(funnelEl, {
            series: [{ data: [funnelData.total, funnelData.positive, funnelData.application, funnelData.admission] }],
            chart: { ...baseChart, type: 'bar', height: 320 },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '65%',
                    distributed: true,
                    borderRadius: 8,
                    dataLabels: { position: 'center' },
                },
            },
            colors: ['#667eea', '#fbbf24', '#38bdf8', '#34d399'],
            dataLabels: {
                enabled: true,
                style: { colors: ['#fff'], fontWeight: 600 },
                formatter: (val, opts) => {
                    const stage = ['total', 'positive', 'application', 'admission'][opts.dataPointIndex];
                    return val + ' (' + (funnelPercentages[stage] ?? 0) + '%)';
                },
            },
            xaxis: {
                categories: ['Total Leads', 'Positive', 'Application', 'Admission'],
                labels: { style: { colors: '#64748b' } },
            },
            yaxis: { labels: { style: { colors: '#64748b', fontWeight: 600 } } },
            grid: { borderColor: '#f1f5f9' },
            legend: { show: false },
            tooltip: { theme: 'light', y: { formatter: (val) => val + ' leads' } },
            noData,
        }).render();
    }

    // 3. Follow-ups — stacked column chart
    const followupsEl = document.querySelector('#todaysFollowupsChart');
    if (followupsEl) {
        new ApexCharts(followupsEl, {
            series: [
                { name: 'Positive', data: @json($followupsData->pluck('positive_count')) },
                { name: 'Negative', data: @json($followupsData->pluck('negative_count')) },
            ],
            chart: { ...baseChart, type: 'bar', height: 320, stacked: true },
            plotOptions: { bar: { columnWidth: '48%', borderRadius: 6, borderRadiusApplication: 'end' } },
            colors: ['#10b981', '#f43f5e'],
            fill: { type: 'gradient', gradient: { shade: 'light', opacityFrom: 0.95, opacityTo: 0.65 } },
            xaxis: { categories: @json($months), labels: { style: { colors: '#64748b' } } },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: { theme: 'light', y: { formatter: (val) => val + ' follow-ups' } },
            noData,
        }).render();
    }

    // 4. Revenue — gradient column chart
    const revenueEl = document.querySelector('#receivedPaymentsChart');
    if (revenueEl) {
        new ApexCharts(revenueEl, {
            series: [{ name: 'Revenue', data: @json(array_values($monthlyRevenueData)) }],
            chart: { ...baseChart, type: 'bar', height: 320 },
            plotOptions: { bar: { columnWidth: '45%', borderRadius: 8, distributed: true } },
            colors: ['#4facfe', '#00f2fe', '#43e97b', '#38f9d7', '#fa709a', '#fee140', '#a18cd1', '#fbc2eb', '#667eea', '#764ba2', '#f093fb', '#f5576c'],
            dataLabels: { enabled: false },
            xaxis: { categories: months, labels: { style: { colors: '#64748b' } } },
            yaxis: {
                labels: {
                    style: { colors: '#64748b' },
                    formatter: (val) => '₹' + Number(val).toLocaleString('en-IN'),
                },
            },
            grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
            legend: { show: false },
            tooltip: { theme: 'light', y: { formatter: (val) => '₹' + Number(val).toLocaleString('en-IN') } },
            noData,
        }).render();
    }

    // 5. Lead status — donut chart with gradient segments
    const statusEl = document.querySelector('#leadsStatusPieChart');
    if (statusEl) {
        const statusLabels = @json(array_keys($leadStatusDistribution));
        const statusValues = @json(array_values($leadStatusDistribution));
        const statusColors = @json($statusColors);

        new ApexCharts(statusEl, {
            series: statusValues.length ? statusValues : [1],
            chart: { ...baseChart, type: 'donut', height: 380 },
            labels: statusLabels.length ? statusLabels : ['No data'],
            colors: statusColors.length ? statusColors : ['#cbd5e1'],
            fill: { type: 'gradient' },
            plotOptions: {
                pie: {
                    donut: {
                        size: '68%',
                        labels: {
                            show: true,
                            name: { show: true, fontSize: '14px' },
                            value: { show: true, fontSize: '22px', fontWeight: 700 },
                            total: {
                                show: true,
                                label: 'Total Leads',
                                formatter: () => @json(number_format($leadsCount['total'])),
                            },
                        },
                    },
                },
            },
            stroke: { width: 2, colors: ['#fff'] },
            legend: { position: 'bottom', fontSize: '13px' },
            dataLabels: { enabled: false },
            tooltip: { theme: 'light', y: { formatter: (val) => val + ' leads' } },
            noData,
        }).render();
    }
});
</script>
@endsection
