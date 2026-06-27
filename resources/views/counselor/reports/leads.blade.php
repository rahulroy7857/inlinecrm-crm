@extends('counselor.layouts.app')
@section('title', 'Leads Report')
@section('style')   
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d9dee3;
        padding: 0.4375rem 0.875rem;
        min-height: 38px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d9dee3;
        padding: 0 !important;
        max-height: 38px;
    }
    /* Toast Styles */
    .toast-container {
        z-index: 1090 !important;
        position: fixed;
    }
    .toast {
        min-width: 300px;
    }
    .toast-header .btn-close {
        margin-right: -0.375rem;
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
                        <h5 class="">Leads Report</h5>
                        <button id="bulkTransferBtn" class="btn btn-info me-2 d-none">
                            Transfer Selected
                        </button>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="d-flex justify-content-center">
                        <form action="{{ route('counselor.reports.leads') }}" method="GET" style="width: 99%; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="fromDate" class="form-label">From Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="fromDate" name="from_date" value="{{ request('from_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="toDate" class="form-label">To Date</label>
                                    <input type="date" class="form-control" onfocus="this.showPicker()" id="toDate" name="to_date" value="{{ request('to_date') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="college" class="form-label">College</label>
                                    <select class="select2-multiple form-select" id="college" name="college_id[]" multiple>
                                        <option value="">Select college</option>
                                        @foreach($colleges as $college)
                                            <option value="{{ $college->id }}" {{ in_array($college->id, (array)request('college_id')) ? 'selected' : '' }}>
                                                {{ $college->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="course" class="form-label">Course</label>
                                    <select class="select2-multiple form-select" id="course" name="course_id[]" multiple>
                                        <option value="">Select course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ in_array($course->id, (array)request('course_id')) ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="source" class="form-label">Source</label>
                                    <select class="select2-multiple form-select" id="source" name="source_id[]" multiple>
                                        <option value="">Select source</option>
                                        @foreach($sources as $source)
                                            <option value="{{ $source->id }}" {{ in_array($source->id, (array)request('source_id')) ? 'selected' : '' }}>
                                                {{ $source->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="select2-multiple form-select" id="status" name="status[]" multiple>
                                        <option value="">Select status</option>
                                        @foreach($statuses as $key => $status)
                                            <option value="{{ $status }}" {{ in_array($status, (array)request('status')) ? 'selected' : '' }}>
                                                {{ $status }}
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
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                    <th>SL.No</th>
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
                                    <td>{{ $index + 1 }}</td>
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
                                            <span class="tf-icons bx bx-show"></span>
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

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <!-- Success Toast -->
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="bx bx-check-circle me-2"></i>
            <strong class="me-auto">Success</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>

    <!-- Warning Toast -->
    <div id="warningToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-warning text-dark">
            <i class="bx bx-error-circle me-2"></i>
            <strong class="me-auto">Warning</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>

    <!-- Error Toast -->
    <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-danger text-white">
            <i class="bx bx-x-circle me-2"></i>
            <strong class="me-auto">Error</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#leadsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('.select2-multiple').select2({
            placeholder: function() {
                return $(this).attr('placeholder') || 'Select options';
            },
            allowClear: true,
            closeOnSelect: false
        });

        // Handle select all checkbox
        $('#selectAll').change(function() {
            $('.lead-checkbox').prop('checked', $(this).prop('checked'));
            updateBulkTransferButton();
        });

        // Handle individual checkboxes
        $(document).on('change', '.lead-checkbox', function() {
            updateBulkTransferButton();
            
            // Update select all checkbox
            if ($('.lead-checkbox:checked').length === $('.lead-checkbox').length) {
                $('#selectAll').prop('checked', true);
            } else {
                $('#selectAll').prop('checked', false);
            }
        });

        // Function to update transfer button visibility
        function updateBulkTransferButton() {
            if ($('.lead-checkbox:checked').length > 0) {
                $('#bulkTransferBtn').removeClass('d-none');
            } else {
                $('#bulkTransferBtn').addClass('d-none');
            }
        }

        // Handle bulk transfer button click
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

        // Handle bulk transfer form submission
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
                error: function(xhr) {
                    showToast('error', 'Error transferring leads. Please try again.');
                    submitBtn.prop('disabled', false).text('Transfer');
                }
            });
        });

        // Updated showToast function
        function showToast(type, message) {
            const toast = $(`#${type}Toast`);
            toast.find('.toast-body').html(message);
            const bsToast = new bootstrap.Toast(toast, {
                animation: true,
                autohide: true,
                delay: 3000
            });
            bsToast.show();
        }
    });
</script>
@endsection