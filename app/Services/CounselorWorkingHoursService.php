<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Counselor;
use App\Models\CounselorBreak;
use Carbon\Carbon;

class CounselorWorkingHoursService
{
    public function __construct(
        private CounselorBreakSettingsService $breakSettings
    ) {}

    public function getTodaySummary(Counselor $counselor): array
    {
        $this->repairTimedBreaks($counselor);
        $logoutRequired = $this->processBreakOvertime($counselor);
        $this->normalizeActiveBreaks($counselor);

        $counselor->refresh();

        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $attendance = $this->attendanceForToday($counselor, $todayStart, $todayEnd);
        $breaks = $this->breaksForToday($counselor, $todayStart, $todayEnd);
        $activeBreak = $breaks
            ->filter(fn (CounselorBreak $break) => $break->isActive())
            ->sortByDesc('started_at')
            ->first();
        $pendingBreak = $this->pendingBreakRequest($counselor);

        $loginAt = $attendance['first_login_at'];
        $logoutAt = $attendance['is_logged_in'] ? null : $attendance['last_logout_at'];
        $grossMinutes = (int) $attendance['session_minutes'];

        $breakMinutes = (int) $breaks
            ->filter(fn (CounselorBreak $break) => $break->started_at !== null)
            ->sum(fn (CounselorBreak $break) => $this->breakDurationMinutes($break));
        $netMinutes = max(0, $grossMinutes - $breakMinutes);

        return [
            'login_at' => $loginAt?->format('h:i A'),
            'logout_at' => $logoutAt?->format('h:i A'),
            'is_logged_in' => $attendance['is_logged_in'],
            'gross_minutes' => $grossMinutes,
            'break_minutes' => $breakMinutes,
            'net_minutes' => $netMinutes,
            'gross_hours' => $this->formatMinutes($grossMinutes),
            'break_hours' => $this->formatMinutes($breakMinutes),
            'net_hours' => $this->formatMinutes($netMinutes),
            'break_count' => $breaks->count(),
            'active_break' => $activeBreak ? $this->formatBreak($activeBreak) : null,
            'pending_break' => $pendingBreak ? $this->formatBreak($pendingBreak) : null,
            'breaks' => $breaks->map(fn (CounselorBreak $break) => $this->formatBreak($break))->values()->all(),
            'break_types' => $this->breakSettings->breakTypesPayload(),
            'break_login_locked' => (bool) $counselor->break_login_locked,
            'break_login_lock_reason' => $counselor->break_login_lock_reason,
            'logout_required' => $logoutRequired,
        ];
    }

    public function pendingBreakRequests()
    {
        return CounselorBreak::query()
            ->with('counselor')
            ->where('approval_status', CounselorBreak::APPROVAL_PENDING)
            ->whereDate('requested_at', today())
            ->orderBy('requested_at')
            ->get();
    }

    public function processBreakOvertime(Counselor $counselor): bool
    {
        $logoutRequired = false;

        CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->whereNull('ended_at')
            ->whereNotNull('started_at')
            ->whereNotNull('duration_minutes')
            ->whereDate('started_at', today())
            ->get()
            ->each(function (CounselorBreak $break) use ($counselor, &$logoutRequired) {
                $deadline = $break->started_at->copy()->addMinutes($break->duration_minutes);
                if (now()->lt($deadline)) {
                    return;
                }

                $break->update([
                    'ended_at' => $deadline,
                    'exceeded_duration' => true,
                ]);

                $this->lockCounselorForBreakOvertime($counselor, $break->fresh());
                $logoutRequired = true;
            });

        return $logoutRequired;
    }

    public function unlockBreakLogin(Counselor $counselor, ?int $adminId = null): void
    {
        $counselor->update([
            'break_login_locked' => false,
            'break_login_locked_at' => null,
            'break_login_lock_reason' => null,
            'break_login_unlocked_by' => $adminId,
            'break_login_unlocked_at' => now(),
        ]);
    }

    public function startBreak(Counselor $counselor, string $type): CounselorBreak
    {
        $setting = $this->breakSettings->findByType($type);
        if (!$setting || !$setting->is_active) {
            throw new \InvalidArgumentException('Invalid break type.');
        }

        $this->repairTimedBreaks($counselor);
        $this->processBreakOvertime($counselor);
        $this->normalizeActiveBreaks($counselor);

        if ($this->pendingBreakRequest($counselor)) {
            throw new \RuntimeException('You already have a break request waiting for admin approval.');
        }

        $activeBreak = CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->whereNull('ended_at')
            ->whereNotNull('started_at')
            ->whereDate('started_at', today())
            ->first();

        if ($activeBreak) {
            throw new \RuntimeException('You already have an active break. Please end it first.');
        }

        if ($setting->requires_admin_approval) {
            return $this->createPendingBreakRequest($counselor, $setting);
        }

        $break = CounselorBreak::create([
            'counselor_id' => $counselor->id,
            'type' => $type,
            'approval_status' => CounselorBreak::APPROVAL_APPROVED,
            'duration_minutes' => $setting->duration_minutes,
            'started_at' => now(),
        ]);

        ActivityLogger::log(
            "Break started: {$break->label}",
            'Break',
            $counselor,
            ['break_id' => $break->id, 'type' => $type]
        );

        return $break;
    }

    public function approveBreakRequest(CounselorBreak $break, int $adminId, ?int $durationMinutes = null): CounselorBreak
    {
        if (!$break->isPendingApproval()) {
            throw new \RuntimeException('This break request is no longer pending.');
        }

        $counselor = $break->counselor;
        $setting = $this->breakSettings->findByType($break->type);

        if ($this->pendingBreakRequest($counselor)?->id !== $break->id) {
            throw new \RuntimeException('Break request not found for today.');
        }

        $activeBreak = CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->whereNull('ended_at')
            ->whereNotNull('started_at')
            ->whereDate('started_at', today())
            ->first();

        if ($activeBreak) {
            throw new \RuntimeException('Counselor already has an active break.');
        }

        $duration = $durationMinutes ?? $setting?->duration_minutes;

        $break->update([
            'approval_status' => CounselorBreak::APPROVAL_APPROVED,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'duration_minutes' => $duration,
            'started_at' => now(),
        ]);

        ActivityLogger::log(
            "Break request approved: {$break->label} for {$counselor->name}",
            'Break Approve',
            $counselor,
            ['break_id' => $break->id, 'admin_id' => $adminId]
        );

        return $break->fresh();
    }

    public function rejectBreakRequest(CounselorBreak $break, int $adminId, ?string $reason = null): CounselorBreak
    {
        if (!$break->isPendingApproval()) {
            throw new \RuntimeException('This break request is no longer pending.');
        }

        $break->update([
            'approval_status' => CounselorBreak::APPROVAL_REJECTED,
            'approved_by' => $adminId,
            'approved_at' => now(),
            'rejected_reason' => $reason ?: 'Rejected by admin.',
        ]);

        ActivityLogger::log(
            "Break request rejected: {$break->label} for {$break->counselor->name}",
            'Break Reject',
            $break->counselor,
            ['break_id' => $break->id, 'admin_id' => $adminId, 'reason' => $reason]
        );

        return $break->fresh();
    }

    public function endActiveBreak(Counselor $counselor): ?CounselorBreak
    {
        $this->repairTimedBreaks($counselor);
        $this->processBreakOvertime($counselor);
        $this->normalizeActiveBreaks($counselor);

        $activeBreaks = CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->whereNull('ended_at')
            ->whereNotNull('started_at')
            ->whereDate('started_at', today())
            ->orderByDesc('started_at')
            ->get();

        if ($activeBreaks->isEmpty()) {
            return null;
        }

        $primary = $activeBreaks->first();
        $isLateEnd = $this->isBreakPastDeadline($primary);

        foreach ($activeBreaks as $break) {
            $this->finalizeBreakEnd($break);

            if ($this->isBreakPastDeadline($break)) {
                $break->update(['exceeded_duration' => true]);
            }
        }

        if ($isLateEnd) {
            $this->lockCounselorForBreakOvertime($counselor, $primary->fresh());
        }

        ActivityLogger::log(
            "Break ended: {$primary->label}",
            'Break',
            $counselor,
            ['break_id' => $primary->id, 'type' => $primary->type]
        );

        return $primary->fresh();
    }

    private function createPendingBreakRequest(Counselor $counselor, $setting): CounselorBreak
    {
        $break = CounselorBreak::create([
            'counselor_id' => $counselor->id,
            'type' => $setting->type,
            'approval_status' => CounselorBreak::APPROVAL_PENDING,
            'requested_at' => now(),
            'duration_minutes' => $setting->duration_minutes,
            'started_at' => null,
        ]);

        ActivityLogger::log(
            "Break request submitted: {$break->label}",
            'Break Request',
            $counselor,
            ['break_id' => $break->id, 'type' => $setting->type]
        );

        return $break;
    }

    private function pendingBreakRequest(Counselor $counselor): ?CounselorBreak
    {
        return CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->where('approval_status', CounselorBreak::APPROVAL_PENDING)
            ->whereDate('requested_at', today())
            ->latest('requested_at')
            ->first();
    }

    private function lockCounselorForBreakOvertime(Counselor $counselor, CounselorBreak $break): void
    {
        if ($counselor->break_login_locked) {
            return;
        }

        $reason = sprintf(
            'Exceeded %s break time (%d min). Admin permission is required to login again.',
            $break->label,
            $break->duration_minutes
        );

        $counselor->update([
            'break_login_locked' => true,
            'break_login_locked_at' => now(),
            'break_login_lock_reason' => $reason,
        ]);

        ActivityLogger::log(
            "Break overtime lock applied: {$break->label}",
            'Break Lock',
            $counselor,
            [
                'break_id' => $break->id,
                'type' => $break->type,
                'duration_minutes' => $break->duration_minutes,
            ]
        );
    }

    private function isBreakPastDeadline(CounselorBreak $break): bool
    {
        if (!$break->duration_minutes || !$break->started_at) {
            return false;
        }

        return now()->gt($break->started_at->copy()->addMinutes($break->duration_minutes));
    }

    private function finalizeBreakEnd(CounselorBreak $break): void
    {
        $endedAt = now();

        if ($endedAt->lte($break->started_at)) {
            $endedAt = $break->started_at->copy()->addMinute();
        }

        if ($break->duration_minutes) {
            $maxEnd = $break->started_at->copy()->addMinutes($break->duration_minutes);
            if ($endedAt->gt($maxEnd)) {
                $endedAt = $maxEnd;
            }
        }

        $break->update(['ended_at' => $endedAt]);
    }

    private function normalizeActiveBreaks(Counselor $counselor): void
    {
        $activeBreaks = CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->whereNull('ended_at')
            ->whereNotNull('started_at')
            ->whereDate('started_at', today())
            ->orderByDesc('started_at')
            ->get();

        if ($activeBreaks->count() <= 1) {
            return;
        }

        $activeBreaks->slice(1)->each(function (CounselorBreak $break) {
            $this->finalizeBreakEnd($break);
        });
    }

    private function attendanceForToday(Counselor $counselor, Carbon $todayStart, Carbon $todayEnd): array
    {
        $logs = ActivityLog::query()
            ->where('causer_type', Counselor::class)
            ->where('causer_id', $counselor->id)
            ->whereIn('action', ['Login', 'Logout'])
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->orderBy('created_at')
            ->get();

        $firstLogin = null;
        $lastLogin = null;
        $lastLogout = null;
        $openLogin = null;
        $sessionMinutes = 0;

        foreach ($logs as $log) {
            if ($log->action === 'Login') {
                if ($firstLogin === null) {
                    $firstLogin = $log->created_at->copy();
                }
                $lastLogin = $log->created_at->copy();
                $openLogin = $log->created_at->copy();
            }

            if ($log->action === 'Logout' && $openLogin) {
                $sessionMinutes += max(0, $openLogin->diffInMinutes($log->created_at));
                $lastLogout = $log->created_at->copy();
                $openLogin = null;
            }
        }

        $isLoggedIn = $openLogin !== null;

        if ($isLoggedIn) {
            $sessionMinutes += max(0, $openLogin->diffInMinutes(now()));
        }

        return [
            'first_login_at' => $firstLogin,
            'last_login_at' => $lastLogin,
            'last_logout_at' => $lastLogout,
            'is_logged_in' => $isLoggedIn,
            'session_minutes' => $sessionMinutes,
        ];
    }

    private function breaksForToday(Counselor $counselor, Carbon $todayStart, Carbon $todayEnd)
    {
        return CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->where(function ($query) use ($todayStart, $todayEnd) {
                $query->whereBetween('started_at', [$todayStart, $todayEnd])
                    ->orWhereBetween('requested_at', [$todayStart, $todayEnd]);
            })
            ->orderByRaw('COALESCE(started_at, requested_at) ASC')
            ->get();
    }

    private function repairTimedBreaks(Counselor $counselor): void
    {
        CounselorBreak::query()
            ->where('counselor_id', $counselor->id)
            ->whereNotNull('started_at')
            ->whereDate('started_at', today())
            ->get()
            ->each(function (CounselorBreak $break) {
                $scheduledEnd = $break->duration_minutes
                    ? $break->started_at->copy()->addMinutes($break->duration_minutes)
                    : null;

                if (!$break->ended_at) {
                    return;
                }

                if ($break->ended_at->lt($break->started_at)) {
                    $break->update([
                        'ended_at' => $scheduledEnd && now()->gte($scheduledEnd)
                            ? $scheduledEnd
                            : $break->started_at->copy()->addMinute(),
                    ]);
                    return;
                }

                if (!$scheduledEnd || !$break->ended_at->equalTo($break->started_at)) {
                    return;
                }

                if (now()->gte($scheduledEnd)) {
                    $break->update(['ended_at' => $scheduledEnd]);
                    return;
                }

                $break->update(['ended_at' => $break->started_at->copy()->addMinute()]);
            });
    }

    private function breakDurationMinutes(CounselorBreak $break): int
    {
        if (!$break->started_at) {
            return 0;
        }

        $scheduledEnd = $break->duration_minutes
            ? $break->started_at->copy()->addMinutes($break->duration_minutes)
            : null;

        if (!$break->ended_at) {
            $end = now();
            if ($scheduledEnd && $end->gte($scheduledEnd)) {
                return (int) $break->duration_minutes;
            }

            return max(0, (int) $break->started_at->diffInMinutes($end));
        }

        $end = $break->ended_at;
        if ($scheduledEnd && $end->gt($scheduledEnd)) {
            $end = $scheduledEnd;
        }
        if ($end->lt($break->started_at)) {
            $end = $break->started_at->copy()->addMinute();
        }

        return max(0, (int) $break->started_at->diffInMinutes($end));
    }

    private function formatBreak(CounselorBreak $break): array
    {
        $durationMinutes = $this->breakDurationMinutes($break);
        $scheduledEnd = ($break->isActive() && $break->duration_minutes && $break->started_at)
            ? $break->started_at->copy()->addMinutes($break->duration_minutes)
            : null;

        return [
            'id' => $break->id,
            'type' => $break->type,
            'label' => $break->label,
            'duration_minutes' => $break->duration_minutes,
            'started_at' => $break->started_at?->format('h:i A'),
            'requested_at' => $break->requested_at?->format('h:i A'),
            'ended_at' => $break->ended_at?->format('h:i A'),
            'ends_at' => $scheduledEnd?->format('h:i A'),
            'ends_at_iso' => $scheduledEnd?->toIso8601String(),
            'is_active' => $break->isActive(),
            'is_pending' => $break->isPendingApproval(),
            'elapsed' => $this->formatMinutes($durationMinutes),
            'elapsed_minutes' => $durationMinutes,
            'status' => $this->breakStatusLabel($break),
            'exceeded_duration' => (bool) $break->exceeded_duration,
            'rejected_reason' => $break->rejected_reason,
        ];
    }

    private function breakStatusLabel(CounselorBreak $break): string
    {
        if ($break->isPendingApproval()) {
            return 'Pending Approval';
        }

        if ($break->isRejected()) {
            return 'Rejected';
        }

        if ($break->isActive()) {
            return 'Active';
        }

        return $break->exceeded_duration ? 'Exceeded' : 'Completed';
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $mins);
        }

        return sprintf('%dm', $mins);
    }
}
