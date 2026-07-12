@extends('admin.layouts.app')
@section('title', 'Upload Leads')
@section('style')
@include('admin.partials.datatables-head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
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
                            <li>Mandatory fields: name and mobile. Email, country, state, and course are optional.</li>
                        </ul>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-12 col-md-8 col-lg-6">
                        <form id="uploadForm" class="crm-upload-panel" action="{{ route('admin.leads.upload') }}" method="POST" enctype="multipart/form-data">
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

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#source_id').select2({
            width: '100%',
            placeholder: "Select Source",
            allowClear: true,
            dropdownAutoWidth: true,
        });
    });

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
                    
                    if (window.showCrmToast) {
                        window.showCrmToast('success', `Successfully uploaded ${response.count} leads`);
                    }
                    
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
                
                if (window.showCrmToast) {
                    window.showCrmToast('error', message);
                }
                
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