@extends('account.layouts.portal')
@section('title', 'Bank & Cash Accounts')
@section('style')
@include('admin.partials.datatables-head')
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Bank & Cash Accounts</h5>
            @if(account_can_manage())
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">Add Account</button>
            @endif
        </div>
        <div class="card-body mt-3">
            <div class="table-responsive">
                <table id="accountsTable" class="table crm-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Bank / A/C No.</th>
                            <th>Opening</th>
                            <th>Current Balance</th>
                            <th>Status</th>
                            @if(account_can_manage())<th>Action</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $account->name }}</td>
                            <td class="text-capitalize">{{ $account->type }}</td>
                            <td>
                                @if($account->type === 'bank')
                                    {{ $account->bank_name }}<br><small class="text-muted">{{ $account->account_number }}</small>
                                @else — @endif
                            </td>
                            <td>₹{{ number_format($account->opening_balance, 2) }}</td>
                            <td>₹{{ number_format($account->current_balance, 2) }}</td>
                            <td>{{ $account->status }}</td>
                            @if(account_can_manage())
                            <td>
                                <button type="button" class="btn btn-icon btn-outline-warning edit-btn"
                                    data-id="{{ $account->id }}"
                                    data-name="{{ $account->name }}"
                                    data-type="{{ $account->type }}"
                                    data-account_number="{{ $account->account_number }}"
                                    data-bank_name="{{ $account->bank_name }}"
                                    data-ifsc_code="{{ $account->ifsc_code }}"
                                    data-opening_balance="{{ $account->opening_balance }}"
                                    data-status="{{ $account->status }}"
                                    data-description="{{ $account->description }}">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <form action="{{ account_route('ledger-accounts.destroy', $account->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger" data-confirm-delete="Delete this account?">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(account_can_manage())
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" action="{{ account_route('ledger-accounts.store') }}">
            @csrf
            <div class="modal-header"><h5 class="modal-title">Add Account</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('account.ledger-accounts._form')</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-header"><h5 class="modal-title">Edit Account</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">@include('account.ledger-accounts._form', ['prefix' => 'edit_'])</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
@include('admin.partials.datatables-scripts')
<script>
$(function() {
    $('#accountsTable').DataTable();
    $('.edit-btn').on('click', function() {
        const d = $(this).data();
        $('#editForm').attr('action', @json(account_route('ledger-accounts.update', ['id' => '__ID__'])).replace('__ID__', d.id));
        $('#edit_name').val(d.name);
        $('#edit_type').val(d.type);
        $('#edit_account_number').val(d.account_number);
        $('#edit_bank_name').val(d.bank_name);
        $('#edit_ifsc_code').val(d.ifsc_code);
        $('#edit_opening_balance').val(d.opening_balance);
        $('#edit_status').val(d.status);
        $('#edit_description').val(d.description);
        toggleBankFields('edit_');
        new bootstrap.Modal('#editModal').show();
    });
    $('#type, #edit_type').on('change', function() {
        toggleBankFields(this.id === 'edit_type' ? 'edit_' : '');
    });
});
function toggleBankFields(prefix) {
    const type = $('#' + prefix + 'type').val();
    $('.bank-fields-' + (prefix || 'add')).toggle(type === 'bank');
}
</script>
@endsection
