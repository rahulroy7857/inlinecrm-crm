@extends('admin.layouts.app')
@section('title', 'Transfer Report')
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
                        <h5 class="">Transfer Report</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('admin.reports.transfer') }}" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate" onfocus="this.showPicker()" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate" onfocus="this.showPicker()" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="counselor" class="form-label">Counselor</label>
                                    <select class="form-select" id="counselor" name="counselor_id">
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

                <div class="card-body mt-3 border-top">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Lead Name</th>
                                    <th>From Counselor</th>
                                    <th>To Counselor</th>
                                    <th>Transfer Date</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($transfers as $index => $transfer)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <a href="{{ route('admin.leads.show', $transfer->lead_id) }}" 
                                            class="text-primary fw-bold"
                                            title="View Lead Profile">
                                            {{ $transfer->lead->lead_id ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>{{ $transfer->lead->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->fromCounselor->name ?? 'Unassigned' }}</td>
                                    <td>{{ $transfer->toCounselor->name ?? 'N/A' }}</td>
                                    <td>{{ $transfer->created_at->format('d M Y, h:i A') }}</td>
                                    <td>{{ $transfer->note }}</td>
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
        initCrmDataTable('#leadsTable', { order: [[5, 'desc']], pageLength: 25 });
    });
</script>
@endsection