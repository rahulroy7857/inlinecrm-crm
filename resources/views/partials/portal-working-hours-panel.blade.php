<div class="counselor-attendance-bar mb-4" id="portalHoursPanel">
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
                <div class="counselor-break-dropdown" id="portalBreakDropdown">
                    <button class="btn btn-sm counselor-break-dropdown__toggle" type="button" id="breakDropdown" aria-expanded="false" aria-haspopup="true">
                        <i class="bx bx-coffee"></i> Take a break
                        <i class="bx bx-chevron-down counselor-break-dropdown__chevron"></i>
                    </button>
                    <ul class="counselor-break-dropdown__menu" id="breakDropdownMenu" role="menu">
                        @foreach($workingHours['break_types'] as $breakType)
                            <li role="none">
                                <button type="button" class="counselor-break-dropdown__item wh-start-break-btn" role="menuitem" data-type="{{ $breakType['type'] }}">
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
