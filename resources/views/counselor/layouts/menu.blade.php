<aside id="layout-menu" aria-label="Main navigation">
    <div class="app-brand">
        <a href="{{ url('/counselor/dashboard') }}" class="app-brand-link">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">IC</div>
            <span class="app-brand-text">Inline CRM</span>
        </a>
        <button type="button" class="text-slate-400 transition-colors hover:text-white lg:hidden" data-sidebar-toggle aria-label="Close menu">
            <i class="bx bx-x text-xl"></i>
        </button>
    </div>

    <ul class="menu-inner">
        <li class="menu-item {{ request()->is('counselor/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/counselor/dashboard') }}" class="menu-link">
                <i class="menu-icon bx bx-home-circle"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/leads-basket') ? 'active' : '' }}">
            <a href="{{ url('/counselor/leads-basket') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-basket"></i>
                    <span>Leads Basket</span>
                </span>
                <span class="badge bg-info" id="basket-leads-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/new-leads') ? 'active' : '' }}">
            <a href="{{ url('/counselor/new-leads') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-user-plus"></i>
                    <span>New Leads</span>
                </span>
                <span class="badge bg-primary" id="new-leads-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/followups/today') ? 'active' : '' }}">
            <a href="{{ url('/counselor/followups/today') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-calendar-check"></i>
                    <span>Today's Tasks</span>
                </span>
                <span class="badge bg-warning" id="today-followups-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/followups/tomorrow') ? 'active' : '' }}">
            <a href="{{ url('/counselor/followups/tomorrow') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-calendar"></i>
                    <span>Tomorrow's Tasks</span>
                </span>
                <span class="badge bg-info" id="tomorrow-followups-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/followups/pending') ? 'active' : '' }}">
            <a href="{{ url('/counselor/followups/pending') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-time-five"></i>
                    <span>Pending Tasks</span>
                </span>
                <span class="badge bg-danger" id="pending-followups-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/leads/bin') ? 'active' : '' }}">
            <a href="{{ url('/counselor/leads/bin') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-trash"></i>
                    <span>Bin Leads</span>
                </span>
                <span class="badge bg-danger" id="bin-leads-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/search') ? 'active' : '' }}">
            <a href="{{ url('/counselor/search') }}" class="menu-link">
                <i class="menu-icon bx bx-search"></i>
                <span>Search</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/student-fees*') ? 'active' : '' }}">
            <a href="{{ route('counselor.student-fees.index') }}" class="menu-link">
                <i class="menu-icon bx bx-wallet"></i>
                <span>Student Fees</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/student-fee-payments*') ? 'active' : '' }}">
            <a href="{{ route('counselor.student-fee-payments.index') }}" class="menu-link">
                <i class="menu-icon bx bx-credit-card"></i>
                <span>Student Fee Payments</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('counselor/reports*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bx-bar-chart-alt-2"></i>
                <span>Reports</span>
                <i class="bx bx-chevron-down ml-auto text-slate-500"></i>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('counselor/reports/leads') ? 'active' : '' }}">
                    <a href="{{ url('/counselor/reports/leads') }}" class="menu-link"><span>Leads</span></a>
                </li>
                <li class="menu-item {{ request()->is('counselor/reports/call-logs') ? 'active' : '' }}">
                    <a href="{{ url('/counselor/reports/call-logs') }}" class="menu-link"><span>Call Log</span></a>
                </li>
                <li class="menu-item {{ request()->is('counselor/reports/counselor-performance') ? 'active' : '' }}">
                    <a href="{{ url('/counselor/reports/counselor-performance') }}" class="menu-link"><span>Performance</span></a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('counselor/change-password') ? 'active' : '' }}">
            <a href="{{ url('/counselor/change-password') }}" class="menu-link">
                <i class="menu-icon bx bx-key"></i>
                <span>Change Password</span>
            </a>
        </li>
    </ul>

    <div class="logout-menu-item">
        <form method="POST" action="{{ route('counselor.logout') }}">
            @csrf
            <button type="submit">
                <i class="menu-icon bx bx-power-off"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
