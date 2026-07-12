@extends('counselor.layouts.app')
@section('title', 'Notifications')
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
                        <h5 class="">Notifications</h5>
                        
                      </div>
                </div>
                <div class="card-body mt-3">
                    
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Notification</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                
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
    });
</script>
@endsection