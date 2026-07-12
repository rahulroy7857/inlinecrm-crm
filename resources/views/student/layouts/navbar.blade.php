<nav class="layout-navbar" id="layout-navbar">
    <div class="flex w-full items-center gap-4">
        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition-colors hover:bg-slate-50 lg:hidden" data-sidebar-toggle aria-label="Open menu">
            <i class="bx bx-menu text-xl"></i>
        </button>

        <div class="hidden flex-1 sm:block">
            <span class="text-sm text-slate-500">Lead ID:</span>
            <span class="badge bg-primary ms-1">{{ auth()->guard('student')->user()->lead_ref ?? 'N/A' }}</span>
        </div>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item navbar-dropdown dropdown">
                <a class="nav-link dropdown-toggle hide-arrow flex items-center gap-2 rounded-lg p-1 hover:bg-slate-100" href="javascript:void(0);" data-bs-toggle="dropdown">
                    @include('admin.partials.user-avatar')
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex align-items-center gap-3">
                                @include('admin.partials.user-avatar')
                                <div>
                                    <span class="fw-semibold d-block">{{ auth()->guard('student')->user()->name }}</span>
                                    <small class="text-muted">{{ auth()->guard('student')->user()->email }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('student.profile.index') }}">
                            <i class="bx bx-user me-2"></i> My Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('student.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('student-logout-form').submit();">
                            <i class="bx bx-power-off me-2"></i> Log Out
                        </a>
                        <form id="student-logout-form" action="{{ route('student.logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
