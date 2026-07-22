@extends('account.layouts.portal')
@section('title', 'New Transaction')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Transaction Entry</h5>
            <a href="{{ account_route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ account_route('transactions.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date *</label>
                        <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Account *</label>
                        <select name="ledger_account_id" class="form-control" required>
                            <option value="">Select account</option>
                            @foreach($ledgerAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('ledger_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }} ({{ $acc->type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category *</label>
                        <select name="category" id="category" class="form-control" required>
                            <option value="income" {{ old('category') === 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ old('category') === 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="transfer" {{ old('category') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                            <option value="refund" {{ old('category') === 'refund' ? 'selected' : '' }}>Refund</option>
                            <option value="forwarding_fee" {{ old('category') === 'forwarding_fee' ? 'selected' : '' }}>Forwarding fee</option>
                            <option value="other" {{ old('category', 'other') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4" id="entryTypeWrap">
                        <label class="form-label">Entry Type *</label>
                        <select name="entry_type" id="entry_type" class="form-control" required>
                            <option value="credit" {{ old('entry_type') === 'credit' ? 'selected' : '' }}>Credit (Receipt / increases balance)</option>
                            <option value="debit" {{ old('entry_type', 'debit') === 'debit' ? 'selected' : '' }}>Debit (Payment / reduces balance)</option>
                        </select>
                        <small class="text-muted">Income auto-credits; Expense auto-debits.</small>
                    </div>
                    <div class="col-md-4" id="toAccountWrap" style="display:none">
                        <label class="form-label">Transfer To Account *</label>
                        <select name="to_ledger_account_id" class="form-control">
                            <option value="">Select target account</option>
                            @foreach($ledgerAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('to_ledger_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control">
                            <option value="">—</option>
                            @foreach(['Cash','Card','UPI','Bank Transfer','Cheque','Other'] as $mode)
                            <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Party Name</label>
                        <select name="party_name" id="party_name" class="form-select party-search-select"
                            data-search-url="{{ account_route('transactions.search-party-students') }}"
                            data-placeholder="Search student or type party name">
                            <option value=""></option>
                            @if(old('party_name'))
                            <option value="{{ old('party_name') }}" selected>{{ old('party_name') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reference No.</label>
                        <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                    <div class="col-12 pt-4">
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$('#category').on('change', function() {
    const category = $(this).val();
    const isTransfer = category === 'transfer';
    $('#toAccountWrap').toggle(isTransfer);
    $('#entryTypeWrap').toggle(!isTransfer);

    if (category === 'income' || category === 'forwarding_fee') {
        $('#entry_type').val('credit');
    } else if (category === 'expense' || category === 'refund') {
        $('#entry_type').val('debit');
    }
}).trigger('change');

$('#party_name').select2({
    placeholder: $('#party_name').data('placeholder') || 'Search student or type party name',
    allowClear: true,
    width: '100%',
    tags: true,
    minimumInputLength: 0,
    ajax: {
        url: $('#party_name').data('search-url'),
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term || '' }),
        processResults: data => data,
        cache: true,
    },
    templateSelection: data => data.id || data.text,
    createTag: function(params) {
        const term = $.trim(params.term || '');
        if (!term) return null;
        return { id: term, text: term, newTag: true };
    },
});

$(document).on('select2:open', () => {
    const field = document.querySelector('.select2-container--open .select2-search__field');
    if (field) field.focus();
});
</script>
@endsection
