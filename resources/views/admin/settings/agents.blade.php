@extends('admin.layouts.app')
@section('title', 'Agents')
@section('style')   
@include('admin.partials.datatables-head')
<style>
    table#agentsTable th, table#agentsTable td {
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
                        <h5 class="">Agents</h5>
                        <div class="">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                                Add Agent
                            </button>
                        </div>
                      </div>
                </div>
                <div class="card-body mt-3">
                    
                    <div class="table-modern-wrap">
                    <div class="table-responsive text-nowrap">
                        <table id="agentsTable" class="table crm-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agents as $agent)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $agent->name }}</td>
                                    <td>{{ $agent->company ?? 'N/A' }}</td>
                                    <td>{{ $agent->mobile }}</td>
                                    <td>{{ $agent->address ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $agent->status === 'Active' ? 'success' : 'danger' }}">
                                            {{ $agent->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-icon btn-outline-warning edit-btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editModal"
                                                data-id="{{ $agent->id }}"
                                                data-name="{{ $agent->name }}"
                                                data-company="{{ $agent->company }}"
                                                data-mobile="{{ $agent->mobile }}"
                                                data-address="{{ $agent->address }}"
                                                data-status="{{ $agent->status }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <form action="{{ route('admin.settings.agents.destroy', $agent->id) }}" 
                                              method="POST" 
                                              style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-icon btn-outline-danger"
                                                    data-confirm-delete="Are you sure you want to delete this agent?">
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
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.settings.agents.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3"></textarea>
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
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" id="editForm">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Edit Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" id="edit_company" class="form-control">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Mobile</label>
                        <input type="text" name="mobile" id="edit_mobile" class="form-control" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" id="edit_address" class="form-control" rows="3"></textarea>
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
        initCrmDataTable('#agentsTable');
        // Edit button click handler
        $('.edit-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const company = $(this).data('company');
            const mobile = $(this).data('mobile');
            const address = $(this).data('address');
            const status = $(this).data('status');

            $('#editForm').attr('action', `/admin/settings/agents/${id}`);
            $('#edit_name').val(name);
            $('#edit_company').val(company);
            $('#edit_mobile').val(mobile);
            $('#edit_address').val(address);
            $('#edit_status').val(status);
        });
    });
</script>
@endsection