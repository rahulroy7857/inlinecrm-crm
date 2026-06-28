@extends('admin.layouts.app')
@section('title', 'Activity Logs')
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
                        <h5 class="">Activity Logs</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('admin.logs.activity') }}" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="fromDate" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="toDate" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 col-6">
                                    <button type="submit" class="btn btn-primary w-100" style="margin-top: 32px;">Search</button>
                                </div>
                                <div class="col-md-3 col-6">
                                    <a href="{{ route('admin.logs.activity') }}" class="btn btn-secondary w-100" style="margin-top: 32px;">Reset</a>
                                </div>
                            </div>                       
                        </form>
                    </div>
                </div>

                <div class="card-body mt-3 border-top">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Log Content</th>
                                    <th>Action</th>
                                    <th>User</th>
                                    <th>Properties</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->log_content }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->causer->name ?? 'System' }}</td>
                                    <td>
                                        @if($log->properties)
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#propertiesModal{{ $log->id }}">
                                                View Details
                                            </button>

                                            <!-- Properties Modal -->
                                            <div class="modal fade" id="propertiesModal{{ $log->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-bottom">
                                                            <h5 class="modal-title">Log Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @if(isset($log->properties['old']))
                                                                <h6>Previous Data:</h6>
                                                                <pre class="bg-light p-3">{{ json_encode($log->properties['old'], JSON_PRETTY_PRINT) }}</pre>
                                                                
                                                                <h6>Updated Data:</h6>
                                                                <pre class="bg-light p-3">{{ json_encode($log->properties['new'], JSON_PRETTY_PRINT) }}</pre>
                                                            @else
                                                                <pre class="bg-light p-3">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer border-top">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">No details</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at->format('d M Y g:i A') }}</td>
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
@endsection
@section('scripts')   
@include('admin.partials.datatables-scripts')

<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
        // Validate date range
        $('form').on('submit', function(e) {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();
            
            if (fromDate && toDate && fromDate > toDate) {
                e.preventDefault();
                alert('From Date cannot be greater than To Date');
            }
        });
    });
</script>
@endsection