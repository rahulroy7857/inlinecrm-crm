@extends('counselor.layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y crm-page portal-dashboard portal-dashboard--counselor">

    <div class="portal-dashboard-header mb-3">
        <h1>Welcome back, {{ auth()->guard('counselor')->user()->name }}! 👋</h1>
        <p>Here's your lead performance overview for {{ session('academic_year_name') }}</p>
    </div>

    <div class="counselor-attendance-bar mb-4" id="counselorHoursPanel">
        <div class="counselor-attendance-bar__row">
            <div class="counselor-attendance-net">
                <span class="counselor-attendance-net__label">Working today</span>
                <strong class="counselor-attendance-net__value" id="whNetHours">{{ $workingHours['net_hours'] }}</strong>
            </div>

            <div class="counselor-attendance-chips">
                <span class="counselor-attendance-chip">
                    <i class="bx bx-log-in"></i>
                    <span class="chip-label">In</span>
                    <strong id="whLoginAt">{{ $workingHours['login_at'] ?? '—' }}</strong>
                </span>
                <span class="counselor-attendance-chip">
                    <i class="bx bx-log-out"></i>
                    <span class="chip-label">Out</span>
                    <strong id="whLogoutAt">{{ $workingHours['logout_at'] ?? ($workingHours['is_logged_in'] ? 'Active' : '—') }}</strong>
                </span>
                <span class="counselor-attendance-chip counselor-attendance-chip--muted">
                    Gross <strong id="whGrossHours">{{ $workingHours['gross_hours'] }}</strong>
                </span>
                <span class="counselor-attendance-chip counselor-attendance-chip--muted">
                    Breaks <strong id="whBreakHours">{{ $workingHours['break_hours'] }}</strong>
                </span>
            </div>

            <div class="counselor-attendance-bar__controls">
                <div class="counselor-attendance-bar__control {{ ($workingHours['active_break'] || $workingHours['pending_break']) ? 'counselor-attendance-is-hidden' : '' }}" id="whBreakActions">
                    <div class="counselor-break-dropdown" id="counselorBreakDropdown">
                        <button class="btn btn-sm counselor-break-dropdown__toggle"
                            type="button"
                            id="breakDropdown"
                            aria-expanded="false"
                            aria-haspopup="true">
                            <i class="bx bx-coffee"></i> Take a break
                            <i class="bx bx-chevron-down counselor-break-dropdown__chevron"></i>
                        </button>
                        <ul class="counselor-break-dropdown__menu" id="breakDropdownMenu" role="menu">
                            @foreach($workingHours['break_types'] as $breakType)
                                <li role="none">
                                    <button type="button" class="counselor-break-dropdown__item wh-start-break-btn" role="menuitem" data-type="{{ $breakType['type'] }}" data-requires-approval="{{ $breakType['requires_admin_approval'] ? '1' : '0' }}">
                                        <span>{{ $breakType['label'] }}</span>
                                        @if($breakType['requires_admin_approval'])
                                            <small>Admin approval</small>
                                        @elseif($breakType['duration_minutes'])
                                            <small>{{ $breakType['duration_minutes'] }} min</small>
                                        @endif
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div id="whPendingBreakWrap" class="counselor-attendance-bar__control counselor-attendance-pending {{ $workingHours['pending_break'] ? '' : 'counselor-attendance-is-hidden' }}">
                    <span class="counselor-attendance-pending__dot"></span>
                    <span class="counselor-attendance-pending__label" id="whPendingBreakLabel">{{ data_get($workingHours, 'pending_break.label', '') }}</span>
                    <span class="counselor-attendance-pending__text">Waiting for admin approval</span>
                </div>

                <div id="whActiveBreakWrap" class="counselor-attendance-bar__control counselor-attendance-active {{ $workingHours['active_break'] ? '' : 'counselor-attendance-is-hidden' }}">
                    <span class="counselor-attendance-active__dot"></span>
                    <span class="counselor-attendance-active__label" id="whActiveBreakLabel">{{ data_get($workingHours, 'active_break.label', '') }}</span>
                    <span class="counselor-attendance-active__elapsed" id="whActiveBreakElapsed">{{ data_get($workingHours, 'active_break.elapsed', '') }}</span>
                    <span class="counselor-attendance-active__ends {{ data_get($workingHours, 'active_break.ends_at') ? '' : 'counselor-attendance-is-hidden' }}" id="whActiveBreakEnds">
                        · ends <span id="whActiveBreakEndsAt">{{ data_get($workingHours, 'active_break.ends_at', '') }}</span>
                    </span>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="whEndBreakBtn">End</button>
                </div>
            </div>
        </div>

        <details class="counselor-break-history" id="whBreakHistory">
            <summary class="counselor-break-history__toggle">
                <span><i class="bx bx-history"></i> Today's break history (<span id="whBreakCount">{{ $workingHours['break_count'] ?? count($workingHours['breaks']) }}</span>)</span>
                <span class="counselor-break-history__total">Total break time: <strong id="whBreakHistoryTotal">{{ $workingHours['break_hours'] }}</strong></span>
            </summary>
            <div class="counselor-break-history__body" id="whBreakHistoryBody">
                @forelse(collect($workingHours['breaks'])->reverse() as $break)
                    <div class="counselor-break-history__row {{ $break['is_active'] ? 'is-active' : '' }}{{ !empty($break['is_pending']) ? ' is-pending' : '' }}{{ !empty($break['exceeded_duration']) ? ' is-exceeded' : '' }}">
                        <span class="counselor-break-history__type">{{ $break['label'] }}</span>
                        <span class="counselor-break-history__time">
                            @if(!empty($break['is_pending']))
                                {{ $break['requested_at'] ?? '—' }} – Pending
                            @else
                                {{ $break['started_at'] }} – {{ $break['ended_at'] ?? 'Active' }}
                            @endif
                        </span>
                        <span class="counselor-break-history__duration">{{ $break['elapsed'] }}</span>
                        <span class="counselor-break-history__status">{{ $break['status'] }}</span>
                    </div>
                @empty
                    <div class="counselor-break-history__empty" id="whBreakHistoryEmpty">No breaks recorded today.</div>
                @endforelse
            </div>
        </details>
    </div>

    <div class="stats-grid portal-stats-grid mb-4">
        <div class="portal-stat-card portal-stat-card--warm">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-sun"></i></div>
                <div class="card-title">Warm Leads</div>
                <h3>{{ number_format($leadsCount['warm']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['warm'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['warm'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'warm']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--hot">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-flame"></i></div>
                <div class="card-title">Hot Leads</div>
                <h3>{{ number_format($leadsCount['hot']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['hot'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['hot'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'hot']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--application">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-file"></i></div>
                <div class="card-title">Applications</div>
                <h3>{{ number_format($leadsCount['application']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['application'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['application'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'application']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--admission">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-graduation"></i></div>
                <div class="card-title">Admissions</div>
                <h3>{{ number_format($leadsCount['admission']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['admission'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['admission'] }}% from last year</div>
                <a href="{{ route('counselor.leads.status', ['status' => 'admission']) }}" class="stretched-link"></a>
            </div>
        </div>
        <div class="portal-stat-card portal-stat-card--total">
            <div class="card-body">
                <div class="icon-bg"><i class="bx bx-group"></i></div>
                <div class="card-title">Total Leads</div>
                <h3>{{ number_format($leadsCount['total']) }}</h3>
                <div class="trend"><i class="bx bx-trending-up"></i> {{ $leadsPercentageDiff['total'] >= 0 ? '+' : '' }}{{ $leadsPercentageDiff['total'] }}% from last year</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="portal-chart-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5><span class="chart-icon chart-icon--leads"><i class="bx bx-line-chart"></i></span> Monthly Leads {{ date('Y') }}</h5>
                    <span class="conversion-pill"><i class="bx bx-trending-up"></i> {{ $conversionRate }}% conversion</span>
                </div>
                <div class="card-body"><div id="monthlyLeadsChart" class="chart-host"></div></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--status"><i class="bx bx-pie-chart-alt-2"></i></span> Lead Status</h5>
                </div>
                <div class="card-body"><div id="leadsStatusChart" class="chart-host chart-host--tall"></div></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--funnel"><i class="bx bx-filter-alt"></i></span> Lead Conversion Funnel</h5>
                </div>
                <div class="card-body"><div id="leadFunnelChart" class="chart-host"></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="portal-chart-card h-100">
                <div class="card-header">
                    <h5><span class="chart-icon chart-icon--followups"><i class="bx bx-calendar-check"></i></span> {{ date('Y') }} Follow-up Performance</h5>
                </div>
                <div class="card-body"><div id="todaysFollowupsChart" class="chart-host"></div></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const statusUrl = @json(route('counselor.working-hours.status'));
    const startBreakUrl = @json(route('counselor.working-hours.break.start'));
    const endBreakUrl = @json(route('counselor.working-hours.break.end'));

    let breakEndTimer = null;
    let breakPollTimer = null;

    function showHoursToast(type, message) {
        if (window.showCrmToast) {
            window.showCrmToast(type, message);
        }
    }

    function redirectToLoginForBreakLock(message) {
        if (message) {
            try {
                sessionStorage.setItem('break_login_lock_message', message);
            } catch (e) {}
        }
        window.location.href = @json(route('counselor.login'));
    }

    function handleBreakLockFetchResponse(response, data) {
        if (response.status === 403 && data && data.break_login_locked) {
            redirectToLoginForBreakLock(data.message);
            return true;
        }

        if (data && data.data && data.data.logout_required) {
            redirectToLoginForBreakLock(data.data.break_login_lock_reason || data.message);
            return true;
        }

        return false;
    }

    function clearBreakTimers() {
        if (breakEndTimer) {
            clearTimeout(breakEndTimer);
            breakEndTimer = null;
        }
        if (breakPollTimer) {
            clearInterval(breakPollTimer);
            breakPollTimer = null;
        }
    }

    function scheduleBreakRefresh(data) {
        clearBreakTimers();

        if (!data.active_break) {
            return;
        }

        breakPollTimer = setInterval(refreshWorkingHours, 30000);

        if (data.active_break.ends_at_iso) {
            const endsAt = new Date(data.active_break.ends_at_iso).getTime();
            const delay = Math.max(1000, endsAt - Date.now() + 500);

            breakEndTimer = setTimeout(function () {
                refreshWorkingHours(true);
            }, delay);
        }
    }

    function renderBreakHistory(data) {
        const body = document.getElementById('whBreakHistoryBody');
        const countEl = document.getElementById('whBreakCount');
        const totalEl = document.getElementById('whBreakHistoryTotal');
        if (!body) return;

        const breaks = data.breaks || [];

        if (countEl) {
            countEl.textContent = data.break_count ?? breaks.length;
        }
        if (totalEl) {
            totalEl.textContent = data.break_hours || '0m';
        }

        if (!breaks.length) {
            body.innerHTML = '<div class="counselor-break-history__empty" id="whBreakHistoryEmpty">No breaks recorded today.</div>';
            return;
        }

        body.innerHTML = breaks.slice().reverse().map(function (item) {
            const timeLabel = item.is_pending
                ? (item.requested_at || '—') + ' – Pending'
                : (item.started_at || '—') + ' – ' + (item.ended_at || 'Active');
            let activeClass = item.is_active ? ' is-active' : '';
            if (item.is_pending) {
                activeClass += ' is-pending';
            }
            if (item.exceeded_duration) {
                activeClass += ' is-exceeded';
            }
            return '<div class="counselor-break-history__row' + activeClass + '">' +
                '<span class="counselor-break-history__type">' + item.label + '</span>' +
                '<span class="counselor-break-history__time">' + timeLabel + '</span>' +
                '<span class="counselor-break-history__duration">' + item.elapsed + '</span>' +
                '<span class="counselor-break-history__status">' + item.status + '</span>' +
                '</div>';
        }).join('');
    }

    function updateWorkingHoursPanel(data) {
        document.getElementById('whLoginAt').textContent = data.login_at || '—';
        document.getElementById('whLogoutAt').textContent = data.logout_at || (data.is_logged_in ? 'Active' : '—');
        document.getElementById('whNetHours').textContent = data.net_hours;
        document.getElementById('whGrossHours').textContent = data.gross_hours;
        document.getElementById('whBreakHours').textContent = data.break_hours;

        renderBreakHistory(data);

        const activeWrap = document.getElementById('whActiveBreakWrap');
        const pendingWrap = document.getElementById('whPendingBreakWrap');
        const breakActions = document.getElementById('whBreakActions');
        const endsWrap = document.getElementById('whActiveBreakEnds');
        const hadActiveBreak = !activeWrap.classList.contains('counselor-attendance-is-hidden');

        if (data.pending_break) {
            closeBreakDropdown();
            pendingWrap.classList.remove('counselor-attendance-is-hidden');
            breakActions.classList.add('counselor-attendance-is-hidden');
            activeWrap.classList.add('counselor-attendance-is-hidden');
            document.getElementById('whPendingBreakLabel').textContent = data.pending_break.label;
            clearBreakTimers();
            if (!breakPollTimer) {
                breakPollTimer = setInterval(refreshWorkingHours, 15000);
            }
            return;
        }

        pendingWrap.classList.add('counselor-attendance-is-hidden');

        if (data.active_break) {
            closeBreakDropdown();
            activeWrap.classList.remove('counselor-attendance-is-hidden');
            breakActions.classList.add('counselor-attendance-is-hidden');
            document.getElementById('whActiveBreakLabel').textContent = data.active_break.label;
            document.getElementById('whActiveBreakElapsed').textContent = data.active_break.elapsed;

            if (data.active_break.ends_at) {
                endsWrap.classList.remove('counselor-attendance-is-hidden');
                document.getElementById('whActiveBreakEndsAt').textContent = data.active_break.ends_at;
            } else {
                endsWrap.classList.add('counselor-attendance-is-hidden');
            }

            scheduleBreakRefresh(data);
        } else {
            activeWrap.classList.add('counselor-attendance-is-hidden');
            breakActions.classList.remove('counselor-attendance-is-hidden');
            clearBreakTimers();

            if (hadActiveBreak) {
                showHoursToast('success', 'Break ended. Working hours updated.');
            }
        }
    }

    function startBreak(type) {
        if (!type) return;

        fetch(startBreakUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ type: type })
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function (result) {
                if (handleBreakLockFetchResponse({ status: result.status }, result.data)) {
                    return;
                }
                if (!result.ok) {
                    showHoursToast('error', result.data.message || 'Unable to start break.');
                    return;
                }
                if (result.data.data && result.data.data.logout_required) {
                    redirectToLoginForBreakLock(result.data.data.break_login_lock_reason);
                    return;
                }
                updateWorkingHoursPanel(result.data.data);
                showHoursToast('success', result.data.message || 'Break started.');
            })
            .catch(function () {
                showHoursToast('error', 'Unable to start break.');
            });
    }

    function refreshWorkingHours(fromAutoEnd) {
        fetch(statusUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { response: response, data: data };
                });
            })
            .then(function (result) {
                if (handleBreakLockFetchResponse(result.response, result.data)) {
                    return;
                }
                if (result.data.success) {
                    if (result.data.data && result.data.data.logout_required) {
                        redirectToLoginForBreakLock(result.data.data.break_login_lock_reason);
                        return;
                    }
                    updateWorkingHoursPanel(result.data.data);
                }
            })
            .catch(function () {});
    }

    const breakDropdown = document.getElementById('counselorBreakDropdown');
    const breakDropdownBtn = document.getElementById('breakDropdown');
    const breakDropdownMenu = document.getElementById('breakDropdownMenu');
    let breakMenuPlaceholder = null;

    function positionBreakMenu() {
        if (!breakDropdownBtn || !breakDropdownMenu) return;

        const rect = breakDropdownBtn.getBoundingClientRect();
        breakDropdownMenu.style.position = 'fixed';
        breakDropdownMenu.style.top = Math.round(rect.bottom + 8) + 'px';
        breakDropdownMenu.style.left = Math.round(rect.right - Math.max(rect.width, 240)) + 'px';
        breakDropdownMenu.style.minWidth = Math.max(rect.width, 240) + 'px';
        breakDropdownMenu.style.zIndex = '9999';
    }

    function closeBreakDropdown() {
        if (!breakDropdown || !breakDropdownMenu) return;

        breakDropdown.classList.remove('is-open');
        breakDropdownMenu.classList.remove('is-visible');

        if (breakMenuPlaceholder && breakMenuPlaceholder.parentNode) {
            breakMenuPlaceholder.parentNode.insertBefore(breakDropdownMenu, breakMenuPlaceholder);
            breakMenuPlaceholder.remove();
            breakMenuPlaceholder = null;
        }

        breakDropdownMenu.style.position = '';
        breakDropdownMenu.style.top = '';
        breakDropdownMenu.style.left = '';
        breakDropdownMenu.style.minWidth = '';
        breakDropdownMenu.style.zIndex = '';

        if (breakDropdownBtn) {
            breakDropdownBtn.setAttribute('aria-expanded', 'false');
        }
    }

    function openBreakDropdown() {
        if (!breakDropdown || !breakDropdownMenu || !breakDropdownBtn) return;

        breakDropdown.classList.add('is-open');
        breakDropdownMenu.classList.add('is-visible');
        breakDropdownBtn.setAttribute('aria-expanded', 'true');

        breakMenuPlaceholder = document.createComment('break-menu-anchor');
        breakDropdownMenu.parentNode.insertBefore(breakMenuPlaceholder, breakDropdownMenu);
        document.body.appendChild(breakDropdownMenu);
        positionBreakMenu();
    }

    function toggleBreakDropdown() {
        if (!breakDropdown) return;

        if (breakDropdown.classList.contains('is-open')) {
            closeBreakDropdown();
        } else {
            openBreakDropdown();
        }
    }

    if (breakDropdownBtn) {
        breakDropdownBtn.addEventListener('click', function (event) {
            event.stopPropagation();
            toggleBreakDropdown();
        });
    }

    window.addEventListener('resize', function () {
        if (breakDropdown && breakDropdown.classList.contains('is-open')) {
            positionBreakMenu();
        }
    });

    window.addEventListener('scroll', function () {
        if (breakDropdown && breakDropdown.classList.contains('is-open')) {
            positionBreakMenu();
        }
    }, true);

    function endBreak() {
        const endBreakBtn = document.getElementById('whEndBreakBtn');
        if (!endBreakBtn || endBreakBtn.disabled) return;

        endBreakBtn.disabled = true;

        fetch(endBreakUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({})
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function (result) {
                if (handleBreakLockFetchResponse({ status: result.status }, result.data)) {
                    return;
                }
                if (!result.ok) {
                    showHoursToast('error', result.data.message || 'Unable to end break.');
                    return;
                }
                if (result.data.data && result.data.data.logout_required) {
                    redirectToLoginForBreakLock(result.data.data.break_login_lock_reason);
                    return;
                }
                updateWorkingHoursPanel(result.data.data);
            })
            .catch(function () {
                showHoursToast('error', 'Unable to end break.');
            })
            .finally(function () {
                endBreakBtn.disabled = false;
            });
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('#whEndBreakBtn')) {
            event.preventDefault();
            event.stopPropagation();
            endBreak();
            return;
        }

        const breakOption = event.target.closest('.wh-start-break-btn');
        if (breakOption) {
            event.preventDefault();
            event.stopPropagation();
            closeBreakDropdown();
            startBreak(breakOption.dataset.type);
            return;
        }

        if (!breakDropdown || !breakDropdown.classList.contains('is-open')) {
            return;
        }

        if (breakDropdownBtn && breakDropdownBtn.contains(event.target)) {
            return;
        }

        if (breakDropdownMenu && breakDropdownMenu.contains(event.target)) {
            return;
        }

        closeBreakDropdown();
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeBreakDropdown();
        }
    });

    @if($workingHours['active_break'])
    scheduleBreakRefresh(@json($workingHours));
    @elseif($workingHours['pending_break'])
    breakPollTimer = setInterval(refreshWorkingHours, 15000);
    @endif

    setInterval(function () {
        if (!document.getElementById('whActiveBreakWrap').classList.contains('counselor-attendance-is-hidden')) {
            return;
        }
        refreshWorkingHours();
    }, 60000);

    if (typeof ApexCharts === 'undefined') return;

    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const baseChart = { fontFamily: '"Golos Text", sans-serif', toolbar: { show: false }, animations: { enabled: true, speed: 700 } };
    const noData = { text: 'No data available', align: 'center', verticalAlign: 'middle', style: { color: '#94a3b8' } };
    const gradientFill = (from, to) => ({
        type: 'gradient',
        gradient: { shade: 'light', type: 'vertical', gradientToColors: [to], opacityFrom: 0.9, opacityTo: 0.25, stops: [0, 100] },
        colors: [from],
    });

    const monthlyEl = document.querySelector('#monthlyLeadsChart');
    if (monthlyEl) {
        new ApexCharts(monthlyEl, {
            series: [{ name: 'Leads', data: @json(array_values($monthlyLeadsData)) }],
            chart: { ...baseChart, type: 'area', height: 320 },
            colors: ['#4776e6'],
            fill: gradientFill('#4776e6', '#8e54e9'),
            stroke: { curve: 'smooth', width: 3 },
            xaxis: { categories: months },
            tooltip: { y: { formatter: (v) => v + ' leads' } },
            noData,
        }).render();
    }

    const statusEl = document.querySelector('#leadsStatusChart');
    if (statusEl) {
        const labels = @json(array_keys($leadStatusDistribution));
        const values = @json(array_values($leadStatusDistribution));
        new ApexCharts(statusEl, {
            series: values.length ? values : [1],
            chart: { ...baseChart, type: 'donut', height: 380 },
            labels: labels.length ? labels : ['No data'],
            colors: @json($statusColors).length ? @json($statusColors) : ['#cbd5e1'],
            fill: { type: 'gradient' },
            plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', formatter: () => @json(number_format($leadsCount['total'])) } } } } },
            legend: { position: 'bottom' },
            dataLabels: { enabled: false },
            noData,
        }).render();
    }

    const funnelEl = document.querySelector('#leadFunnelChart');
    if (funnelEl) {
        const funnelData = @json($funnelData);
        const funnelPercentages = @json($funnelPercentages);
        new ApexCharts(funnelEl, {
            series: [{ name: 'Leads', data: [funnelData.total, funnelData.positive, funnelData.application, funnelData.admission] }],
            chart: { ...baseChart, type: 'radar', height: 320 },
            colors: ['#8e54e9'],
            fill: { opacity: 0.2 },
            stroke: { width: 2 },
            markers: { size: 4 },
            xaxis: { categories: ['Total', 'Positive', 'Application', 'Admission'] },
            yaxis: { show: false },
            tooltip: { y: { formatter: (v, opts) => {
                const keys = ['total', 'positive', 'application', 'admission'];
                return v + ' (' + (funnelPercentages[keys[opts.dataPointIndex]] ?? 0) + '%)';
            } } },
            noData,
        }).render();
    }

    const followupsEl = document.querySelector('#todaysFollowupsChart');
    if (followupsEl) {
        new ApexCharts(followupsEl, {
            series: [
                { name: 'Positive', data: @json($followupsData->pluck('positive_count')) },
                { name: 'Negative', data: @json($followupsData->pluck('negative_count')) },
            ],
            chart: { ...baseChart, type: 'line', height: 320 },
            stroke: { curve: 'smooth', width: 3 },
            colors: ['#10b981', '#f43f5e'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.05 } },
            xaxis: { categories: @json($months) },
            legend: { position: 'top', horizontalAlign: 'right' },
            tooltip: { y: { formatter: (v) => v + ' follow-ups' } },
            noData,
        }).render();
    }
});
</script>
@endsection
