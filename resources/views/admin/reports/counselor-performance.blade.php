@extends('admin.layouts.app')
@section('title', 'Counselor Performance')
@section('style')   
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Counselor Performance</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <!-- Update the filter form -->
                        <form action="{{ route('admin.reports.counselor-performance') }}" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="counselor_id" class="form-label">Counselor</label>
                                    <select class="form-select" id="counselor_id" name="counselor_id">
                                        <option value="">All Counselors</option>
                                        @foreach($counselors as $counselor)
                                            <option value="{{ $counselor->id }}" {{ request('counselor_id') == $counselor->id ? 'selected' : '' }}>
                                                {{ $counselor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100" style="margin-top: 32px;">Search</button>
                                </div>
                            </div>                       
                        </form>
                    </div>
                </div>

                <div class="card-body  border-top">
                    <div class="row mb-4">
                        <!-- Update metrics cards -->
                        <div class="col-md-3 mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #007bff;">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="menu-icon tf-icons bx bx-bar-chart-alt"></i> Total Leads
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ number_format($metrics['total_leads']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #28a745;">
                                <div class="card-body">
                                    <h5 class="card-title text-success">
                                        <i class="menu-icon tf-icons bx bx-user-check"></i> Admissions
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ number_format($metrics['admissions']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card text-center" style="background-color: #f8f9fa; border: 1px solid #ffc107;">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">
                                        <i class="menu-icon tf-icons bx bx-file"></i> Applications
                                    </h5>
                                    <p class="card-text fs-4 fw-bold text-dark">{{ number_format($metrics['applications']) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
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
                    @if($selecetdCounselor != '')
                    <div class="row mb-4">
                        <div class="col-md-12 mt-3">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Unopened</th>
                                    <th>Today's Tasks</th>
                                    <th>Tomorrows's Tasks</th>
                                    <th>Pending Tasks</th>
                                    <th>Bin</th>
                                </tr>
                                <tr>
                                    <td>{{$unseen}}</td>
                                    <td>{{$pendingFL}}</td>
                                    <td>{{$todaysFL}}</td>
                                    <td>{{$tomorrowsFL}}</td>
                                    <td>{{$bin}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
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

                    



                    <div class="table-responsive text-nowrap">
                        <!-- Update DataTable -->
                        <table id="leadsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Lead Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Assigned Counselor</th>
                                    <th>Status</th>
                                    <th>Next FL</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($leads as $index => $lead)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $lead->lead_id }}</td>
                                        <td>{{ $lead->name }}</td>
                                        <td>{{ $lead->personal_email }}</td>
                                        <td>{{ $lead->mobile }}</td>
                                        <td>{{ $lead->counselor->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ App\Helpers\LeadStatus::getColor($lead->status) }}">
                                                {{ $lead->status }}
                                            </span>
                                        </td>
                                        <td>{{ $lead->next_follow_up ? $lead->next_follow_up->format('d M Y h:i A') : '-' }}</td>
                                        <td>{{ $lead->created_at->format('Y-m-d H:i A') }}</td>
                                    </tr>
                                
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>    
    </div>
</div>
@endsection
@section('scripts')   
<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
    $(document).ready(function() {
        $('#leadsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        });
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