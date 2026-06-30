<aside id="layout-menu" aria-label="Main navigation">
    <div class="app-brand">
        <a href="{{ route('account.dashboard') }}" class="app-brand-link">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">IC</div>
            <span class="app-brand-text">Accounts</span>
        </a>
        <button type="button" class="text-slate-400 transition-colors hover:text-white lg:hidden" data-sidebar-toggle aria-label="Close menu">
            <i class="bx bx-x text-xl"></i>
        </button>
    </div>

    <ul class="menu-inner">
        <li class="menu-item {{ request()->is('account/dashboard') ? 'active' : '' }}">
            <a href="{{ route('account.dashboard') }}" class="menu-link">
                <i class="menu-icon bx bx-home-circle"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('account/ledger-accounts*') ? 'active' : '' }}">
            <a href="{{ route('account.ledger-accounts.index') }}" class="menu-link">
                <i class="menu-icon bx bx-wallet"></i>
                <span>Bank & Cash Accounts</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('account/transactions*') ? 'active' : '' }}">
            <a href="{{ route('account.transactions.index') }}" class="menu-link">
                <i class="menu-icon bx bx-transfer"></i>
                <span>Transactions</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('account/daybook*') ? 'active' : '' }}">
            <a href="{{ route('account.daybook.index') }}" class="menu-link">
                <i class="menu-icon bx bx-book-open"></i>
                <span>Daybook</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('account/profit-loss*') ? 'active' : '' }}">
            <a href="{{ route('account.profit-loss.index') }}" class="menu-link">
                <i class="menu-icon bx bx-line-chart"></i>
                <span>Profit & Loss</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('account/crm-sync*') ? 'active' : '' }}">
            <a href="{{ route('account.crm-sync.index') }}" class="menu-link">
                <i class="menu-icon bx bx-sync"></i>
                <span>CRM Payment Sync</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('account/reports*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bx-bar-chart-alt-2"></i>
                <span>Reports</span>
                <i class="bx bx-chevron-down ml-auto text-slate-500"></i>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('account/reports') && !request()->is('account/reports/*') ? 'active' : '' }}">
                    <a href="{{ route('account.reports.index') }}" class="menu-link"><span>Overview</span></a>
                </li>
                <li class="menu-item {{ request()->is('account/reports/account-statement') ? 'active' : '' }}">
                    <a href="{{ route('account.reports.account-statement') }}" class="menu-link"><span>Account Statement</span></a>
                </li>
                <li class="menu-item {{ request()->is('account/reports/cash-flow') ? 'active' : '' }}">
                    <a href="{{ route('account.reports.cash-flow') }}" class="menu-link"><span>Cash Flow</span></a>
                </li>
                <li class="menu-item {{ request()->is('account/reports/ledger-summary') ? 'active' : '' }}">
                    <a href="{{ route('account.reports.ledger-summary') }}" class="menu-link"><span>Ledger Summary</span></a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('account/change-password') ? 'active' : '' }}">
            <a href="{{ route('account.change-password') }}" class="menu-link">
                <i class="menu-icon bx bx-key"></i>
                <span>Change Password</span>
            </a>
        </li>
    </ul>

    <div class="logout-menu-item">
        <form method="POST" action="{{ route('account.logout') }}">
            @csrf
            <button type="submit">
                <i class="menu-icon bx bx-power-off"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
