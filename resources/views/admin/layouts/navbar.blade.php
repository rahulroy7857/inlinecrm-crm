<nav
        class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
        id="layout-navbar"
        >
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
          <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)" id="menu-toggle">
          <i class="bx bx-menu bx-sm"></i>
          </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
          <!-- Nova Style Search -->
          <div class="navbar-nav align-items-center me-auto">
          <div class="nav-item d-flex align-items-center">
          <form class="d-flex" method="GET" action="{{ url('admin/search') }}">
            <input
              type="text"
              name="value"
              class="form-control border-1 shadow-none"
              placeholder="Search mobile number..."
              aria-label="Search mobile number..."
              required
            />
            <input type="hidden" name="column_name" value="mobile" />
            <button class="btn btn-primary ms-2 me-2" type="submit">
              <i class="bx bx-search fs-4"></i>
            </button>
          </form>
          </div>
          </div>
          <!-- /Nova Style Search -->

            <ul class="navbar-nav flex-row align-items-center ms-auto">
            <li class="nav-item d-none d-sm-block" style="padding-right: 5px;">
              <a class="dropdown-item" href="#" data-bs-toggle="modal"
            data-bs-target="#academicYearModal">
              <span class="badge bg-primary ms-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              {{ session('academic_year_name', '-') }}
              </span>
              </a>
            </li>
            <!-- Notifications -->
            <li class="nav-item position-relative d-none d-sm-block">
            <a class="nav-link" href="{{ url('admin/messages') }}">
              <i class="bx bx-envelope fs-4"></i>
              <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1" style="font-size: 0.75rem; transform: translate(-50%, -50%); top: 10px !important; left: 28px !important;background-color: red !important;">
              0
              </span>
            </a>
            </li>
            <li class="nav-item d-none d-sm-block" style="padding-right: 5px;">
            <a class="nav-link position-relative" href="{{ url('admin/notifications') }}">
              <i class="bx bx-bell fs-4"></i>
              <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle p-1" style="font-size: 0.75rem; transform: translate(-50%, -50%); top: 10px !important; left: 28px !important;background-color: red !important;">
              0
              </span>
            </a>
            </li>
            <!-- Current Academic Year Badge -->
            
          <!-- /Notifications -->

          <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="avatar avatar-online">
            <img src="{{url('crm/assets/img/avatars/1.png')}}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
            <li>
            <a class="dropdown-item" href="#">
            <div class="d-flex">
            <div class="flex-shrink-0 me-3">
            <div class="avatar avatar-online">
              <img src="{{url('crm/assets/img/avatars/1.png')}}" alt class="w-px-40 h-auto rounded-circle" />
            </div>
            </div>
            <div class="flex-grow-1">
            <span class="fw-semibold d-block">{{auth()->guard('admin')->user()->name}}</span>
            <small class="text-muted">Admin</small>
            </div>
            </div>
            </a>
            </li>
            <li>
            <div class="dropdown-divider"></div>
            </li>
            <li>
            <a class="dropdown-item" href="#" data-bs-toggle="modal"
            data-bs-target="#academicYearModal">
            <i class="bx bx-calendar me-2"></i>
            <span class="align-middle">Academic Year</span>
            </a>
            </li>
            <li>
            <a class="dropdown-item" href="{{ route('admin.logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bx bx-power-off me-2"></i>
            <span class="align-middle">Log Out</span>
            </a>
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
              @csrf
            </form>
            </li>
            </ul>
            </li>
          <!--/ User -->
          </ul>
        </div>
        </nav>