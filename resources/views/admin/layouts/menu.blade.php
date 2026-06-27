<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <style>
            .layout-menu {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            }
            
            .menu-inner {
                background: transparent !important;
            }
            
            .menu-item .menu-link {
                color: white !important;
            }
            
            .menu-item .menu-link:hover {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: white !important;
            }
            
            .menu-item.active .menu-link {
                background-color: rgba(255, 255, 255, 0.2) !important;
                color: white !important;
            }
            
            .menu-icon {
                color: white !important;
            }
            
            .menu-text {
                color: white !important;
            }
            
            .app-brand-text {
                color: white !important;
            }
            
            .layout-menu-toggle {
                color: white !important;
            }
            
            .menu-sub {
                background-color: rgba(255, 255, 255, 0.1) !important;
            }
            
            .menu-sub .menu-link {
                color: white !important;
            }
            
            .menu-sub .menu-link:hover {
                background-color: rgba(255, 255, 255, 0.1) !important;
            }
            
            .menu-sub .menu-item.active .menu-link {
                background-color: rgba(255, 255, 255, 0.2) !important;
            }
            
            .logout-menu-item {
                background: rgba(255, 255, 255, 0.1) !important;
                border-top: 1px solid rgba(255, 255, 255, 0.2) !important;
            }
            
            .logout-menu-item button {
                color: white !important;
            }
            
            .logout-menu-item button:hover {
                background-color: rgba(255, 255, 255, 0.1) !important;
            }
            
            .badge {
                /*background-color: rgba(255, 255, 255, 0.2) !important;*/
                /*color: white !important;*/
            }
        </style>
        
        <div class="app-brand demo" style="background-color: #fff;">
        <a href="{{url('/admin/dashboard')}}" class="app-brand-link">
          <span class="app-brand-logo demo">
          <svg
            width="25"
            viewBox="0 0 25 42"
            version="1.1"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink"
          >
            <defs>
            <path
              d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
              id="path-1"
            ></path>
            <path
              d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
              id="path-3"
            ></path>
            <path
              d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
              id="path-4"
            ></path>
            <path
              d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
              id="path-5"
            ></path>
            </defs>
            <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
              <g id="Icon" transform="translate(27.000000, 15.000000)">
              <g id="Mask" transform="translate(0.000000, 8.000000)">
                <mask id="mask-2" fill="white">
                <use xlink:href="#path-1"></use>
                </mask>
                <use fill="#696cff" xlink:href="#path-1"></use>
                <g id="Path-3" mask="url(#mask-2)">
                <use fill="#696cff" xlink:href="#path-3"></use>
                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                </g>
                <g id="Path-4" mask="url(#mask-2)">
                <use fill="#696cff" xlink:href="#path-4"></use>
                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                </g>
              </g>
              <g
                id="Triangle"
                transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) "
              >
                <use fill="#696cff" xlink:href="#path-5"></use>
                <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
              </g>
              </g>
            </g>
            </g>
          </svg>
          </span>
          <span class="app-brand-text demo menu-text fw-bolder ms-2" style="color:#000 !important">CRM</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
          <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
        
          <!-- Dashboard -->
            <li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <a href="{{url('/admin/dashboard')}}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
            </li>
            <li class="menu-item {{ request()->is('admin/new-leads') ? 'active' : '' }}">
            <a href="{{url('/admin/new-leads')}}" class="menu-link d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
              <i class="menu-icon tf-icons bx bx-user"></i>
              <div data-i18n="Leads Pool" class="">New Leads</div>
              </div>
              <span class="badge bg-primary ms-2" id="new-leads-count">0</span>
            </a>
            </li>
            <li class="menu-item {{ request()->is('admin/upload-leads') ? 'active' : '' }}">
            <a href="{{url('/admin/upload-leads')}}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-upload"></i>
              <div data-i18n="Leads Pool">Upload Leads</div>
            </a>
            </li>

            
            <li class="menu-item {{ request()->is('admin/followups/today') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/today') }}" class="menu-link d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Todays Followups" class="">Today's Tasks</div>
              </div>
              <span class="badge bg-warning ms-2" id="today-followups-count">0</span>
              </a>
            </li>
            <li class="menu-item {{ request()->is('admin/followups/tomorrow') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/tomorrow') }}" class="menu-link d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Tomorrows Followups" class="">Tomorrow's Tasks</div>
              </div>
              <span class="badge bg-info ms-2" id="tomorrow-followups-count">0</span>
              </a>
            </li>
            <li class="menu-item {{ request()->is('admin/followups/pending') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/pending') }}" class="menu-link d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Pending Followups" class="">Pending Tasks</div>
              </div>
              <span class="badge bg-danger ms-2" id="pending-followups-count">0</span>
              </a>
            </li>
            <li class="menu-item {{ request()->is('admin/leads/bin') ? 'active' : '' }}">
              <a href="{{ url('/admin/leads/bin') }}" class="menu-link d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center">
                <i class="menu-icon tf-icons bx bx-trash"></i>
                <div data-i18n="Bin Leads" class="">Bin Leads</div>
              </div>
              <span class="badge bg-danger ms-2" id="bin-leads-count">0</span>
              </a>
            </li>
            <!-- <li class="menu-item {{ request()->is('admin/followups*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-envelope"></i>
              <div data-i18n="Reports">Mail</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ request()->is('admin/followups/pending') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/pending') }}" class="menu-link">
                <div data-i18n="Account">Inbox</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/followups/today') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/today') }}" class="menu-link">
                <div data-i18n="Notifications">Sent</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/followups/tomorrow') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/tomorrow') }}" class="menu-link">
                <div data-i18n="Notifications">Compose</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/followups/inbox') ? 'active' : '' }}">
              <a href="{{ url('/admin/followups/inbox') }}" class="menu-link">
                <div data-i18n="Account">Settings</div>
              </a>
              </li>
            </ul>
            </li> -->

            <li class="menu-item {{ request()->is('admin/search') ? 'active' : '' }}">
            <a href="{{ url('/admin/search') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-search"></i>
              <div data-i18n="Leads Pool">Search</div>
            </a>
            </li>
            <!-- <li class="menu-item {{ request()->is('admin/bulk-sms') ? 'active' : '' }}">
            <a href="{{ url('/admin/bulk-sms') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-message"></i>
              <div data-i18n="Leads Pool">Bulk SMS</div>
            </a>
            </li> -->

          <!-- <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Users</span>
          </li> -->

            <li class="menu-item {{ request()->is('admin/users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-group"></i>
              <div data-i18n="Account Settings">Users</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ request()->is('admin/users/admin') ? 'active' : '' }}">
              <a href="{{ url('/admin/users/admin') }}" class="menu-link">
                <div data-i18n="Account">Admin</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/users/counselor') ? 'active' : '' }}">
              <a href="{{ url('/admin/users/counselor') }}" class="menu-link">
                <div data-i18n="Notifications">Counselor</div>
              </a>
              </li>
              <!-- <li class="menu-item {{ request()->is('admin/users/role') ? 'active' : '' }}">
              <a href="{{ url('/admin/users/role') }}" class="menu-link">
                <div data-i18n="Notifications">Role</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/users/role-permission') ? 'active' : '' }}">
              <a href="{{ url('/admin/users/role-permission') }}" class="menu-link">
                <div data-i18n="Notifications">Role Permission</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/users/permission') ? 'active' : '' }}">
              <a href="{{ url('/admin/users/permission') }}" class="menu-link">
                <div data-i18n="Notifications">Permission</div>
              </a>
              </li> -->
            </ul>
            </li>

          <li class="menu-item {{ request()->is('admin/reports*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-bar-chart-alt"></i>
              <div data-i18n="Reports">Reports</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ request()->is('admin/reports/leads') ? 'active' : '' }}">
                <a href="{{ url('/admin/reports/leads') }}" class="menu-link">
                  <div data-i18n="Leads">Leads</div>
                </a>
              </li>
                <li class="menu-item {{ request()->is('admin/reports/pending-followups') ? 'active' : '' }}">
                <a href="{{ url('/admin/reports/pending-followups') }}" class="menu-link">
                  <div data-i18n="Pending Followups">Pending Tasks</div>
                </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/payments') ? 'active' : '' }}">
                <a href="{{ url('/admin/reports/payments') }}" class="menu-link">
                  <div data-i18n="Payments">Payments</div>
                </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/call-logs') ? 'active' : '' }}">
                <a href="{{ url('/admin/reports/call-logs') }}" class="menu-link">
                  <div data-i18n="Call Log">Call Log</div>
                </a>
                </li>
                <!-- <li class="menu-item {{ request()->is('admin/reports/picked-leads') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/picked-leads') }}" class="menu-link">
                    <div data-i18n="Picked Leads">Picked Leads</div>
                  </a>
                </li> -->
                <li class="menu-item {{ request()->is('admin/reports/counselor-performance') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/counselor-performance') }}" class="menu-link">
                    <div data-i18n="Counselors Performance">Counselors Performance</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/analytics') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/analytics') }}" class="menu-link">
                    <div data-i18n="Analytics">Analytics</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/transfer') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/transfer') }}" class="menu-link">
                    <div data-i18n="Transfer">Transfer</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/agent-commission') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/agent-commission') }}" class="menu-link">
                    <div data-i18n="Transfer">Agent Commission</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/consolidated') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/consolidated') }}" class="menu-link">
                    <div data-i18n="Transfer">Consolidated</div>
                  </a>
                </li>
                <li class="menu-item {{ request()->is('admin/reports/picked-leads') ? 'active' : '' }}">
                  <a href="{{ url('/admin/reports/picked-leads') }}" class="menu-link">
                    <div data-i18n="Picked Leads">Picked Leads</div>
                  </a>
                </li>
            </ul>
          </li>
          
            <li class="menu-item {{ request()->is('admin/logs/activity') ? 'active' : '' }}">
            <a href="{{ url('/admin/logs/activity') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-file"></i>
              <div data-i18n="Activity Logs">Activity Logs</div>
            </a>
            </li>
            <li class="menu-item {{ request()->is('admin/settings*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-cog"></i>
              <div data-i18n="Account Settings">Settings</div>
            </a>
            <ul class="menu-sub">
              <!-- <li class="menu-item {{ request()->is('admin/settings/countries') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/countries') }}" class="menu-link">
                <div data-i18n="Account">Countries</div>
              </a>
              </li> -->
              <li class="menu-item {{ request()->is('admin/settings/colleges') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/colleges') }}" class="menu-link">
                <div data-i18n="Notifications">Colleges</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/settings/courses') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/courses') }}" class="menu-link">
                <div data-i18n="Notifications">Courses</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/settings/academic-years') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/academic-years') }}" class="menu-link">
                <div data-i18n="Notifications">Academic Years</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/settings/sources') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/sources') }}" class="menu-link">
                <div data-i18n="Notifications">Source</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/settings/holidays') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/holidays') }}" class="menu-link">
                <div data-i18n="Notifications">Holidays</div>
              </a>
              </li>
              <li class="menu-item {{ request()->is('admin/settings/agents') ? 'active' : '' }}">
              <a href="{{ url('/admin/settings/agents') }}" class="menu-link">
                <div data-i18n="Notifications">Agents</div>
              </a>
              </li>
            </ul>
            </li>

          <!-- <li class="menu-item {{ request()->is('admin/config*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-wrench"></i>
              <div data-i18n="Config">Config</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item {{ request()->is('admin/config/general') ? 'active' : '' }}">
                <a href="{{ url('/admin/config/general') }}" class="menu-link">
                  <div data-i18n="General">General</div>
                </a>
              </li>
              <li class="menu-item {{ request()->is('admin/config/sms') ? 'active' : '' }}">
                <a href="{{ url('/admin/config/sms') }}" class="menu-link">
                  <div data-i18n="SMS">SMS</div>
                </a>
              </li>
              <li class="menu-item {{ request()->is('admin/config/payment-gateway') ? 'active' : '' }}">
                <a href="{{ url('/admin/config/payment-gateway') }}" class="menu-link">
                  <div data-i18n="Payment Gateway">Payment Gateway</div>
                </a>
              </li>
              <li class="menu-item {{ request()->is('admin/config/mail') ? 'active' : '' }}">
                <a href="{{ url('/admin/config/mail') }}" class="menu-link">
                  <div data-i18n="Mail">Mail</div>
                </a>
              </li>
            </ul>
          </li> -->
            <li class="menu-item {{ request()->is('admin/change-password') ? 'active' : '' }}">
            <a href="{{ url('/admin/change-password') }}" class="menu-link">
              <i class="menu-icon tf-icons bx bx-key"></i>
              <div data-i18n="Leads Pool">Change Password</div>
            </a>
            </li>


            <li class="menu-item logout-menu-item" style="position: fixed; bottom: 0;  background: #fff; z-index: 10; border-top: 1px solid #ddd;">
            <form method="POST" action="{{ route('admin.logout') }}" class="menu-link" style="display: flex; align-items: center;">
              @csrf
              <button type="submit" style="border: none; background: none; display: flex; align-items: center; width: 100%; padding: 0; text-align: left;">
              <i class="menu-icon tf-icons bx bx-power-off"></i>
              <div data-i18n="Logout">Logout</div>
              </button>
            </form>
            </li>
        </ul>
      </aside>
      <!-- / Menu -->