@extends('admin.layouts.app')
@section('title', 'Permissions')
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
            <div class="card">
                <div class="card-header border-bottom">
                      <div class="d-flex justify-content-between align-items-center">
                        <h5 class="">Permissions</h5>
                        <div class="">
                        <button
                          type="button"
                          class="btn btn-primary"
                          data-bs-toggle="modal"
                          data-bs-target="#backDropModal"
                        >
                          Add Permission
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
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td>1</td>
                                    <td>new-leads</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-warning">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>upload-leads</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-warning">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>delete-leads</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-warning">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-outline-danger">
                                            <i class="bx bx-trash"></i>
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
@include('admin.partials.datatables-scripts')

<script>
    $(document).ready(function() {
        initCrmDataTable('#leadsTable');
    });
</script>
@endsection