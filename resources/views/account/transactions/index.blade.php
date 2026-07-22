@extends('account.layouts.portal')
@section('title', 'Transactions')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page account-transactions-page">
    @include('account.partials.alerts')

    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h4 class="mb-1">Transactions</h4>
            <p class="text-muted mb-0">Income increases bank balance. Expense reduces bank balance.</p>
        </div>
        @if(account_can_manage())
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ account_route('transactions.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>Add Expense / Income
            </a>
            {{-- <a href="{{ account_route('lead-payments.index', ['add' => 1]) }}" class="btn btn-outline-primary">
                <i class="bx bx-receipt me-1"></i>Add Lead Payment
            </a> --}}
        </div>
        @endif
    </div>

    <div class="account-txn-summary mb-4">
        <div class="account-txn-summary__item account-txn-summary__item--credit">
            <span class="account-txn-summary__label">Credits (Income)</span>
            <span class="account-txn-summary__value">+₹{{ number_format($totals['credit'] ?? 0, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--debit">
            <span class="account-txn-summary__label">Debits (Expense)</span>
            <span class="account-txn-summary__value">-₹{{ number_format($totals['debit'] ?? 0, 2) }}</span>
        </div>
        <div class="account-txn-summary__item account-txn-summary__item--net">
            <span class="account-txn-summary__label">Net</span>
            <span class="account-txn-summary__value">₹{{ number_format($totals['net'] ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="card mb-4 account-txn-filters">
        <div class="card-body">
            <form method="GET" class="account-filter-grid">
                <div class="account-filter-grid__field">
                    <label class="form-label">From</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="account-filter-grid__field">
                    <label class="form-label">To</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="account-filter-grid__field">
                    <label class="form-label">Account</label>
                    <select name="ledger_account_id" class="form-control">
                        <option value="">All accounts</option>
                        @foreach($ledgerAccounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('ledger_account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="account-filter-grid__field">
                    <label class="form-label">Type</label>
                    <select name="entry_type" class="form-control">
                        <option value="">All</option>
                        <option value="credit" {{ request('entry_type') === 'credit' ? 'selected' : '' }}>Credit</option>
                        <option value="debit" {{ request('entry_type') === 'debit' ? 'selected' : '' }}>Debit</option>
                    </select>
                </div>
                <div class="account-filter-grid__field">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="">All</option>
                        @foreach(['income' => 'Income', 'expense' => 'Expense', 'transfer' => 'Transfer', 'refund' => 'Refund', 'forwarding_fee' => 'Forwarding fee', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" {{ request('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="account-filter-grid__actions">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->hasAny(['from_date','to_date','ledger_account_id','entry_type','category']))
                        <a href="{{ account_route('transactions.index') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card account-txn-table-card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Transaction List</h5>
            <span class="text-muted small">{{ $transactions->total() }} records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table crm-table account-txn-table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Party / Details</th>
                            <th>Account</th>
                            <th>Category</th>
                            <th>Mode</th>
                            <th class="text-end">Amount</th>
                            <th>Source</th>
                            @if(account_can_manage())<th class="text-end">Action</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $txn)
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-semibold">{{ $txn->transaction_date->format('d M Y') }}</div>
                                <span class="badge bg-label-{{ $txn->entry_type === 'credit' ? 'success' : 'danger' }} text-uppercase">{{ $txn->entry_type }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $txn->party_name ?? '—' }}</div>
                                @if($txn->description)
                                    <div class="account-txn-desc">{{ $txn->description }}</div>
                                @endif
                                @if($txn->reference_no)
                                    <div class="text-muted small">Ref: {{ $txn->reference_no }}</div>
                                @endif
                            </td>
                            <td>{{ $txn->ledgerAccount->name }}</td>
                            <td><span class="text-capitalize">{{ str_replace('_', ' ', $txn->category) }}</span></td>
                            <td>{{ $txn->payment_mode ?? '—' }}</td>
                            <td class="text-end text-nowrap fw-semibold {{ $txn->entry_type === 'credit' ? 'text-success' : 'text-danger' }}">
                                {{ $txn->entry_type === 'credit' ? '+' : '-' }}₹{{ number_format($txn->amount, 2) }}
                            </td>
                            <td>
                                @if($txn->student_payment_id)
                                    <span class="badge bg-label-primary">Student Fee</span>
                                @elseif($txn->is_crm_synced)
                                    <span class="badge bg-label-info">CRM</span>
                                @else
                                    <span class="badge bg-label-secondary">Manual</span>
                                @endif
                            </td>
                            @if(account_can_manage())
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    @if(is_admin_account_portal())
                                    <a href="{{ account_route('transactions.edit', $txn->id) }}"
                                       class="btn btn-icon btn-outline-warning btn-sm"
                                       title="Edit">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    @endif
                                    @unless($txn->is_crm_synced)
                                    <form action="{{ account_route('transactions.destroy', $txn->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-icon btn-outline-danger btn-sm" data-confirm-delete="Delete this transaction?" title="Delete">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                    @elseif(!is_admin_account_portal())
                                        <span class="text-muted">—</span>
                                    @endunless
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ account_can_manage() ? 8 : 7 }}" class="text-center text-muted py-5">No transactions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer border-top">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>
@endsection
