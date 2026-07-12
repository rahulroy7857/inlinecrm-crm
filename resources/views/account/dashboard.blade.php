@extends('account.layouts.portal')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page portal-dashboard portal-dashboard--account">
    <div class="portal-dashboard-header mb-4">
        <h1>Welcome, {{ account_user_name() }}!</h1>
        <p>Financial overview for {{ session('academic_year_name', 'all years') }}</p>
    </div>

    @if(!empty($workingHours))
        @include('partials.portal-working-hours-panel', ['workingHours' => $workingHours])
    @endif

    <div class="stats-grid portal-stats-grid portal-stats-grid--account mb-4">
        <div class="portal-stat-card portal-stat-card--balance">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-wallet"></i></div>
                <div class="card-title">Total Balance</div>
                <h3>₹{{ number_format($totalBalance, 2) }}</h3>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--income">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-trending-up"></i></div>
                <div class="card-title">Total Income</div>
                <h3>₹{{ number_format($totalIncome, 2) }}</h3>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--expense">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-trending-down"></i></div>
                <div class="card-title">Total Expense</div>
                <h3>₹{{ number_format($totalExpense, 2) }}</h3>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--entries">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-book-open"></i></div>
                <div class="card-title">Today's Entries</div>
                <h3>{{ $todayTransactions }}</h3>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="portal-chart-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5><span class="chart-icon chart-icon--revenue"><i class="bx bx-bar-chart-alt-2"></i></span> Income vs Expense {{ date('Y') }}</h5>
                    <span class="conversion-pill"><i class="bx bx-wallet"></i> Net ₹{{ number_format($netProfit, 2) }}</span>
                </div>
                <div class="card-body"><div id="incomeExpenseChart" class="chart-host"></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--status"><i class="bx bx-pie-chart-alt-2"></i></span> Account Balances</h5>
                </div>
                <div class="card-body"><div id="accountBalanceChart" class="chart-host chart-host--tall"></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="portal-chart-card portal-list-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--list"><i class="bx bx-list-ul"></i></span> Account Balances</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled m-0 d-flex flex-column gap-2">
                        @forelse($ledgerAccounts as $account)
                            <li class="list-item d-flex align-items-center justify-content-between gap-2">
                                <div>
                                    <strong>{{ $account->name }}</strong>
                                    <small class="d-block text-muted text-capitalize">{{ $account->type }}</small>
                                </div>
                                <strong>₹{{ number_format($account->current_balance, 2) }}</strong>
                            </li>
                        @empty
                            <li class="text-muted text-center py-4">No accounts yet. <a href="{{ account_route('ledger-accounts.index') }}">Add one</a></li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="portal-chart-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><span class="chart-icon chart-icon--transactions"><i class="bx bx-transfer"></i></span> Recent Transactions</h5>
                    @if(account_can_manage())
                        <a href="{{ account_route('lead-payments.index', ['add' => 1]) }}" class="btn btn-sm btn-primary">Add Payment</a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table crm-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $txn)
                                    <tr>
                                        <td>{{ $txn->transaction_date->format('d M Y') }}</td>
                                        <td>{{ $txn->ledgerAccount->name }}</td>
                                        <td>
                                            <span class="badge bg-label-{{ $txn->entry_type === 'credit' ? 'success' : 'danger' }} text-capitalize">{{ $txn->entry_type }}</span>
                                        </td>
                                        <td class="text-end">₹{{ number_format($txn->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No transactions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof ApexCharts === 'undefined') return;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const baseChart = { fontFamily: '"Golos Text", sans-serif', toolbar: { show: false }, animations: { enabled: true, speed: 700 } };
    const noData = { text: 'No data available', align: 'center', verticalAlign: 'middle', style: { color: '#94a3b8' } };
    const inr = (v) => '₹' + Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const incomeExpenseEl = document.querySelector('#incomeExpenseChart');
    if (incomeExpenseEl) {
        new ApexCharts(incomeExpenseEl, {
            series: [
                { name: 'Income', data: @json(array_values($monthlyIncomeData)) },
                { name: 'Expense', data: @json(array_values($monthlyExpenseData)) },
            ],
            chart: { ...baseChart, type: 'bar', height: 320 },
            plotOptions: { bar: { columnWidth: '42%', borderRadius: 8 } },
            colors: ['#11998e', '#f953c6'],
            fill: { type: 'gradient', gradient: { shade: 'light', opacityFrom: 0.95, opacityTo: 0.55 } },
            xaxis: { categories: months },
            yaxis: { labels: { formatter: (v) => '₹' + Number(v).toLocaleString('en-IN') } },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: { y: { formatter: inr } },
            noData,
        }).render();
    }

    const balanceEl = document.querySelector('#accountBalanceChart');
    if (balanceEl) {
        const labels = @json($accountBalanceLabels);
        const values = @json($accountBalanceValues);
        new ApexCharts(balanceEl, {
            series: values.length ? values : [1],
            chart: { ...baseChart, type: 'polarArea', height: 380 },
            labels: labels.length ? labels : ['No accounts'],
            colors: ['#667eea', '#4facfe', '#11998e', '#f093fb', '#ffd200', '#f5576c'],
            fill: { opacity: 0.85 },
            stroke: { width: 1, colors: ['#fff'] },
            legend: { position: 'bottom' },
            yaxis: { show: false },
            tooltip: { y: { formatter: inr } },
            noData,
        }).render();
    }
});
</script>

@if(!empty($workingHours))
@include('partials.portal-working-hours-scripts', [
    'statusUrl' => route('account.working-hours.status'),
    'startBreakUrl' => route('account.working-hours.break.start'),
    'endBreakUrl' => route('account.working-hours.break.end'),
    'loginUrl' => route('account.login'),
    'workingHours' => $workingHours,
])
@endif
@endsection
