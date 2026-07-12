@extends('account.layouts.portal')
@section('title', 'Lead Payments')
@section('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container { width: 100% !important; }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    .modal .select2-container { z-index: 9999; }
    .select2-dropdown { z-index: 10000; }
</style>
@endsection
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page">
    @include('account.partials.alerts')

    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Lead Payments</h5>
            @if(account_can_manage())
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Add Payment</button>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table crm-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Lead</th>
                            <th>Transaction Type</th>
                            <th>Payment Type</th>
                            <th>Mode</th>
                            <th>Bank / Cash Account</th>
                            <th>Remark</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d-m-Y') }}</td>
                            <td>
                                {{ $payment->lead?->lead_id ?? '—' }}<br>
                                <small class="text-muted">{{ $payment->lead?->name }}</small>
                            </td>
                            <td>{{ transaction_types($payment->transaction_type) }}</td>
                            <td>{{ $payment->payment_type }}</td>
                            <td>{{ $payment->payment_mode }}</td>
                            <td>{{ $payment->accountTransaction?->ledgerAccount?->name ?? '—' }}</td>
                            <td>{{ $payment->remark }}</td>
                            <td class="text-end">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $payments->links() }}
        </div>
    </div>
</div>

@if(account_can_manage())
<div class="modal fade" id="paymentModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ account_route('lead-payments.store') }}">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Add Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @include('partials.lead-payment-form-fields', [
                        'showLeadSelect' => true,
                        'selectedLeadOption' => old('lead_id') ? \App\Models\Lead::select('id', 'name', 'lead_id', 'mobile')->find(old('lead_id')) : null,
                        'ledgerAccounts' => $ledgerAccounts,
                        'requireLedgerAccount' => true,
                    ])
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
function initLeadSearchSelect($el) {
    const url = $el.data('search-url');
    const placeholder = $el.data('placeholder') || 'Search lead';

    $el.select2({
        placeholder,
        allowClear: true,
        width: '100%',
        dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $(document.body),
        ajax: {
            url,
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term || '' }),
            processResults: data => data,
            cache: true,
        },
        minimumInputLength: 0,
    });
}

function initAccountSearchSelect($el) {
    $el.select2({
        placeholder: $el.data('placeholder') || 'Search account',
        allowClear: !$el.prop('required'),
        width: '100%',
        dropdownParent: $el.closest('.modal').length ? $el.closest('.modal') : $(document.body),
    });
}

$(function() {
    $('.lead-search-select').each(function() {
        initLeadSearchSelect($(this));
    });

    $('.account-search-select').each(function() {
        initAccountSearchSelect($(this));
    });

    @if(request('add') || $errors->any())
    new bootstrap.Modal('#paymentModal').show();
    @endif

    $(document).on('select2:open', () => {
        const field = document.querySelector('.select2-container--open .select2-search__field');
        if (field) field.focus();
    });
});
</script>
@endsection
