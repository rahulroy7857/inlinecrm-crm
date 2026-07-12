@extends('counselor.layouts.app')
@section('title', 'Search Leads')
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
                        <h5 class="">Search Leads</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 col-lg-6">
                            <form action="" method="GET" style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                                <div class="mb-3">
                                    <label for="referenceName" class="form-label">Column Name</label>
                                    <select class="form-select" id="referenceName" name="column_name" required>
                                        <option value="" disabled {{ request('column_name') ? '' : 'selected' }}>Select</option>
                                        <option value="mobile" {{ request('column_name') == 'mobile' ? 'selected' : '' }}>Mobile</option>
                                        <option value="personal_email" {{ request('column_name') == 'personal_email' ? 'selected' : '' }}>Email</option>
                                        <option value="state" {{ request('column_name') == 'state' ? 'selected' : '' }}>State</option>
                                        <option value="lead_id" {{ request('column_name') == 'lead_id' ? 'selected' : '' }}>Lead ID</option>
                                        <option value="name" {{ request('column_name') == 'name' ? 'selected' : '' }}>Name</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="leadEmail" class="form-label">Value</label>
                                    <input type="text" value="{{ request('value') }}" class="form-control" id="leadEmail" name="value" placeholder="Value">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="card-body mt-3 border-top">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>State</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($leads ?? [] as $lead)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $lead->lead_id }}</td>
                                        <td>{{ $lead->name }}</td>
                                        <td>{{ $lead->personal_email }}</td>
                                        <td>{{ $lead->mobile }}</td>
                                        <td>{{ $lead->state }}</td>
                                        <td>{!! \App\Helpers\LeadStatus::getBadge($lead->status) !!}</td>
                                        <td>
                                            <a href="{{ route('counselor.leads.show', $lead->id) }}" 
                                            class="btn btn-icon btn-outline-primary">
                                                <i class="bx bx-show"></i>
                                            </a>
                                           
                                        </td>
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
        // Handle delete button click
        $(document).on('click', '.delete-lead', function(e) {
            e.preventDefault();
            const button = $(this);
            const leadId = button.data('id');
            const leadName = button.data('lead-name');

            openCrmDeleteModal('Are you sure you want to delete lead "' + leadName + '"?', function () {
                $.ajax({
                    url: '/admin/delete-lead/' + leadId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            button.closest('tr').remove();
                            showToast('success', 'Lead deleted successfully');
                        }
                    },
                    error: function() {
                        showToast('error', 'Error deleting lead');
                    }
                });
            });
        });
    });
</script>
@endsection