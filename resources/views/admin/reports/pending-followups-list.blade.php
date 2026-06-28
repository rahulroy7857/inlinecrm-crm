@extends('admin.layouts.app')
@section('title', 'Pending Followups - ' . $counselor->name)
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
                        <h5 class="">Pending Tasks - {{ $counselor->name }}</h5>
                        <!-- <a href="{{ route('admin.reports.pending-followups') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back
                        </a> -->
                    </div>
                </div>
                <div class="card-body mt-3">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
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
                                                <i class="bx bx-show"></i>
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
</div>
@endsection

@section('scripts')   
@include('admin.partials.datatables-scripts')
<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable', { order: [[8, 'asc']] });
    });
</script>
@endsection