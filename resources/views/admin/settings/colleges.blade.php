@extends('admin.layouts.app')
@section('title', 'Colleges')
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
                        <h5 class="">Colleges</h5>
                        <div class="">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            Add College
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
                                    <th>Place</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                               @foreach($colleges as $college)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $college->name }}</td>
                                    <td>{{ $college->place }}</td>
                                    <td>{{ $college->status }}</td>
                                    <td>
                                        <button type="button" 
                                            class="btn btn-icon btn-outline-warning edit-btn"
                                            data-id="{{ $college->id }}"
                                            data-name="{{ $college->name }}"
                                            data-place="{{ $college->place }}"
                                            data-status="{{ $college->status }}">
                                            <span class="tf-icons bx bx-edit"></span>
                                        </button>
                                        <form action="{{ route('admin.settings.colleges.destroy', $college->id) }}" 
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger"
                                                    onclick="return confirm('Are you sure?')">
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
        <form class="modal-content" method="POST" action="{{ route('admin.settings.colleges.store') }}" id="addForm">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add College</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Place</label>
                        <input type="text" name="place" class="form-control" required>
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
                <button type="submit" class="btn btn-primary d-none" id="submitBtn">
                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span>
                    <span class="btn-text">Submit</span>
                </button>
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
                <h5 class="modal-title">Edit College</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Place</label>
                        <input type="text" name="place" id="edit_place" class="form-control" required>
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
<!-- Include jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="{{ url('crm/js/common.js') }}"></script>
<script>
    $(document).ready(function() {
        initializeFormSubmission('#addForm');
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
          var place = $(this).data('place');
          var status = $(this).data('status');

          $('#edit_name').val(name);
          $('#edit_place').val(place);
          $('#edit_status').val(status);

          var url = "{{ route('admin.settings.colleges.update', ':id') }}";
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