@extends('admin.layouts.app')
@section('title', 'Counselors')
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
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @php $lockedCounselors = $counselors->where('break_login_locked', true); @endphp
            @if($lockedCounselors->isNotEmpty())
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>{{ $lockedCounselors->count() }} counselor(s)</strong> need login permission after exceeding break time.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(($pendingBreakRequests ?? collect())->isNotEmpty())
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <strong>{{ $pendingBreakRequests->count() }} break request(s)</strong> waiting for approval.
                    <a href="{{ route('admin.settings.counselor-breaks') }}" class="alert-link">Review in Counselor Breaks</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Counselors</h5>
                        <div class="">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bx bx-plus me-1"></i>Add Counselor
                        </button>
                         
                        </div>
                      </div>
                </div>
                <div class="card-body mt-3">
                    
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Languages</th>
                                    <th>Joining Date</th>
                                    <th>Salary</th>
                                    <th>Status</th>
                                    <th>Break Lock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              @foreach($counselors as $counselor)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $counselor->name }}</td>
                                    <td>{{ $counselor->email }}</td>
                                    <td>{{ $counselor->mobile }}</td>
                                    <td>{{ implode(', ', $counselor->languages ?? []) }}</td>
                                    <td>{{ $counselor->joining_date?->format('d-m-Y') ?? '—' }}</td>
                                    <td>₹{{ number_format($counselor->salary ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $counselor->status ? 'success' : 'danger' }}">
                                            {{ $counselor->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($counselor->break_login_locked)
                                            <button type="button"
                                                    class="btn btn-sm btn-warning grant-login-btn"
                                                    data-id="{{ $counselor->id }}"
                                                    data-name="{{ $counselor->name }}"
                                                    data-reason="{{ $counselor->break_login_lock_reason ?: 'Break time exceeded. Login permission required.' }}"
                                                    data-locked-at="{{ $counselor->break_login_locked_at?->format('d-m-Y h:i A') }}">
                                                <i class="bx bx-lock-open me-1"></i>Grant Login
                                            </button>
                                        @else
                                            <span class="badge bg-label-secondary">Clear</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-icon btn-outline-warning edit-btn"
                                                data-id="{{ $counselor->id }}"
                                                data-name="{{ $counselor->name }}"
                                                data-email="{{ $counselor->email }}"
                                                data-mobile="{{ $counselor->mobile }}"
                                                data-languages="{{ json_encode($counselor->languages) }}"
                                                data-joining-date="{{ $counselor->joining_date?->format('Y-m-d') }}"
                                                data-office-start="{{ $counselor->office_start_time ? \Carbon\Carbon::parse($counselor->office_start_time)->format('H:i') : '' }}"
                                                data-office-end="{{ $counselor->office_end_time ? \Carbon\Carbon::parse($counselor->office_end_time)->format('H:i') : '' }}"
                                                data-working-days="{{ json_encode($counselor->working_days ?? []) }}"
                                                data-salary="{{ $counselor->salary }}"
                                                data-status="{{ $counselor->status }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.users.counselor.destroy', $counselor->id) }}" 
                                              method="POST" 
                                              style="display:inline;"
                                              data-confirm-delete="Are you sure you want to delete this counselor?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.users.counselor.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Counselor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" class="form-control" value="{{ old('joining_date') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Office Start Time</label>
                        <input type="time" name="office_start_time" class="form-control" value="{{ old('office_start_time', '09:00') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Office End Time</label>
                        <input type="time" name="office_end_time" class="form-control" value="{{ old('office_end_time', '18:00') }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Monthly Salary (₹)</label>
                    <input type="number" name="salary" class="form-control" min="0" step="0.01" value="{{ old('salary') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Working Days</label>
                    <div class="row">
                        @foreach(config('weekdays') as $key => $label)
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="working_days[]" value="{{ $key }}"
                                    {{ is_array(old('working_days')) && in_array($key, old('working_days')) ? 'checked' : '' }}>
                                <label class="form-check-label">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Languages</label>
                    <div class="row">
                        @foreach(config('languages.indian') as $language)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="languages[]" value="{{ $language }}">
                                <label class="form-check-label">{{ $language }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" name="status" value="1" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Edit Counselor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Similar fields as add modal but with id prefixed with 'edit_' -->
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" id="edit_mobile" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Joining Date</label>
                        <input type="date" name="joining_date" id="edit_joining_date" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Office Start Time</label>
                        <input type="time" name="office_start_time" id="edit_office_start" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Office End Time</label>
                        <input type="time" name="office_end_time" id="edit_office_end" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Monthly Salary (₹)</label>
                    <input type="number" name="salary" id="edit_salary" class="form-control" min="0" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Working Days</label>
                    <div class="row">
                        @foreach(config('weekdays') as $key => $label)
                        <div class="col-md-4 col-6">
                            <div class="form-check">
                                <input class="form-check-input edit-working-day" type="checkbox" name="working_days[]" value="{{ $key }}">
                                <label class="form-check-label">{{ $label }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Languages</label>
                    <div class="row">
                        @foreach(config('languages.indian') as $language)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input edit-language" type="checkbox" name="languages[]" value="{{ $language }}">
                                <label class="form-check-label">{{ $language }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-check">
                        <input type="hidden" name="status" value="0">
                        <input class="form-check-input" type="checkbox" name="status" id="edit_status" value="1">
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- Grant Login Permission Modal -->
<div class="modal fade" id="grantLoginModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" id="grantLoginForm">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    <i class="bx bx-lock-open text-warning me-1"></i> Grant Login Permission
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Allow <strong id="grantLoginCounselorName"></strong> to login again?
                </p>
                <div class="alert alert-warning mb-0">
                    <div class="fw-semibold mb-1">Lock reason</div>
                    <p id="grantLoginReason" class="mb-1 small"></p>
                    <p id="grantLoginLockedAt" class="mb-0 small text-muted"></p>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">
                    <i class="bx bx-lock-open me-1"></i>Grant Login
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')   
@include('admin.partials.datatables-scripts')

<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');

        $(document).on('click', '.grant-login-btn', function () {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var reason = $(this).data('reason');
            var lockedAt = $(this).data('locked-at');

            $('#grantLoginCounselorName').text(name);
            $('#grantLoginReason').text(reason);
            $('#grantLoginLockedAt').text(lockedAt ? 'Locked at: ' + lockedAt : '');

            var url = "{{ route('admin.users.counselor.unlock-break-login', ':id') }}";
            url = url.replace(':id', id);
            $('#grantLoginForm').attr('action', url);

            $('#grantLoginModal').modal('show');
        });

        // Edit button click handler
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var mobile = $(this).data('mobile');
            var languages = $(this).data('languages');
            var joiningDate = $(this).data('joining-date');
            var officeStart = $(this).data('office-start');
            var officeEnd = $(this).data('office-end');
            var workingDays = $(this).data('working-days');
            var salary = $(this).data('salary');
            var status = $(this).data('status');

            $('#edit_name').val(name);
            $('#edit_email').val(email);
            $('#edit_mobile').val(mobile);
            $('#edit_joining_date').val(joiningDate);
            $('#edit_office_start').val(officeStart);
            $('#edit_office_end').val(officeEnd);
            $('#edit_salary').val(salary);
            $('#edit_status').prop('checked', status == 1);

            // Reset and set languages
            $('.edit-language').prop('checked', false);
            if (languages) {
                languages.forEach(function(lang) {
                    $('.edit-language[value="' + lang + '"]').prop('checked', true);
                });
            }

            $('.edit-working-day').prop('checked', false);
            if (workingDays) {
                workingDays.forEach(function(day) {
                    $('.edit-working-day[value="' + day + '"]').prop('checked', true);
                });
            }

            var url = "{{ route('admin.users.counselor.update', ':id') }}";
            url = url.replace(':id', id);
            $('#editForm').attr('action', url);

            $('#editModal').modal('show');
        });
    });
</script>
@endsection