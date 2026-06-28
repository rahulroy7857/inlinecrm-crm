<nav class="layout-navbar" id="layout-navbar">
    <div class="flex w-full items-center gap-4">
        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition-colors hover:bg-slate-50 lg:hidden" data-sidebar-toggle aria-label="Open menu">
            <i class="bx bx-menu text-xl"></i>
        </button>

        <form class="hidden flex-1 sm:flex sm:max-w-md" method="GET" action="{{ url('counselor/search') }}">
            <div class="relative w-full">
                <i class="bx bx-search absolute top-1/2 left-3 -translate-y-1/2 text-slate-400"></i>
                <input
                    type="text"
                    name="value"
                    class="form-control !pl-10"
                    placeholder="Search mobile number..."
                    aria-label="Search mobile number"
                    required
                />
                <input type="hidden" name="column_name" value="mobile" />
            </div>
            <button class="btn btn-primary ml-2 shrink-0" type="submit">
                <i class="bx bx-search"></i>
            </button>
        </form>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item d-none d-sm-block">
                <a href="#" data-bs-toggle="modal" data-bs-target="#academicYearModal" class="inline-flex items-center">
                    <span class="badge bg-primary cursor-pointer">
                        {{ session('academic_year_name', '-') }}
                    </span>
                </a>
            </li>

            <li class="nav-item position-relative d-none d-sm-block">
                <a class="nav-link flex h-10 w-10 items-center justify-center rounded-lg hover:bg-slate-100" href="{{ url('counselor/messages') }}">
                    <i class="bx bx-envelope fs-4"></i>
                    <span class="badge bg-danger position-absolute rounded-pill p-1" style="font-size: 0.65rem; top: 4px; right: 4px;">0</span>
                </a>
            </li>

            <li class="nav-item d-none d-sm-block">
                <a class="nav-link position-relative flex h-10 w-10 items-center justify-center rounded-lg hover:bg-slate-100" href="{{ url('counselor/notifications') }}">
                    <i class="bx bx-bell fs-4"></i>
                    <span class="badge bg-danger position-absolute rounded-pill p-1" style="font-size: 0.65rem; top: 4px; right: 4px;">0</span>
                </a>
            </li>

            <li class="nav-item navbar-dropdown dropdown">
                <a class="nav-link dropdown-toggle hide-arrow flex items-center gap-2 rounded-lg p-1 hover:bg-slate-100" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                    @include('admin.partials.user-avatar')
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="#">
                            <div class="d-flex align-items-center gap-3">
                                @include('admin.partials.user-avatar')
                                <div>
                                    <span class="fw-semibold d-block">{{ auth()->guard('counselor')->user()->name }}</span>
                                    <small class="text-muted">Counselor</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#academicYearModal">
                            <i class="bx bx-calendar me-2"></i>
                            <span>Academic Year</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('counselor.logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-power-off me-2"></i>
                            <span>Log Out</span>
                        </a>
                        <form id="logout-form" action="{{ route('counselor.logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
