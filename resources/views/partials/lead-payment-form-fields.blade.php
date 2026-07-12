@if(!empty($showLeadSelect))
<div class="col-12 mb-3">
    <label for="paymentLead" class="form-label">Lead *</label>
    <select id="paymentLead" class="form-select lead-search-select" name="lead_id" required
        data-search-url="{{ account_route('lead-payments.search-leads') }}"
        data-placeholder="Search by name, lead ID or mobile">
        @if(!empty($selectedLeadOption))
        <option value="{{ $selectedLeadOption->id }}" selected>
            {{ $selectedLeadOption->lead_id }} — {{ $selectedLeadOption->name }}@if($selectedLeadOption->mobile) ({{ $selectedLeadOption->mobile }})@endif
        </option>
        @elseif(old('lead_id'))
        <option value="{{ old('lead_id') }}" selected>Selected lead</option>
        @endif
    </select>
</div>
@endif

<div class="col-12 mb-3">
    <label for="paymentDate" class="form-label">Payment Date</label>
    <input
        type="date"
        id="paymentDate"
        name="payment_date"
        class="form-control"
        placeholder="Select Payment Date"
        onfocus="this.showPicker()"
        max="{{ date('Y-m-d') }}"
        value="{{ old('payment_date', date('Y-m-d')) }}"
        required
    />
</div>
<div class="col-12 mb-3">
    <label for="transactionType" class="form-label">Transaction Type</label>
    <select id="transactionType" class="form-select" name="transaction_type" required>
        @foreach(transaction_types() as $value => $label)
        <option value="{{ $value }}" {{ (string) old('transaction_type', '1') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 mb-3">
    <label for="type" class="form-label">Type</label>
    <select id="type" class="form-select" name="payment_type" required>
        @foreach(['Application Fee', 'Reservation Fee', 'Admission Fee', 'Commission', 'Refund', 'Tuition Fee', 'Other'] as $paymentType)
        <option value="{{ $paymentType }}" {{ old('payment_type') === $paymentType ? 'selected' : '' }}>{{ $paymentType }}</option>
        @endforeach
    </select>
</div>
<div class="col-12 mb-3">
    <label for="paymentMode" class="form-label">Payment Mode</label>
    <select id="paymentMode" class="form-select" name="payment_mode" required>
        @foreach(['Cash', 'Card', 'UPI', 'Bank Transfer', 'Cheque', 'RazorPay', 'Other'] as $mode)
        <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
        @endforeach
    </select>
</div>
@if(!empty($ledgerAccounts) && $ledgerAccounts->count())
<div class="col-12 mb-3">
    <label for="ledgerAccount" class="form-label">Bank / Cash Account{{ !empty($requireLedgerAccount) ? ' *' : '' }}</label>
    <select id="ledgerAccount" class="form-select account-search-select" name="ledger_account_id" {{ !empty($requireLedgerAccount) ? 'required' : '' }}
        data-placeholder="Search bank or cash account">
        @if(empty($requireLedgerAccount))
        <option value="">— Select account (optional) —</option>
        @else
        <option value="">Select account</option>
        @endif
        @foreach($ledgerAccounts as $acc)
        <option value="{{ $acc->id }}" {{ (string) old('ledger_account_id') === (string) $acc->id ? 'selected' : '' }}>
            {{ $acc->name }} ({{ ucfirst($acc->type) }})@if($acc->type === 'bank' && $acc->bank_name) — {{ $acc->bank_name }}@endif
        </option>
        @endforeach
    </select>
</div>
@endif
<div class="col-12 mb-3">
    <label for="paymentAmount" class="form-label">Amount</label>
    <input
        type="number"
        step="0.01"
        min="0.01"
        id="paymentAmount"
        class="form-control"
        name="amount"
        placeholder="Enter Payment Amount"
        value="{{ old('amount') }}"
        required
    />
</div>
<div class="col-12 mb-3">
    <label for="paymentRemark" class="form-label">Remark</label>
    <textarea
        id="paymentRemark"
        class="form-control"
        rows="3"
        placeholder="Enter Remark"
        name="remarks"
    >{{ old('remarks') }}</textarea>
</div>
