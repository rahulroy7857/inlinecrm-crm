@extends('admin.layouts.app')
@section('title', 'Holidays')
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
                        <h5 class="">Holidays</h5>
                        <div class="">
                        <button
                          type="button"
                          class="btn btn-primary"
                          data-bs-toggle="modal"
                          data-bs-target="#backDropModal"
                        >
                          Add Holiday
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
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @foreach($holidays as $holiday)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $holiday->title }}</td>
                                    <td>{{ $holiday->holiday_date->format('d M Y') }}</td>
                                    <td>{{ $holiday->status }}</td>
                                    <td>
                                        <button type="button" 
                                            class="btn btn-icon btn-outline-warning edit-btn" 
                                            data-id="{{ $holiday->id }}"
                                            data-title="{{ $holiday->title }}"
                                            data-date="{{ $holiday->holiday_date->format('Y-m-d') }}"
                                            data-status="{{ $holiday->status }}"
                                            data-description="{{ $holiday->description ?? '' }}">
                                            <span class="tf-icons bx bx-edit"></span>
                                        </button>
                                        <form action="{{ route('admin.settings.holidays.destroy', $holiday->id) }}" 
                                            method="POST" 
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-icon btn-outline-danger" 
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

<!-- Add Holiday Modal -->
<div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.settings.holidays.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="holiday_date" onfocus="this.showPicker()" style="cursor: pointer; background-color: #fff;" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
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
<!-- Edit Holiday Modal -->
<div class="modal fade" id="editModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Edit Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="edit_title" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" 
                               name="holiday_date" 
                               id="edit_holiday_date" 
                               class="form-control" 
                               onfocus="this.showPicker()" 
                               style="cursor: pointer; background-color: #fff;" 
                               required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
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
        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            const title = $(this).data('title');
            const date = $(this).data('date');
            const status = $(this).data('status');
            const description = $(this).data('description');

            // Set form action
            $('#editForm').attr('action', `/admin/settings/holidays/${id}`);

            // Populate form fields
            $('#edit_title').val(title);
            $('#edit_holiday_date').val(date);
            $('#edit_status').val(status);
            $('#edit_description').val(description);

            // Show modal
            $('#editModal').modal('show');
        });

        // Clear form when modal is closed
        $('#editModal').on('hidden.bs.modal', function() {
            $('#editForm')[0].reset();
        });
    });
</script>
@endsection