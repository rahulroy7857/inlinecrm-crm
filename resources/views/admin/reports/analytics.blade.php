@extends('admin.layouts.app')
@section('title', 'Analytics')
@section('style')   


@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Analytics</h5>
                      </div>
                </div>


                <div class="card-body mt-3 ">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-center">Leads by Source</h6>
                            <canvas id="leadsBySourceChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Admissions by Month</h6>
                            <canvas id="conversionsByMonthChart"></canvas>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6 class="text-center">Admission by College</h6>
                            <canvas id="admissionbyCollege"></canvas>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-center">Leads by month</h6>
                            <canvas id="leadsByMonthChart"></canvas>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="text-center">Sales Report</h6>
                            <div class="table-responsive">
                                <table class="table crm-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Course</th>
                                            <th>Application</th>
                                            <th>Reservation</th>
                                            <th>Admission</th>
                                            <th>Cancellation</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tbody>
                                            @foreach($salesReport as $index => $report)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $report->course }}</td>
                                                    <td>{{ $report->applications }}</td>
                                                    <td>{{ $report->reservations }}</td>
                                                    <td>{{ $report->admissions }}</td>
                                                    <td>{{ $report->cancellations }}</td>
                                                </tr>
                                           
                                            @endforeach
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
    </div>
</div>
@endsection 

@section('scripts')   
<script src="https://cdn.jsdelivr.net/npm/chart.js/dist/Chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Leads by Source Chart
    const leadsBySourceCtx = document.getElementById('leadsBySourceChart').getContext('2d');
    new Chart(leadsBySourceCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(@json($leadsBySource)),
            datasets: [{
                data: Object.values(@json($leadsBySource)),
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                    '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c',
                    '#6c757d', '#343a40', '#adb5bd', '#f8f9fa', '#ff6384',
                    '#36a2eb', '#cc65fe', '#ffce56', '#2ecc40', '#ff851b',
                    '#7fdbff', '#b10dc9', '#85144b', '#3d9970', '#111111'
                ]
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

    // Conversions by Month Chart
    const conversionsByMonthCtx = document.getElementById('conversionsByMonthChart').getContext('2d');
    new Chart(conversionsByMonthCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyConversions->pluck('month')),
            datasets: [{
                label: 'Conversions',
                data: @json($monthlyConversions->pluck('count')),
                backgroundColor: '#007bff'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Admissions by College Chart
    const admissionsByCollegeCtx = document.getElementById('admissionbyCollege').getContext('2d');
    new Chart(admissionsByCollegeCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($admissionsByCollege)),
            datasets: [{
                data: @json(array_values($admissionsByCollege)),
                backgroundColor: [
                    '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8',
                    '#6f42c1', '#fd7e14', '#20c997', '#6610f2', '#e83e8c',
                    '#6c757d', '#343a40', '#adb5bd', '#f8f9fa', '#ff6384',
                    '#36a2eb', '#cc65fe', '#ffce56', '#2ecc40', '#ff851b',
                    '#7fdbff', '#b10dc9', '#85144b', '#3d9970', '#111111'
                ]
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

    // Leads by Month Chart
    const leadsByMonthCtx = document.getElementById('leadsByMonthChart').getContext('2d');
new Chart(leadsByMonthCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Leads',
            data: Object.values(@json($leadsByMonthData)),
            borderColor: '#007bff',
            backgroundColor: '#007bff20',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    drawBorder: false
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        }
    }
});
});
</script>
@endsection