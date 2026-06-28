@extends('admin.layouts.app')
@section('title', 'Academic Years')
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#academicYearTable th, table#academicYearTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
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
                <h5>Academic Years</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    Add Academic Year
                </button>
            </div>
        </div>
        <div class="card-body mt-3">
            <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                <table id="academicYearTable" class="table crm-table">
                    <thead>
                        <tr>
                            <th>SL.No</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach($academicYears as $year)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $year->name }}</td>
                            <td>
                                <span class="badge bg-{{ $year->is_active ? 'success' : 'secondary' }}">
                                    {{ $year->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button type="button" 
                                    class="btn btn-icon btn-outline-warning edit-yr-btn"
                                    data-id="{{ $year->id }}"
                                    data-name="{{ $year->name }}"
                                    data-active="{{ $year->is_active }}">
                                    <i class="bx bx-edit"></i>
                                </button>
                                @if(!$year->is_active)
                                <!-- <form action="{{ route('admin.settings.academic-years.destroy', $year->id) }}" 
                                    method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger"
                                            onclick="return confirm('Are you sure?')">
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.settings.academic-years.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name (e.g., 2025-26)</label>
                        <input type="text" name="name" class="form-control" required 
                               pattern="\d{4}-\d{2}" title="Format: YYYY-YY">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input">
                            <label class="form-check-label">Set as Active Year</label>
                        </div>
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
                <h5 class="modal-title">Edit Academic Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required
                               pattern="\d{4}-\d{2}" title="Format: YYYY-YY">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" id="edit_is_active" class="form-check-input">
                            <label class="form-check-label">Set as Active Year</label>
                        </div>
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
        initCrmDataTable('#academicYearTable');

        // Edit button click handler
         $('.edit-yr-btn').click(function() {
          $('#editModal').modal('show');
            var id = $(this).data('id');
            var name = $(this).data('name');
            var isActive = $(this).data('active');

            $('#edit_name').val(name);
            $('#edit_is_active').prop('checked', isActive == 1);
            $('#editForm').attr('action', `/admin/settings/academic-years/${id}`);
            // Show modal
          $('#editModal').modal('show');
        });

        // Clear form when modal is closed
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('form')[0].reset();
        });
    });
</script>
@endsection