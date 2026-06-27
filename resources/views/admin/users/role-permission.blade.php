@extends('admin.layouts.app')
@section('title', 'Role & Permission')
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
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Role & Permission</h5>
                        <div class="">
                        <button
                          type="button"
                          class="btn btn-primary"
                          data-bs-toggle="modal"
                          data-bs-target="#backDropModal"
                        >
                          Add
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
                                    <th>Role</th>
                                    <th>Permissions</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td>1</td>
                                    <td>Super Admin</td>
                                    <td><span class="badge rounded-pill bg-label-secondary">create-lead</span><span class="badge rounded-pill bg-label-secondary">edit-leads</span></td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-warning">
                                            <span class="tf-icons bx bx-edit"></span>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger">
                                            <span class="tf-icons bx bx-trash"></span>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Admin</td>
                                    <td><span class="badge rounded-pill bg-label-secondary">create-lead</span><span class="badge rounded-pill bg-label-secondary">edit-leads</span></td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-warning">
                                            <span class="tf-icons bx bx-edit"></span>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger">
                                            <span class="tf-icons bx bx-trash"></span>
                                        </button>
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>    
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="backDropModal" data-bs-backdrop="static" tabindex="-1">
                          <div class="modal-dialog">
                            <form class="modal-content">
                              <div class="modal-header border-bottom">
                                <h5 class="modal-title" id="backDropModalTitle">Add Permission</h5>
                                <button
                                  type="button"
                                  class="btn-close"
                                  data-bs-dismiss="modal"
                                  aria-label="Close"
                                ></button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col-12 mb-3">
                                    <label for="nameBackdrop" class="form-label">Name</label>
                                    <input
                                      type="text"
                                      id="nameBackdrop"
                                      class="form-control"
                                      placeholder="Enter Name"
                                    />
                                  </div>
                                </div>
                                
                              </div>
                              <div class="modal-footer border-top">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                  Close
                                </button>
                                <button type="button" class="btn btn-primary">Save</button>
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
    });
</script>
@endsection