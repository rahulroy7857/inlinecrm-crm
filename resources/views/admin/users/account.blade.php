@extends('admin.layouts.app')
@section('title', 'Account Users')
@section('style')
@include('admin.partials.datatables-head')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Account Portal Users</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bx bx-plus me-1"></i>Add Account User
            </button>
        </div>
        <div class="card-body mt-3">
            <div class="table-responsive">
                <table id="accountsTable" class="table crm-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $account->name }}</td>
                            <td>{{ $account->email }}</td>
                            <td>{{ $account->mobile ?? '—' }}</td>
                            <td class="text-capitalize">{{ $account->role }}</td>
                            <td>
                                <span class="badge bg-{{ $account->status ? 'success' : 'danger' }}">
                                    {{ $account->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-icon btn-outline-warning edit-btn"
                                    data-id="{{ $account->id }}"
                                    data-name="{{ $account->name }}"
                                    data-email="{{ $account->email }}"
                                    data-mobile="{{ $account->mobile }}"
                                    data-role="{{ $account->role }}"
                                    data-status="{{ $account->status }}">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <form action="{{ route('admin.users.account.destroy', $account->id) }}" method="POST" class="d-inline"
                                      data-confirm-delete="Delete this account user?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger"><i class="bx bx-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-muted small mt-3 mb-0">
                These users can sign in at <a href="{{ route('account.login') }}" target="_blank">{{ route('account.login') }}</a>
            </p>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('admin.users.account.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Account User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @include('admin.users.partials.account-form')
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Edit Account User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @include('admin.users.partials.account-form', ['prefix' => 'edit_', 'edit' => true])
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
$(function() {
    initCrmDataTable('#accountsTable');
    $('.edit-btn').on('click', function() {
        const d = $(this).data();
        $('#edit_name').val(d.name);
        $('#edit_email').val(d.email);
        $('#edit_mobile').val(d.mobile);
        $('#edit_role').val(d.role);
        $('#edit_status').prop('checked', d.status == 1);
        $('#editForm').attr('action', "{{ route('admin.users.account.update', ':id') }}".replace(':id', d.id));
        $('#editModal').modal('show');
    });
});
</script>
@endsection
