@extends('admin.layouts.app')
@section('title', 'Counselors')
@section('style')   
<!-- Include DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
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
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Counselors</h5>
                        <div class="">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <span class="tf-icons bx bx-plus me-1"></span>Add Counselor
                        </button>
                         
                        </div>
                      </div>
                </div>
                <div class="card-body mt-3">
                    
                    <div class="table-responsive text-nowrap">
                        <table id="leadsTable" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SL.No</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Languages</th>
                                    <th>Status</th>
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
                                    <td>
                                        <span class="badge bg-{{ $counselor->status ? 'success' : 'danger' }}">
                                            {{ $counselor->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-icon btn-outline-warning edit-btn"
                                                data-id="{{ $counselor->id }}"
                                                data-name="{{ $counselor->name }}"
                                                data-email="{{ $counselor->email }}"
                                                data-mobile="{{ $counselor->mobile }}"
                                                data-languages="{{ json_encode($counselor->languages) }}"
                                                data-status="{{ $counselor->status }}">
                                            <span class="tf-icons bx bx-edit"></span>
                                        </button>
                                        <form action="{{ route('admin.users.counselor.destroy', $counselor->id) }}" 
                                              method="POST" 
                                              style="display:inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this counselor?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger">
                                                <span class="tf-icons bx bx-trash"></span>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
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
    <div class="modal-dialog">
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

<script>
    $(document).ready(function() {
        $('#leadsTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        // Edit button click handler
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var email = $(this).data('email');
            var mobile = $(this).data('mobile');
            var languages = $(this).data('languages');
            var status = $(this).data('status');

            $('#edit_name').val(name);
            $('#edit_email').val(email);
            $('#edit_mobile').val(mobile);
            $('#edit_status').prop('checked', status == 1);

            // Reset and set languages
            $('.edit-language').prop('checked', false);
            if (languages) {
                languages.forEach(function(lang) {
                    $('.edit-language[value="' + lang + '"]').prop('checked', true);
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