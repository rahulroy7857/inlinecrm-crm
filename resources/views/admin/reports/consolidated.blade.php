@extends('admin.layouts.app')
@section('title', 'Consolidated Counselor Report')
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
        border-bottom: 1px solid #dee2e6 !important;
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
                        <h5 class="mb-0">
                            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                            Consolidated Counselor Report
                        </h5>
                        <small class="text-muted">Current Academic Year: {{ $academicYear?->name ?? 'Not set' }}</small>
                    </div>
                </div>

                       
                        <div class="card-body mt-3 ">
                            <div class="table-responsive ">
                                <table class="table crm-table" id="leadsTable">
                                    <thead class="table-border-top-1">
                                        <tr>
                                            <th>Counselor Name</th>
                                            <th>Total Leads</th>
                                            <th>Pending Followups</th>
                                            <th>Today's Followups</th>
                                            <th>Tomorrow's Followups</th>
                                            <th>Applications</th>
                                            <th>Admissions</th>
                                            <th>Conversion Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($consolidatedData as $data)
                                        <tr>
                                            <td>
                                                <strong>{{ $data['counselor_name'] }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ number_format($data['total_leads']) }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($data['pending_followups'] > 0)
                                                    <span class="badge bg-danger">{{ number_format($data['pending_followups']) }}</span>
                                                @else
                                                    <span class="badge bg-success">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data['today_followups'] > 0)
                                                    <span class="badge bg-warning">{{ number_format($data['today_followups']) }}</span>
                                                @else
                                                    <span class="badge bg-secondary">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data['tomorrow_followups'] > 0)
                                                    <span class="badge bg-info">{{ number_format($data['tomorrow_followups']) }}</span>
                                                @else
                                                    <span class="badge bg-secondary">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning">{{ number_format($data['applications']) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ number_format($data['admissions']) }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $rateClass = $data['conversion_rate'] >= 20 ? 'high' : 
                                                                ($data['conversion_rate'] >= 10 ? 'medium' : 'low');
                                                @endphp
                                                <span class="conversion-rate {{ $rateClass }}">
                                                    {{ $data['conversion_rate'] }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="" style="border-top: 2px solid #dee2e6;">
                                        <tr>
                                            <th><strong>TOTALS</strong></th>
                                            <th class="text-center"><strong>{{ number_format($totals['total_leads']) }}</strong></th>
                                            <th class="text-center"><strong>{{ number_format($totals['pending_followups']) }}</strong></th>
                                            <th class="text-center"><strong>{{ number_format($totals['today_followups']) }}</strong></th>
                                            <th class="text-center"><strong>{{ number_format($totals['tomorrow_followups']) }}</strong></th>
                                            <th class="text-center"><strong>{{ number_format($totals['applications']) }}</strong></th>
                                            <th class="text-center"><strong>{{ number_format($totals['admissions']) }}</strong></th>
                                            <th class="text-center"><strong>{{ $totals['conversion_rate'] }}%</strong></th>
                                        </tr>
                                    </tfoot>
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