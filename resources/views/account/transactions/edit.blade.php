@extends('account.layouts.portal')
@section('title', 'Edit Transaction')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Transaction</h5>
            <a href="{{ account_route('transactions.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ account_route('transactions.update', $transaction->id) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Date *</label>
                        <input type="date" name="transaction_date" class="form-control"
                               value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Account *</label>
                        <select name="ledger_account_id" class="form-control" required>
                            <option value="">Select account</option>
                            @foreach($ledgerAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ (string) old('ledger_account_id', $transaction->ledger_account_id) === (string) $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }} ({{ $acc->type }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Category *</label>
                        <select name="category" id="category" class="form-control" required>
                            @foreach(['income' => 'Income', 'expense' => 'Expense', 'transfer' => 'Transfer', 'refund' => 'Refund', 'forwarding_fee' => 'Forwarding fee', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ old('category', $transaction->category) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4" id="entryTypeWrap">
                        <label class="form-label">Entry Type *</label>
                        <select name="entry_type" id="entry_type" class="form-control" required>
                            <option value="credit" {{ old('entry_type', $transaction->entry_type) === 'credit' ? 'selected' : '' }}>Credit (Receipt / increases balance)</option>
                            <option value="debit" {{ old('entry_type', $transaction->entry_type) === 'debit' ? 'selected' : '' }}>Debit (Payment / reduces balance)</option>
                        </select>
                        <small class="text-muted">Income auto-credits; Expense auto-debits.</small>
                    </div>
                    <div class="col-md-4" id="toAccountWrap" style="display:none">
                        <label class="form-label">Transfer To Account *</label>
                        <select name="to_ledger_account_id" class="form-control">
                            <option value="">Select target account</option>
                            @foreach($ledgerAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ (string) old('to_ledger_account_id', $transaction->to_ledger_account_id) === (string) $acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Amount *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                               value="{{ old('amount', $transaction->amount) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Mode</label>
                        <select name="payment_mode" class="form-control">
                            <option value="">—</option>
                            @foreach(['Cash','Card','UPI','Bank Transfer','Cheque','Other'] as $mode)
                            <option value="{{ $mode }}" {{ old('payment_mode', $transaction->payment_mode) === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Party Name</label>
                        <input type="text" name="party_name" class="form-control" value="{{ old('party_name', $transaction->party_name) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reference No.</label>
                        <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no', $transaction->reference_no) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $transaction->description) }}</textarea>
                    </div>
                    @if($transaction->is_crm_synced || $transaction->student_payment_id)
                    <div class="col-12">
                        <div class="alert alert-warning mb-0 py-2 small">
                            This transaction is linked to a student/CRM payment. Updating amount or date will also update the linked payment record.
                        </div>
                    </div>
                    @endif
                    <div class="col-12 pt-4">
                        <button type="submit" class="btn btn-primary">Update Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
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
</script>
@endsection
