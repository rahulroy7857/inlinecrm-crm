<aside id="layout-menu" aria-label="Main navigation">
    <div class="app-brand">
        <a href="{{ route('student.dashboard') }}" class="app-brand-link">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">IC</div>
            <span class="app-brand-text">Student Portal</span>
        </a>
        <button type="button" class="text-slate-400 transition-colors hover:text-white lg:hidden" data-sidebar-toggle aria-label="Close menu">
            <i class="bx bx-x text-xl"></i>
        </button>
    </div>

    <ul class="menu-inner">
        <li class="menu-item {{ request()->is('student/dashboard') ? 'active' : '' }}">
            <a href="{{ route('student.dashboard') }}" class="menu-link">
                <i class="menu-icon bx bx-home-circle"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('student/profile*') ? 'active' : '' }}">
            <a href="{{ route('student.profile.index') }}" class="menu-link">
                <i class="menu-icon bx bx-user"></i>
                <span>Profile</span>
            </a>
        </li>

        <li class="menu-item {{ request()->is('student/payment*') ? 'active' : '' }}">
            <a href="{{ route('student.payment.index') }}" class="menu-link">
                <i class="menu-icon bx bx-credit-card"></i>
                <span>Payment</span>
            </a>
        </li>
    </ul>

    <div class="logout-menu-item">
        <form method="POST" action="{{ route('student.logout') }}">
            @csrf
            <button type="submit">
                <i class="menu-icon bx bx-power-off"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
