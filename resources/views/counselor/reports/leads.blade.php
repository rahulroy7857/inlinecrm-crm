@extends('counselor.layouts.app')
@section('title', 'Leads Report')
@section('style')
@include('admin.partials.datatables-head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
@php
    $hasFilters = request()->hasAny(['from_date','to_date','college_id','course_id','source_id','status']);
    $leadCount = is_countable($leads) ? count($leads) : 0;
@endphp
<div class="container-xxl flex-grow-1 container-p-y crm-page crm-report-page">
    <div class="crm-report-hero">
        <div>
            <h4 class="crm-report-hero__title">Leads Report</h4>
            <p class="crm-report-hero__sub">Filter your leads by date range, college, course, source, and status.</p>
        </div>
        <button id="bulkTransferBtn" class="btn btn-info d-none">
            <i class="bx bx-transfer me-1"></i>Transfer Selected
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="crm-report-filter">
                <div class="crm-report-filter__head">
                    <h6 class="crm-report-filter__head-title">
                        <i class="bx bx-filter-alt"></i>
                        Search Filters
                    </h6>
                    @if($hasFilters)
                        <a href="{{ route('counselor.reports.leads') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bx bx-x me-1"></i>Clear all
                        </a>
                    @endif
                </div>
                <div class="crm-report-filter__body">
                    <form action="{{ route('counselor.reports.leads') }}" method="GET">
                        <div class="crm-report-filter__grid">
                            <div class="crm-report-filter__field">
                                <label for="fromDate" class="form-label">From Date</label>
                                <input type="date" class="form-control" onfocus="this.showPicker()" id="fromDate" name="from_date" value="{{ request('from_date') }}">
                            </div>
                            <div class="crm-report-filter__field">
                                <label for="toDate" class="form-label">To Date</label>
                                <input type="date" class="form-control" onfocus="this.showPicker()" id="toDate" name="to_date" value="{{ request('to_date') }}">
                            </div>
                            <div class="crm-report-filter__field">
                                <label for="college" class="form-label">College</label>
                                <select class="select2-multiple form-select" id="college" name="college_id[]" multiple data-placeholder="Select college">
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}" {{ in_array($college->id, (array) request('college_id')) ? 'selected' : '' }}>
                                            {{ $college->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="crm-report-filter__field">
                                <label for="course" class="form-label">Course</label>
                                <select class="select2-multiple form-select" id="course" name="course_id[]" multiple data-placeholder="Select course">
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ in_array($course->id, (array) request('course_id')) ? 'selected' : '' }}>
                                            {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="crm-report-filter__field">
                                <label for="source" class="form-label">Source</label>
                                <select class="select2-multiple form-select" id="source" name="source_id[]" multiple data-placeholder="Select source">
                                    @foreach($sources as $source)
                                        <option value="{{ $source->id }}" {{ in_array($source->id, (array) request('source_id')) ? 'selected' : '' }}>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="crm-report-filter__field">
                                <label for="status" class="form-label">Status</label>
                                <select class="select2-multiple form-select" id="status" name="status[]" multiple data-placeholder="Select status">
                                    @foreach($statuses as $key => $status)
                                        <option value="{{ $status }}" {{ in_array($status, (array) request('status')) ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="crm-report-filter__footer">
                            <p class="crm-report-filter__hint">Select one or more filters, then search.</p>
                            <div class="crm-report-filter__actions">
                                @if($hasFilters)
                                    <a href="{{ route('counselor.reports.leads') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-refresh me-1"></i>Reset
                                    </a>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body border-top">
            <div class="crm-report-results">
                <div class="crm-report-results__meta">
                    Showing <strong>{{ number_format($leadCount) }}</strong> lead{{ $leadCount === 1 ? '' : 's' }}
                    @if($hasFilters)
                        <span class="text-muted">· filtered results</span>
                    @endif
                </div>
            </div>
            <div class="table-modern-wrap">
                <div class="table-responsive text-nowrap">
                    <table id="leadsTable" class="table crm-table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                </th>
                                
                                <th>Lead ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>College</th>
                                <th>Course</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($leads as $index => $lead)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input lead-checkbox" value="{{ $lead->id }}">
                                </td>
                               
                                <td>
                                    <a href="{{ route('counselor.leads.show', $lead->id) }}"
                                        class="text-primary fw-bold"
                                        title="View Lead Profile">
                                            {{ $lead->lead_id ?? 'N/A' }}
                                        </a>
                                </td>
                                <td>{{ $lead->name }}</td>
                                <td>{{ $lead->personal_email }}</td>
                                <td>{{ $lead->mobile }}</td>
                                <td>{{ $lead->college->name ?? '-' }}</td>
                                <td>{{ $lead->course->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{!! App\Helpers\LeadStatus::getBadge($lead->status) !!}">
                                        {{ $lead->status ? ucfirst($lead->status) : 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('counselor.leads.show', $lead->id) }}" class="btn btn-icon btn-outline-primary">
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

<!-- Bulk Transfer Modal -->
<div class="modal fade" id="bulkTransferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Transfer Selected Leads</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkTransferForm" action="{{ route('admin.bulk-transfer') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_ids" id="selectedLeadIds">
                    <div class="mb-3">
                        <label class="form-label">Select Counselor</label>
                        <select name="counselor_id" class="form-select" required>
                            <option value="">Select Counselor</option>
                            @foreach(App\Models\Counselor::where('status', 1)->get() as $counselor)
                                <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Next Follow-up Date</label>
                        <input type="datetime-local" onfocus="this.showPicker()"  name="next_fl_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Transfer Note</label>
                        <textarea name="transfer_note" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@include('admin.partials.datatables-scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
        $('.select2-multiple').each(function () {
            const $el = $(this);
            $el.select2({
                placeholder: $el.data('placeholder') || 'Select options',
                allowClear: true,
                closeOnSelect: false,
                width: '100%'
            });
        });

        $('#selectAll').change(function() {
            $('.lead-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkTransferButton();
        });

        $(document).on('change', '.lead-checkbox', function() {
            updateBulkTransferButton();
            if ($('.lead-checkbox:checked').length === $('.lead-checkbox').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        function updateBulkTransferButton() {
            if ($('.lead-checkbox:checked').length > 0) {
                $('#bulkTransferBtn').removeClass('d-none');
            } else {
                $('#bulkTransferBtn').addClass('d-none');
            }
        }

        $('#bulkTransferBtn').click(function() {
            const selectedLeads = $('.lead-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedLeads.length === 0) {
                showToast('warning', 'Please select leads to transfer');
                return;
            }

            $('#selectedLeadIds').val(JSON.stringify(selectedLeads));
            $('#bulkTransferModal').modal('show');
        });

        $('#bulkTransferForm').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');

            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#bulkTransferModal').modal('hide');
                    showToast('success', response.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                },
                error: function() {
                    showToast('error', 'Error transferring leads. Please try again.');
                    submitBtn.prop('disabled', false).text('Transfer');
                }
            });
        });

        function showToast(type, message) {
            if (window.showCrmToast) {
                window.showCrmToast(type, message);
            }
        }
    });
</script>
@endsection
