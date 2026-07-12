<?php

namespace App\Services;

use App\Models\AccountBreakSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AccountBreakSettingsService
{
    public function allActive(): Collection
    {
        return Cache::remember('account_break_settings', 300, function () {
            return AccountBreakSetting::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        });
    }

    public function all(): Collection
    {
        return AccountBreakSetting::query()
            ->orderBy('sort_order')
            ->get();
    }

    public function findByType(string $type): ?AccountBreakSetting
    {
        return $this->allActive()->firstWhere('type', $type)
            ?? AccountBreakSetting::query()->where('type', $type)->first();
    }

    public function validTypes(): array
    {
        return $this->allActive()->pluck('type')->all();
    }

    public function requiresApproval(string $type): bool
    {
        return (bool) $this->findByType($type)?->requires_admin_approval;
    }

    public function durationFor(string $type): ?int
    {
        $minutes = $this->findByType($type)?->duration_minutes;

        return $minutes !== null ? (int) $minutes : null;
    }

    public function labelFor(string $type): string
    {
        return $this->findByType($type)?->label
            ?? ucfirst(str_replace('_', ' ', $type));
    }

    public function breakTypesPayload(): array
    {
        return $this->allActive()->map(function (AccountBreakSetting $setting) {
            return [
                'type' => $setting->type,
                'label' => $setting->label,
                'duration_minutes' => $setting->duration_minutes,
                'duration_label' => $setting->durationLabel(),
                'requires_admin_approval' => $setting->requires_admin_approval,
            ];
        })->values()->all();
    }

    public function updateSettings(array $settings): void
    {
        foreach ($settings as $row) {
            AccountBreakSetting::query()
                ->where('type', $row['type'])
                ->update([
                    'label' => $row['label'],
                    'duration_minutes' => $row['duration_minutes'] !== '' && $row['duration_minutes'] !== null
                        ? (int) $row['duration_minutes']
                        : null,
                    'requires_admin_approval' => (bool) ($row['requires_admin_approval'] ?? false),
                    'is_active' => (bool) ($row['is_active'] ?? true),
                ]);
        }

        Cache::forget('account_break_settings');
    }

    public function clearCache(): void
    {
        Cache::forget('account_break_settings');
    }
}
