@extends('counselor.layouts.app')
@section('title', 'New Leads')
@section('style')   
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
    /* Ensure proper stacking context */
    .layout-wrapper {
        z-index: 1;
    }

    .layout-container {
        z-index: 2;
    }

    .layout-page {
        z-index: 3;
    }
    .toast-container {
        z-index: 1060;
    }
    
    .toast {
        min-width: 300px;
    }
    
    .toast-header .btn-close {
        margin-right: -0.375rem;
    }
    /* Select2 Styles - Clean version */
    .offcanvas {
        z-index: 1045;
    }

    .offcanvas .select2-container {
        width: 100% !important;
    }

    /* Fix for modal/offcanvas select2 */
    .select2-container {
        z-index: 9999;
    }

    .select2-dropdown {
        z-index: 9999;
        border-color: #d9dee3;
    }
    .select2-container--open .select2-dropdown {
        z-index: 10000;
    }
    .offcanvas-backdrop {
        z-index: 1050;
    }

    .offcanvas {
        z-index: 1051;
    }

    .select2-container {
        z-index: 1052 !important;
    }

    .select2-dropdown {
        z-index: 1053 !important;
        border-color: #d9dee3;
    }

    .select2-container--open .select2-dropdown {
        z-index: 1054 !important;
    }

    /* Fix offcanvas overlay */
    .offcanvas-end {
        width: 450px;
        border-left: 1px solid rgba(0, 0, 0, 0.175);
        transform: translateX(100%);
    }

    .offcanvas-end.show {
        transform: translateX(0);
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">New Leads</h5>
                        <div class="">
                          <!-- <button
                            class="btn btn-primary"
                            type="button"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasEnd"
                            aria-controls="offcanvasEnd"
                          >
                            Add New Lead
                          </button> -->
                          <form action="{{ route('counselor.leads.store') }}" method="POST" id="newLeadForm">
                          @csrf
                          <div
                            class="offcanvas offcanvas-end"
                            tabindex="-1"
                            id="offcanvasEnd"
                            aria-labelledby="offcanvasEndLabel"
                          >
                            <div class="offcanvas-header border-bottom">
                              <h5 id="offcanvasEndLabel" class="offcanvas-title">Add New Lead</h5>
                              <button
                                type="button"
                                class="btn-close text-reset"
                                data-bs-dismiss="offcanvas"
                                aria-label="Close"
                              ></button>
                            </div>
                                <div class="offcanvas-body ">
                                        <div class="mb-2">
                                            <label class="form-label" for="basic-default-fullname">Full Name</label>
                                            <input type="text" name="name" class="form-control" id="basic-default-fullname" placeholder="John Doe" required/>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="basic-default-phone">Phone No</label>
                                            <input
                                                type="text"
                                                name="mobile"
                                                id="basic-default-phone"
                                                class="form-control phone-mask"
                                                placeholder="658 799 8941"
                                                required
                                            />
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label" for="basic-default-email">Email</label>
                                            <input
                                                type="email"
                                                name="email"
                                                id="basic-default-email"
                                                class="form-control"
                                                placeholder="Email"
                                                required
                                            />
                                        </div>
                                        <div class="mb-2">
                                            <label for="course_id" class="form-label">Course</label>
                                            <select class="form-select" id="course_id" name="course_id" required>
                                                <option value="">Select Course</option>
                                                @foreach($courses as $course)
                                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="academic_year_id" class="form-label">Academic Year</label>
                                            <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                                                <option value="">Select Academic Year</option>
                                                @foreach($academicYears as $year)
                                                    <option value="{{ $year->id }}" 
                                                        {{ session('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                        {{ $year->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="source_id" class="form-label">Source</label>
                                            <select class="form-select" id="source_id" name="source_id" aria-label="Default select example" required>
                                                <option selected>Select Source</option>
                                                @foreach($sources as $source)
                                                    <option value="{{ $source['value'] }}">{{ $source['text'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="exampleFormControlSelect1" class="form-label">Country</label>
                                            <select class="form-select" id="country" name="country" required>
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country }}" {{ $country === 'India' ? 'selected' : '' }}>
                                                        {{ $country }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label for="exampleFormControlSelect1" class="form-label">State</label>
                                            <select class="form-select" id="state" name="state" required>
                                                <option value="">Select State</option>
                                            </select>
                                        </div>
                                </div>
                                <div class="offcanvas-footer border-top">
                                    <div class="d-flex justify-content-end mt-3 mb-3 mr-3" style="margin-right: 20px;">
                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Close</button>
                                        <button type="button" class="btn btn-info me-2" id="verifyBtn">
                                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                                            <span class="btn-text">Verify</span>
                                        </button>
                                        <button type="submit" class="btn btn-primary d-none" id="submitBtn">
                                            <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                                            <span class="btn-text">Submit</span>
                                        </button>
                                    </div>
                                </div>
                          </div>
                          </form>
                        </div>
                      </div>
                </div>
                <div class="card-body mt-3">
                    
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Lead ID</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>State</th>
                                    <th>Status</th>
                                    <th>Next Follow Up</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            @foreach($leads as $lead)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->lead_id }}</td>
                                    <td>{{ $lead->name }}</td>
                                    <td>{{ $lead->mobile }}</td>
                                    <td>{{ $lead->state }}</td>
                                    <td>{!! \App\Helpers\LeadStatus::getBadge($lead->status) !!}</td>
                                    <td>{{ $lead->next_follow_up->format('d M Y h:i A') }}</td>
                                    <td>
                                        <a href="{{url('/counselor/lead-profile/'.$lead->id)}}">
                                            <button type="button" class="btn btn-icon btn-outline-primary">
                                                <span class="tf-icons bx bx-show"></span>
                                            </button>
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


<!-- Success Toast -->
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
<script src="{{ url('crm/assets/js/countries-states.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ url('crm/js/common.js') }}"></script>
<script>
    $(document).ready(function() {
        initializeFormSubmission('#newLeadForm');
        $('#leadsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
$(document).ready(function() {
    const selectConfig = {
        dropdownParent: $('#offcanvasEnd'),
        width: '100%',
        allowClear: true,
        containerCssClass: 'select2-container--full',
        dropdownCssClass: 'select2-dropdown--full'
    };

    $('#course_id').select2({
        ...selectConfig,
        placeholder: "Select Course"
    });

    $('#source_id').select2({
        ...selectConfig,
        placeholder: "Select Source"
    });

    $('#country').select2({
        ...selectConfig,
        placeholder: "Select Country"
    });

    $('#state').select2({
        ...selectConfig,
        placeholder: "Select State"
    });
    // Initialize states for default country (India)
    const countrySelect = $('#country');
    const stateSelect = $('#state');
    const phoneInput = $('#basic-default-phone');

    // Function to validate phone number
    function validatePhoneNumber() {
        const country = countrySelect.val();
        const phone = phoneInput.val().replace(/\D/g, ''); // Remove non-digits
        
        if (country === 'India') {
            if (phone.length !== 10) {
                phoneInput.get(0).setCustomValidity('Phone number must be 10 digits for India');
                return false;
            }
        }
        
        phoneInput.get(0).setCustomValidity('');
        return true;
    }
    
    // Add validation on phone input
    phoneInput.on('input', validatePhoneNumber);
    
    // Add validation on country change
    countrySelect.on('change', function() {
        validatePhoneNumber();
        loadStates($(this).val());
        $('#state').val(null).trigger('change');
    });
    
    function loadStates(country) {
        stateSelect.empty().append('<option value="">Select State</option>');
        
        if (country && countriesData[country]) {
            countriesData[country].forEach(state => {
                stateSelect.append(`<option value="${state}">${state}</option>`);
            });
        }
        $('#state').trigger('change');
    }

    // Load states on page load for default country
    loadStates(countrySelect.val());

    // Load states when country changes
    countrySelect.on('change', function() {
        loadStates($(this).val());
    });

    // Form submission validation
    $('form').on('submit', function(e) {
        if (!validatePhoneNumber()) {
            e.preventDefault();
            return false;
        }
    });
    // Ensure focus works properly
    $(document).on('select2:open', () => {
        document.querySelector('.select2-container--open .select2-search__field').focus();
    });

    // Prevent offcanvas scroll when select2 is open
    $('.select2').on('select2:open', function() {
        $('.offcanvas-body').css('overflow', 'hidden');
    }).on('select2:close', function() {
        $('.offcanvas-body').css('overflow', '');
    });
});


// Reset validation on input
$('input, select').on('input change', function() {
    $(this).removeClass('is-invalid');
});

// Clear form on offcanvas close
$('#offcanvasEnd').on('hidden.bs.offcanvas', function () {
    $('#newLeadForm')[0].reset();
    $('input, select').removeClass('is-invalid');
    $('.select2').val(null).trigger('change');
    $('#verifyBtn').removeClass('d-none').prop('disabled', false);
    $('#submitBtn').addClass('d-none');
});

function showToast(type, message) {
    const toast = $(`#${type}Toast`);
    toast.find('.toast-body').html(message);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// Add this inside your $(document).ready function
// $('#verifyBtn').on('click', function() {
//     const btn = $(this);
//     const spinner = btn.find('.spinner-border');
//     const btnText = btn.find('.btn-text');
//     const form = $('#newLeadForm');

//     // Basic validation
//     if (!form[0].checkValidity()) {
//         form[0].reportValidity();
//         return;
//     }

//     // Show loading state
//     btn.prop('disabled', true);
//     spinner.removeClass('d-none');
//     btnText.text('Verifying...');

//     // Send verification request
//     $.ajax({
//         url: '{{ route("counselor.leads.verify") }}',
//         method: 'POST',
//         data: {
//             _token: $('meta[name="csrf-token"]').attr('content'),
//             mobile: $('#basic-default-phone').val(),
//             email: $('#basic-default-email').val()
//         },
//         success: function(response) {
//             if (response.can_proceed) {
//                 // Show success toast and submit button
//                 const successToast = $('#successToast');
//                 successToast.find('.toast-body').text('Verification successful. You can proceed.');
//                 const bsSuccessToast = new bootstrap.Toast(successToast);
//                 bsSuccessToast.show();
                
//                 $('#submitBtn').removeClass('d-none');
//                 btn.addClass('d-none');
//             } else {
//                 // Show warning toast for duplicates
//                 let message = 'Duplicate records found:\n';
//                 if (response.duplicates.mobile) {
//                     message += `\nPhone: ${response.duplicates.mobile.name} (${response.duplicates.mobile.lead_id})`;
//                 }
//                 if (response.duplicates.email) {
//                     message += `\nEmail: ${response.duplicates.email.name} (${response.duplicates.email.lead_id})`;
//                 }
                
//                 const warningToast = $('#warningToast');
//                 warningToast.find('.toast-body').html(message.replace(/\n/g, '<br>'));
//                 const bsWarningToast = new bootstrap.Toast(warningToast);
//                 bsWarningToast.show();
//             }
//         },
//         error: function(xhr) {
//             // Show error toast
//             const errorToast = $('#errorToast');
//             errorToast.find('.toast-body').text('Error during verification. Please try again.');
//             const bsErrorToast = new bootstrap.Toast(errorToast);
//             bsErrorToast.show();
//         },
//         complete: function() {
//             // Reset button state
//             btn.prop('disabled', false);
//             spinner.addClass('d-none');
//             btnText.text('Verify');
//         }
//     });
// });

$(document).ready(function() {
    // Track verification status
    let isVerified = false;

    // Monitor email and phone changes
    $('#basic-default-phone, #basic-default-email').on('change input', function() {
        if (isVerified) {
            $('#submitBtn').addClass('d-none');
            $('#verifyBtn').removeClass('d-none');
            isVerified = false;
            showToast('warning', 'Contact details changed. Please verify again.');
        }
    });

    // Update verify button click handler
    $('#verifyBtn').on('click', function() {
        const btn = $(this);
        const spinner = btn.find('.spinner-border');
        const btnText = btn.find('.btn-text');
        const form = $('#newLeadForm');

        // Basic validation
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        // Show loading state
        btn.prop('disabled', true);
        spinner.removeClass('d-none');
        btnText.text('Verifying...');

        // Send verification request
        $.ajax({
            url: '{{ route("counselor.leads.verify") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                mobile: $('#basic-default-phone').val(),
                email: $('#basic-default-email').val()
            },
            success: function(response) {
                if (response.can_proceed) {
                    isVerified = true;
                    showToast('success', 'Verification successful! You can proceed.');
                    $('#submitBtn').removeClass('d-none');
                    btn.addClass('d-none');
                } else {
                    let message = '<strong>Duplicate records found:</strong><br>';
                    if (response.duplicates.mobile) {
                        const mobileField = response.duplicates.mobile.field || 'Phone';
                        message += `<br>${mobileField}: ${response.duplicates.mobile.name} (${response.duplicates.mobile.lead_id})`;
                    }
                    if (response.duplicates.email) {
                        const emailField = response.duplicates.email.field || 'Email';
                        message += `<br>${emailField}: ${response.duplicates.email.name} (${response.duplicates.email.lead_id})`;
                    }
                    showToast('warning', message);
                }
            },
            error: function(xhr) {
                showToast('error', 'Error during verification. Please try again.');
                alert('error');
            },
            complete: function() {
                btn.prop('disabled', false);
                spinner.addClass('d-none');
                btnText.text('Verify');
            }
        });
    });

    // Update form submission handler
    $('#newLeadForm').on('submit', function(e) {
        if (!isVerified) {
            e.preventDefault();
            showToast('warning', 'Please verify contact details before submitting.');
            return false;
        }
    });

    // Update offcanvas close handler
    $('#offcanvasEnd').on('hidden.bs.offcanvas', function () {
        $('#newLeadForm')[0].reset();
        $('input, select').removeClass('is-invalid');
        $('.select2').val(null).trigger('change');
        $('#verifyBtn').removeClass('d-none').prop('disabled', false);
        $('#submitBtn').addClass('d-none');
        isVerified = false;
    });
});
function showToast(type, message) {
    // Remove alerts after success
    const toast = document.getElementById(`${type}Toast`);
    const bsToast = new bootstrap.Toast(toast, {
        animation: true,
        autohide: true,
        delay: 3000
    });

    // Update toast content
    toast.querySelector('.toast-body').innerHTML = message;
    
    // Show the toast
    bsToast.show();
}

$(document).ready(function() {
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
});
</script>
@endsection