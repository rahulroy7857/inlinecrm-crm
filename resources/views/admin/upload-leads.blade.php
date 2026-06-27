@extends('admin.layouts.app')
@section('title', 'Upload Leads')
@section('style')   
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
    .select2-container {
        z-index: 1000;
    }

    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem + 2px);
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
                        <h5 class="">Upload Leads</h5>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="mb-3">
                        <p class="text-danger">Rules:</p>
                        <ul>
                            <li>Only 1000 leads can be uploaded at a time.</li>
                            <li>File type should be Excel (.xlsx or .xls).</li>
                            <li>Mandatory fields name, email, mobile, country, state, course.</li>
                        </ul>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-6 col-lg-6">
                        <form id="uploadForm" action="{{ route('admin.leads.upload') }}" method="POST" enctype="multipart/form-data" style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">
                            @csrf
                            <div class="mb-3">
                                <label for="source_id" class="form-label">Source Name</label>
                                <select class="form-select" id="source_id" name="source_id" required>
                                    <option value="" disabled selected>Select Source</option>
                                    @foreach($sources as $source)
                                        <option value="{{ $source->id }}">{{ $source->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="source_id" class="form-label">Assign To</label>
                                <select class="form-select" id="assign_to" name="assign_to">
                                    <option value="" selected>Default</option>
                                    @foreach($counselors as $counselor)
                                        <option value="{{ $counselor->id }}">{{ $counselor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="leadsFile" class="form-label">Upload Excel File</label>
                                <input type="file" class="form-control" id="leadsFile" name="leads_file" accept=".xlsx, .xls" required>
                            </div>
                            <div class="progress mb-3 d-none" id="uploadProgress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <div id="uploadStatus" class="alert d-none mb-3"></div>
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                                <span class="btn-text">Upload Leads</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>    
    </div>
</div>

<!-- Toast Containers -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <i class="bx bx-check-circle me-2"></i>
            <strong class="me-auto">Success</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body"></div>
    </div>

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
        $('#source_id').select2({
            width: '100%',
            placeholder: "Select Source",
            allowClear: true,
            dropdownAutoWidth: true,
        });
});
function showToast(type, message) {
    const toastEl = document.getElementById(`${type}Toast`);
    const toast = new bootstrap.Toast(toastEl, {
        animation: true,
        autohide: true,
        delay: 3000
    });
    
    // Update toast content
    $(toastEl).find('.toast-body').html(message);
    
    // Hide any existing toasts
    $('.toast').each(function() {
        const t = bootstrap.Toast.getInstance(this);
        if (t) t.hide();
    });
    
    // Show new toast
    toast.show();
}
 $(document).ready(function() {
    const form = $('#uploadForm');
    const submitBtn = $('#submitBtn');
    const progressBar = $('#uploadProgress');
    const progressBarInner = progressBar.find('.progress-bar');
    const statusDiv = $('#uploadStatus');
    const spinner = submitBtn.find('.spinner-border');
    const btnText = submitBtn.find('.btn-text');

    form.on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        // Reset UI
        progressBar.removeClass('d-none');
        progressBarInner.css('width', '0%');
        statusDiv.removeClass('alert-success alert-danger').addClass('d-none');
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Uploading...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        progressBarInner.css('width', percent + '%');
                        progressBarInner.text(percent + '%');
                    }
                });
                return xhr;
            },
            success: function(response) {
                if (response.success) {
                    progressBarInner.css('width', '100%');
                    statusDiv.removeClass('d-none alert-danger')
                        .addClass('alert-success')
                        .html(`Successfully uploaded ${response.count} leads`);
                    
                    // showToast('success', 'Leads uploaded successfully');
                    
                    // Reset form
                    form[0].reset();
                    $('#source_id').val(null).trigger('change');
                    
                    // Reset UI after short delay
                    setTimeout(() => {
                        progressBar.addClass('d-none');
                        statusDiv.addClass('d-none');
                        submitBtn.prop('disabled', false);
                        spinner.addClass('d-none');
                        btnText.text('Upload Leads');
                        
                        // Redirect to leads page
                        if(response.count > 0){
                            window.location.href = '{{ route("admin.new-leads") }}';
                        }
                    }, 2000);
                } else {
                    throw new Error(response.message);
                }
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Error uploading file';
                statusDiv.removeClass('d-none alert-success')
                    .addClass('alert-danger')
                    .text(message);
                
                // showToast('error', message);
                
                // Reset UI
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text('Upload Leads');
            }
        });
    });
});
</script>
@endsection