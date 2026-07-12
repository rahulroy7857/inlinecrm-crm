@extends('counselor.layouts.app')
@section('title', 'Counselor Performance')
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Performance</h5>
                      </div>
                </div>
                

                <div class="card-body">
                    <div class="row mb-4">
                        <!-- Update metrics cards -->
                        <div class="col-md-3  mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #007bff;">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="menu-icon tf-icons bx bx-bar-chart-alt"></i> Total Leads
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ number_format($metrics['total_leads']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3  mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #28a745;">
                                <div class="card-body">
                                    <h5 class="card-title text-success">
                                        <i class="menu-icon tf-icons bx bx-user-check"></i> Admissions
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ number_format($metrics['admissions']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3  mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #ffc107;">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">
                                        <i class="menu-icon tf-icons bx bx-file"></i> Applications
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ number_format($metrics['applications']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3  mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #dc3545;">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">
                                        <i class="menu-icon tf-icons bx bx-pie-chart-alt"></i> Conversion Rate
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ $metrics['conversion_rate'] }}%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <!-- Source Wise Leads Count -->
                        <div class="col-md-6">
                            <div class="card">
                               
                                <div class="card-body">
                                    <canvas id="sourceWiseLeadsChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Lead Funnel -->
                        <div class="col-md-6">
                            <div class="card">
                                
                                <div class="card-body">
                                    <canvas id="leadFunnelChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    



                </div>
            </div>    
    </div>
</div>
@endsection
@section('scripts')   
@include('admin.partials.datatables-scripts')

<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/Chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    // Source Wise Leads Chart
    const sourceWiseLeadsCtx = document.getElementById('sourceWiseLeadsChart').getContext('2d');
    const sourceWiseData = @json($sourceWiseLeads);
    
    new Chart(sourceWiseLeadsCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(sourceWiseData),
            datasets: [{
                label: 'Leads',
                data: Object.values(sourceWiseData),
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1',
                    '#17a2b8', '#fd7e14', '#20c997', '#6610f2', '#e83e8c',
                    '#343a40', '#adb5bd', '#f8d7da', '#b8daff', '#d4edda',
                    '#fff3cd', '#f5c6cb', '#c3e6cb', '#ffeeba', '#bee5eb'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Lead Funnel Chart
    const leadFunnelCtx = document.getElementById('leadFunnelChart').getContext('2d');
    new Chart(leadFunnelCtx, {
        type: 'doughnut',
        data: {
            labels: ['Total Leads', 'Applications', 'Reservations', 'Admissions', 'Cancellations'],
            datasets: [{
                data: [
                    @json($funnelData['total']),
                    @json($funnelData['applications']),
                    @json($funnelData['reservations']),
                    @json($funnelData['admissions']),
                    @json($funnelData['cancellations'])
                ],
                backgroundColor: ['#007bff', '#0d6efd', '#6610f2', '#198754', '#f8d7da'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection