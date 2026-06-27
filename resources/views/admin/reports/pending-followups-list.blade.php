@extends('admin.layouts.app')
@section('title', 'Pending Followups - ' . $counselor->name)
@section('style')   
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
                        <h5 class="">Pending Tasks - {{ $counselor->name }}</h5>
                        <!-- <a href="{{ route('admin.reports.pending-followups') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back
                        </a> -->
                    </div>
                </div>
                <div class="card-body mt-3">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Name</th>
                                    <th>College</th>
                                    <th>Course</th>
                                    <th>Source</th>
                                    <th>Status</th>
                                    <th>Next Followup</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($leads as $index => $lead)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.leads.show', $lead->id) }}" 
                                            class="text-primary fw-bold"
                                            title="View Lead Profile">
                                                {{ $lead->lead_id ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->college->name ?? '-' }}</td>
                                    <td>{{ $lead->course->name ?? '-' }}</td>
                                    <td>{{ $lead->source->name ?? '-' }}</td>
                                    <td>{!! \App\Helpers\LeadStatus::getBadge($lead->status) !!}</td>
                                    <td>{{ $lead->next_follow_up->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{url('/admin/lead-profile/'.$lead->id)}}">
                                            <button type="button" class="btn btn-icon btn-outline-primary">
                                                <span class="tf-icons bx bx-show"></span>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">No pending tasks found</td>
                                </tr>
                                @endforelse
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
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            order: [[8, 'asc']] // Sort by next followup date
        });
    });
</script>
@endsection