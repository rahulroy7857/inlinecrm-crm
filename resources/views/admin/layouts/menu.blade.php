<aside id="layout-menu" aria-label="Main navigation">
    <div class="app-brand">
        <a href="{{ url('/admin/dashboard') }}" class="app-brand-link">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">IC</div>
            <span class="app-brand-text">Inline CRM</span>
        </a>
        <button type="button" class="text-slate-400 transition-colors hover:text-white lg:hidden" data-sidebar-toggle aria-label="Close menu">
            <i class="bx bx-x text-xl"></i>
        </button>
    </div>

    <ul class="menu-inner">
        <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{ url('/admin/dashboard') }}" class="menu-link">
                <i class="menu-icon bx bx-home-circle"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/new-leads') ? 'active' : '' }}">
            <a href="{{ url('/admin/new-leads') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-user-plus"></i>
                    <span>New Leads</span>
                </span>
                <span class="badge bg-primary" id="new-leads-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/upload-leads') ? 'active' : '' }}">
            <a href="{{ url('/admin/upload-leads') }}" class="menu-link">
                <i class="menu-icon bx bx-upload"></i>
                <span>Upload Leads</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/followups/today') ? 'active' : '' }}">
            <a href="{{ url('/admin/followups/today') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-calendar-check"></i>
                    <span>Today's Tasks</span>
                </span>
                <span class="badge bg-warning" id="today-followups-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/followups/tomorrow') ? 'active' : '' }}">
            <a href="{{ url('/admin/followups/tomorrow') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-calendar"></i>
                    <span>Tomorrow's Tasks</span>
                </span>
                <span class="badge bg-info" id="tomorrow-followups-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/followups/pending') ? 'active' : '' }}">
            <a href="{{ url('/admin/followups/pending') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-time-five"></i>
                    <span>Pending Tasks</span>
                </span>
                <span class="badge bg-danger" id="pending-followups-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/leads/bin') ? 'active' : '' }}">
            <a href="{{ url('/admin/leads/bin') }}" class="menu-link justify-between">
                <span class="flex items-center gap-3">
                    <i class="menu-icon bx bx-trash"></i>
                    <span>Bin Leads</span>
                </span>
                <span class="badge bg-danger" id="bin-leads-count">0</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/search') ? 'active' : '' }}">
            <a href="{{ url('/admin/search') }}" class="menu-link">
                <i class="menu-icon bx bx-search"></i>
                <span>Search</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bx-user"></i>
                <span>Users</span>
                <i class="bx bx-chevron-down ml-auto text-slate-500"></i>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/users/admin') ? 'active' : '' }}">
                    <a href="{{ url('/admin/users/admin') }}" class="menu-link"><span>Admin</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/users/counselor') ? 'active' : '' }}">
                    <a href="{{ url('/admin/users/counselor') }}" class="menu-link"><span>Counselor</span></a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('admin/reports*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bx-bar-chart-alt-2"></i>
                <span>Reports</span>
                <i class="bx bx-chevron-down ml-auto text-slate-500"></i>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/reports/leads') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/leads') }}" class="menu-link"><span>Leads</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/pending-followups') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/pending-followups') }}" class="menu-link"><span>Pending Tasks</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/payments') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/payments') }}" class="menu-link"><span>Payments</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/call-logs') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/call-logs') }}" class="menu-link"><span>Call Log</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/counselor-performance') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/counselor-performance') }}" class="menu-link"><span>Counselor Performance</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/analytics') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/analytics') }}" class="menu-link"><span>Analytics</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/transfer') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/transfer') }}" class="menu-link"><span>Transfer</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/agent-commission') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/agent-commission') }}" class="menu-link"><span>Agent Commission</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/consolidated') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/consolidated') }}" class="menu-link"><span>Consolidated</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/picked-leads') ? 'active' : '' }}">
                    <a href="{{ url('/admin/reports/picked-leads') }}" class="menu-link"><span>Picked Leads</span></a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('admin/logs/activity') ? 'active' : '' }}">
            <a href="{{ url('/admin/logs/activity') }}" class="menu-link">
                <i class="menu-icon bx bx-file"></i>
                <span>Activity Logs</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/settings*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon bx bx-cog"></i>
                <span>Settings</span>
                <i class="bx bx-chevron-down ml-auto text-slate-500"></i>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/settings/colleges') ? 'active' : '' }}">
                    <a href="{{ url('/admin/settings/colleges') }}" class="menu-link"><span>Colleges</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/settings/courses') ? 'active' : '' }}">
                    <a href="{{ url('/admin/settings/courses') }}" class="menu-link"><span>Courses</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/settings/academic-years') ? 'active' : '' }}">
                    <a href="{{ url('/admin/settings/academic-years') }}" class="menu-link"><span>Academic Years</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/settings/sources') ? 'active' : '' }}">
                    <a href="{{ url('/admin/settings/sources') }}" class="menu-link"><span>Source</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/settings/holidays') ? 'active' : '' }}">
                    <a href="{{ url('/admin/settings/holidays') }}" class="menu-link"><span>Holidays</span></a>
                </li>
                <li class="menu-item {{ request()->is('admin/settings/agents') ? 'active' : '' }}">
                    <a href="{{ url('/admin/settings/agents') }}" class="menu-link"><span>Agents</span></a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('admin/change-password') ? 'active' : '' }}">
            <a href="{{ url('/admin/change-password') }}" class="menu-link">
                <i class="menu-icon bx bx-key"></i>
                <span>Change Password</span>
            </a>
        </li>
    </ul>

    <div class="logout-menu-item">
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">
                <i class="menu-icon bx bx-power-off"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
