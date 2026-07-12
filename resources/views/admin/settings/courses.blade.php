@extends('admin.layouts.app')
@section('title', 'Courses')
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#courseTable th, table#courseTable td {
        border-top: 1px solid #dee2e6 !important;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            @if(session('success'))
            <div class="alert alert-success">
            {{ session('success') }}
            </div>
          @endif
          @if(session('error'))
              <div class="alert alert-danger">
              {{ session('error') }}
              </div>
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
                        <h5 class="">Courses</h5>
                        <div class="">
                          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                              Add Course
                          </button>
                        </div>
                      </div>
                </div>
                <div class="card-body mt-3">
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="courseTable" class="table crm-table">
                            <thead>
                                <tr>
                                  <th>SL.No</th>
                                  <th>Name</th>
                                  <!-- <th>Code</th> -->
                                  <th>Status</th>
                                  <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                              @foreach($courses as $course)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $course->name }}</td>
                                    <!-- <td>{{ $course->code }}</td> -->
                                    <td>{{ $course->status }}</td>
                                    <td>
                                        <button type="button" 
                                            class="btn btn-icon btn-outline-warning edit-btn"
                                            data-id="{{ $course->id }}"
                                            data-name="{{ $course->name }}"
                                            data-status="{{ $course->status }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.courses.destroy', $course->id) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger"
                                                    data-confirm-delete="Are you sure you want to delete this course?">
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
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.settings.courses.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
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
                <h5 class="modal-title">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
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
        initCrmDataTable('#courseTable');
        // Edit button click handler
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var status = $(this).data('status');

            $('#edit_name').val(name);
            $('#edit_status').val(status);

            var url = "{{ route('admin.settings.courses.update', ':id') }}";
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