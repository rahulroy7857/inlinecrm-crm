@extends('admin.layouts.app')
@section('title', 'Picked Leads Report')
@section('style')
@include('admin.partials.datatables-head')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="menu-icon tf-icons bx bx-hand-up"></i>
                            Picked Leads Report
                        </h5>
                        <small class="text-muted">Current Academic Year</small>
                    </div>
                </div>

                <div class="card-body mt-3">
                    <!-- Date Filter Form -->
                    <div class="crm-filter-panel mb-4">
                        <form action="" method="GET">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" id="fromDate" name="from_date" 
                                           value="{{ request('from_date') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" id="toDate" name="to_date" 
                                           value="{{ request('to_date') }}" placeholder="To Date">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary w-100">Search</button>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <a href="{{ url('/admin/reports/picked-leads') }}" class="btn btn-secondary w-100">Clear</a>
                                </div>
                            </div>                       
                        </form>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="metric-card text-center">
                                <div class="metric-value">{{ number_format($summary['total_counselors']) }}</div>
                                <div class="metric-label">Total Counselors</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card text-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                <div class="metric-value">{{ number_format($summary['total_picked_leads']) }}</div>
                                <div class="metric-label">Total Picked Leads</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="metric-card text-center" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                <div class="metric-value">{{ number_format($summary['average_picked_per_counselor']) }}</div>
                                <div class="metric-label">Average Per Counselor</div>
                            </div>
                        </div>
                    </div>

                    <!-- Counselor Picked Leads Table -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Counselor Picked Leads Summary</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-modern-wrap">
                            <div class="table-responsive">
                                <table class="table crm-table" id="leadsTable">
                                    <thead class="">
                                        <tr>
                                            <th>SL.No</th>
                                            <th>Counselor Name</th>
                                            <th class="text-center">Picked Leads Count</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach($pickedLeadsData as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $data['counselor_name'] }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($data['picked_leads_count'] > 0)
                                                    <span class="badge bg-success">{{ number_format($data['picked_leads_count']) }}</span>
                                                @else
                                                    <span class="badge bg-secondary">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($data['picked_leads_count'] > 0)
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            onclick="showPickedLeads({{ $data['counselor_id'] }}, '{{ $data['counselor_name'] }}')">
                                                        <i class="bx bx-show"></i> View Details
                                                    </button>
                                                @else
                                                    <span class="text-muted">No leads picked</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="">
                                        <tr>
                                            <th colspan="2"><strong>TOTALS</strong></th>
                                            <th class="text-center"><strong>{{ number_format($summary['total_picked_leads']) }}</strong></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</div>

<!-- Modal for showing picked leads details -->
<div class="modal fade modal-crm-table" id="pickedLeadsModal" tabindex="-1" aria-labelledby="pickedLeadsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-table">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pickedLeadsModalLabel">Picked Leads Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pickedLeadsDetails"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@include('admin.partials.datatables-scripts')
<script>
    let modalLeadsTable = null;

    $(document).ready(function() {
        initCrmDataTable('#leadsTable', {
            order: [[1, 'asc']]
        });
    });

    function showPickedLeads(counselorId, counselorName) {
        const counselorData = @json($pickedLeadsData);
        const data = counselorData.find(item => item.counselor_id === counselorId);
        
        if (data && data.picked_leads.length > 0) {
            if (modalLeadsTable) {
                modalLeadsTable.destroy();
                modalLeadsTable = null;
            }

            let tableHtml = `
                <div class="modal-table-toolbar">
                    <h6 class="mb-0 fw-semibold">${counselorName} — Picked Leads</h6>
                    <span class="badge bg-primary">${data.picked_leads.length} leads</span>
                </div>
                <div class="modal-table-body">
                    <table class="table crm-table w-100" id="modalLeadsTable">
                        <thead>
                            <tr>
                                <th>Lead ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>College</th>
                                <th>Course</th>
                                <th>Source</th>
                                <th>Picked Date</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            data.picked_leads.forEach(lead => {
                tableHtml += `
                    <tr>
                        <td>${lead.lead_id || 'N/A'}</td>
                        <td>${lead.name || 'N/A'}</td>
                        <td>${lead.personal_email || 'N/A'}</td>
                        <td>${lead.mobile || 'N/A'}</td>
                        <td>${lead.college ? lead.college.name : 'N/A'}</td>
                        <td>${lead.course ? lead.course.name : 'N/A'}</td>
                        <td>${lead.source ? lead.source.name : 'N/A'}</td>
                        <td>${lead.picked_at ? new Date(lead.picked_at).toLocaleDateString() : 'N/A'}</td>
                    </tr>
                `;
            });
            
            tableHtml += `</tbody></table></div>`;
            
            document.getElementById('pickedLeadsDetails').innerHTML = tableHtml;
            $('#pickedLeadsModal').modal('show');

            $('#pickedLeadsModal').one('shown.bs.modal', function() {
                modalLeadsTable = initCrmDataTable('#modalLeadsTable', {
                    dom: "<'crm-dt-toolbar'<'crm-dt-export'B><'crm-dt-search'f>>rtip",
                    pageLength: 50
                });
            });
        } else {
            if (window.showCrmToast) {
                window.showCrmToast('warning', 'No picked leads found for this counselor.');
            }
        }
    }
</script>
@endsection