<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('portalHoursPanel');
    if (!panel) return;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const statusUrl = @json($statusUrl);
    const startBreakUrl = @json($startBreakUrl);
    const endBreakUrl = @json($endBreakUrl);
    const loginUrl = @json($loginUrl);

    let breakEndTimer = null;
    let breakPollTimer = null;

    function showHoursToast(type, message) {
        if (window.showCrmToast) window.showCrmToast(type, message);
    }

    function redirectToLoginForBreakLock(message) {
        if (message) {
            try { sessionStorage.setItem('break_login_lock_message', message); } catch (e) {}
        }
        window.location.href = loginUrl;
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
        if (breakEndTimer) { clearTimeout(breakEndTimer); breakEndTimer = null; }
        if (breakPollTimer) { clearInterval(breakPollTimer); breakPollTimer = null; }
    }

    function scheduleBreakRefresh(data) {
        clearBreakTimers();
        if (!data.active_break) return;
        breakPollTimer = setInterval(refreshWorkingHours, 30000);
        if (data.active_break.ends_at_iso) {
            const delay = Math.max(1000, new Date(data.active_break.ends_at_iso).getTime() - Date.now() + 500);
            breakEndTimer = setTimeout(refreshWorkingHours, delay);
        }
    }

    function renderBreakHistory(data) {
        const body = document.getElementById('whBreakHistoryBody');
        const countEl = document.getElementById('whBreakCount');
        const totalEl = document.getElementById('whBreakHistoryTotal');
        if (!body) return;
        const breaks = data.breaks || [];
        if (countEl) countEl.textContent = data.break_count ?? breaks.length;
        if (totalEl) totalEl.textContent = data.break_hours || '0m';
        if (!breaks.length) {
            body.innerHTML = '<div class="counselor-break-history__empty">No breaks recorded today.</div>';
            return;
        }
        body.innerHTML = breaks.slice().reverse().map(function (item) {
            const timeLabel = item.is_pending
                ? (item.requested_at || '—') + ' – Pending'
                : (item.started_at || '—') + ' – ' + (item.ended_at || 'Active');
            let cls = item.is_active ? ' is-active' : '';
            if (item.is_pending) cls += ' is-pending';
            if (item.exceeded_duration) cls += ' is-exceeded';
            return '<div class="counselor-break-history__row' + cls + '">' +
                '<span class="counselor-break-history__type">' + item.label + '</span>' +
                '<span class="counselor-break-history__time">' + timeLabel + '</span>' +
                '<span class="counselor-break-history__duration">' + item.elapsed + '</span>' +
                '<span class="counselor-break-history__status">' + item.status + '</span></div>';
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
            if (!breakPollTimer) breakPollTimer = setInterval(refreshWorkingHours, 15000);
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
            if (hadActiveBreak) showHoursToast('success', 'Break ended. Working hours updated.');
        }
    }

    function startBreak(type) {
        if (!type) return;
        fetch(startBreakUrl, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ type: type })
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, status: r.status, data: d }; }); })
        .then(function (result) {
            if (handleBreakLockFetchResponse({ status: result.status }, result.data)) return;
            if (!result.ok) { showHoursToast('error', result.data.message || 'Unable to start break.'); return; }
            if (result.data.data && result.data.data.logout_required) { redirectToLoginForBreakLock(result.data.data.break_login_lock_reason); return; }
            updateWorkingHoursPanel(result.data.data);
            showHoursToast('success', result.data.message || 'Break started.');
        }).catch(function () { showHoursToast('error', 'Unable to start break.'); });
    }

    function refreshWorkingHours() {
        fetch(statusUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().then(function (d) { return { response: r, data: d }; }); })
            .then(function (result) {
                if (handleBreakLockFetchResponse(result.response, result.data)) return;
                if (result.data.success) {
                    if (result.data.data && result.data.data.logout_required) { redirectToLoginForBreakLock(result.data.data.break_login_lock_reason); return; }
                    updateWorkingHoursPanel(result.data.data);
                }
            }).catch(function () {});
    }

    const breakDropdown = document.getElementById('portalBreakDropdown');
    const breakDropdownBtn = document.getElementById('breakDropdown');
    const breakDropdownMenu = document.getElementById('breakDropdownMenu');
    let breakMenuPlaceholder = null;

    function positionBreakMenu() {
        if (!breakDropdownBtn || !breakDropdownMenu) return;
        const rect = breakDropdownBtn.getBoundingClientRect();
        breakDropdownMenu.style.cssText = 'position:fixed;top:' + Math.round(rect.bottom + 8) + 'px;left:' + Math.round(rect.right - Math.max(rect.width, 240)) + 'px;min-width:' + Math.max(rect.width, 240) + 'px;z-index:9999';
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
        breakDropdownMenu.style.cssText = '';
        if (breakDropdownBtn) breakDropdownBtn.setAttribute('aria-expanded', 'false');
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

    if (breakDropdownBtn) {
        breakDropdownBtn.addEventListener('click', function (e) { e.stopPropagation(); breakDropdown.classList.contains('is-open') ? closeBreakDropdown() : openBreakDropdown(); });
    }
    window.addEventListener('resize', function () { if (breakDropdown && breakDropdown.classList.contains('is-open')) positionBreakMenu(); });
    window.addEventListener('scroll', function () { if (breakDropdown && breakDropdown.classList.contains('is-open')) positionBreakMenu(); }, true);

    function endBreak() {
        const btn = document.getElementById('whEndBreakBtn');
        if (!btn || btn.disabled) return;
        btn.disabled = true;
        fetch(endBreakUrl, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({})
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, status: r.status, data: d }; }); })
        .then(function (result) {
            if (handleBreakLockFetchResponse({ status: result.status }, result.data)) return;
            if (!result.ok) { showHoursToast('error', result.data.message || 'Unable to end break.'); return; }
            if (result.data.data && result.data.data.logout_required) { redirectToLoginForBreakLock(result.data.data.break_login_lock_reason); return; }
            updateWorkingHoursPanel(result.data.data);
        }).catch(function () { showHoursToast('error', 'Unable to end break.'); })
        .finally(function () { btn.disabled = false; });
    }

    document.addEventListener('click', function (event) {
        if (event.target.closest('#whEndBreakBtn')) { event.preventDefault(); event.stopPropagation(); endBreak(); return; }
        const opt = event.target.closest('.wh-start-break-btn');
        if (opt) { event.preventDefault(); event.stopPropagation(); closeBreakDropdown(); startBreak(opt.dataset.type); return; }
        if (!breakDropdown || !breakDropdown.classList.contains('is-open')) return;
        if (breakDropdownBtn && breakDropdownBtn.contains(event.target)) return;
        if (breakDropdownMenu && breakDropdownMenu.contains(event.target)) return;
        closeBreakDropdown();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeBreakDropdown(); });

    @if(!empty($workingHours['active_break']))
    scheduleBreakRefresh(@json($workingHours));
    @elseif(!empty($workingHours['pending_break']))
    breakPollTimer = setInterval(refreshWorkingHours, 15000);
    @endif

    setInterval(function () {
        const activeWrap = document.getElementById('whActiveBreakWrap');
        const pendingWrap = document.getElementById('whPendingBreakWrap');
        if (activeWrap && !activeWrap.classList.contains('counselor-attendance-is-hidden')) return;
        if (pendingWrap && !pendingWrap.classList.contains('counselor-attendance-is-hidden')) return;
        refreshWorkingHours();
    }, 60000);
});
</script>
