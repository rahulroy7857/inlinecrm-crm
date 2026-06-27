@extends('admin.layouts.app')
@section('title', 'Agent Commission')
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
                        <h5 class="">Agent Commission Report</h5>
                      </div>
                </div>
                <!-- <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('admin.reports.agent-commission') }}" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="fromDate" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="toDate" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100" style="margin-top: 32px;">Search</button>
                                </div>
                            </div>                       
                        </form>
                    </div>
                </div> -->

                <!-- Summary Cards -->
                <div class="card-body ">
                    <div class="row mb-4">
                        <div class="col-md-4 mt-3">
                            <div class="card text-white" style="background-color: #f8f9fa; border: 1px solid #007bff;">
                                <div class="card-body text-white">
                                    <h5 class="text-primary">Total Leads</h5>
                                    <h3 class="text-primary">{{ number_format($summary['total_leads']) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                            <div class="card text-white" style="background-color: #f8f9fa; border: 1px solid #28a745;">
                                <div class="card-body text-white">
                                    <h5 class="text-success">Total Admissions</h5>
                                    <h3 class="text-success">{{ number_format($summary['total_admissions']) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mt-3">
                            <div class="card bg-white" style="background-color: #f8f9fa; border: 1px solid #ffc107;">
                                <div class="card-body">
                                    <h5 class="text-warning">Total Commission Paid</h5>
                                    <h3 class="text-warning">₹{{ number_format($summary['total_commission_paid']) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agent wise details -->
                    @foreach($agentCommissions as $commission)
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $commission['agent_name'] }}</h6>
                                <div>
                                    <span class="badge bg-primary me-2">Leads: {{ $commission['total_leads'] }}</span>
                                    <span class="badge bg-success me-2">Admissions: {{ $commission['total_admissions'] }}</span>
                                    <span class="badge bg-warning">Commission: ₹{{ number_format($commission['total_commission']) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Lead ID</th>
                                            <th>Name</th>
                                            <th>College</th>
                                            <th>Status</th>
                                            <th>Agent Commission</th>
                                            <th>Commission Paid</th>
                                            <th>Balance</th>
                                            <th>Admission Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalAgentCommission = 0; $totalCommissionPaid = 0; ?>
                                        @foreach($commission['leads'] as $lead)
                                        <tr>
                                            <td>
                                                <a href="{{ route('admin.leads.show', $lead['id']) }}" 
                                                    class="text-primary fw-bold"
                                                    title="View Lead Profile">
                                                {{ $lead['lead_id'] }}
                                                </a>
                                            </td>
                                            <td>{{ $lead['name'] }}</td>
                                            <td>{{ $lead['college'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ App\Helpers\LeadStatus::getColor($lead['status']) }}">
                                                    {{ $lead['status'] }}
                                                </span>
                                            </td>
                                            <td>₹{{ number_format($lead['agent_commission']) }}</td>
                                            <td>₹{{ number_format($lead['commission_paid']) }}</td>
                                            <td>₹{{ number_format($lead['agent_commission'] - $lead['commission_paid']) }}</td>
                                            <td>{{ $lead['admission_date'] ? date('d M Y', strtotime($lead['admission_date'])) : 'N/A' }}</td>
                                        </tr>
                                        <?php 
                                            $totalAgentCommission += $lead['agent_commission']; 
                                            $totalCommissionPaid += $lead['commission_paid'];
                                        ?>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total</th>
                                            <th>₹{{ number_format($totalAgentCommission) }}</th>
                                            <th>₹{{ number_format($totalCommissionPaid) }}</th>
                                            <th>₹{{ number_format($totalAgentCommission - $totalCommissionPaid) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
            ]
        });
    });
</script>
@endsection