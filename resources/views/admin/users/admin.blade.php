@extends('admin.layouts.app')
@section('title', 'Admin')
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#leadsTable th, table#leadsTable td {
        border-top: 1px solid #dee2e6 !important;
    }
    .badge {
        padding: 0.5em 0.75em;
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
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Admin</h5>
                        <div class="">
                        <button type="button" 
                                class="btn btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#addModal">
                            <i class="bx bx-plus me-1"></i>
                            Add Admin
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
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($admins as $admin)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $admin->name }}</td>
                                    <td>{{ $admin->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $admin->status ? 'success' : 'danger' }}">
                                            {{ $admin->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-icon btn-outline-warning edit-btn"
                                                data-id="{{ $admin->id }}"
                                                data-name="{{ $admin->name }}"
                                                data-email="{{ $admin->email }}"
                                                data-status="{{ $admin->status }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        @if($admin->id !== auth()->guard('admin')->id())
                                        <!-- <form action="{{ route('admin.users.admin.destroy', $admin->id) }}" 
                                              method="POST" 
                                              style="display:inline;"
                                              onsubmit="return confirm('Are you sure you want to delete this admin?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form> -->
                                        @endif
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
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.users.admin.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Admin</h5>
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
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
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
                <h5 class="modal-title">Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
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
@include('admin.partials.datatables-scripts')

<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
        $(document).on('click', '.edit-btn', function() {
          var id = $(this).data('id');
          var name = $(this).data('name');
          var email = $(this).data('email');
          var status = $(this).data('status');

          $('#edit_name').val(name);
          $('#edit_email').val(email);
          $('#edit_status').prop('checked', status == 1);

          var url = "{{ route('admin.users.admin.update', ':id') }}";
          url = url.replace(':id', id);
          $('#editForm').attr('action', url);

          $('#editModal').modal('show');
      });
     
      // Clear form when modal is closed
      $('.modal').on('hidden.bs.modal', function() {
          $(this).find('form')[0].reset();
      });
    });
</script>
@endsection