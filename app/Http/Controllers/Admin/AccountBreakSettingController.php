<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountBreak;
use App\Services\AccountBreakSettingsService;
use App\Services\AccountWorkingHoursService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AccountBreakSettingController extends Controller
{
    public function __construct(
        private AccountBreakSettingsService $breakSettings,
        private AccountWorkingHoursService $workingHoursService
    ) {}

    public function index()
    {
        return view('admin.settings.account-breaks', [
            'settings' => $this->breakSettings->all(),
            'pendingRequests' => $this->workingHoursService->pendingBreakRequests(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.type' => 'required|string',
            'settings.*.label' => 'required|string|max:255',
            'settings.*.duration_minutes' => 'nullable|integer|min:1|max:480',
        ]);

        $this->breakSettings->updateSettings($request->input('settings'));

        ActivityLogger::log(
            'Updated account break settings',
            'Update',
            auth()->guard('admin')->user(),
            ['settings_count' => count($request->input('settings'))]
        );

        return redirect()->back()->with('success', 'Account break settings updated successfully.');
    }

    public function approveRequest(Request $request, $id)
    {
        $request->validate([
            'duration_minutes' => 'nullable|integer|min:1|max:480',
        ]);

        $break = AccountBreak::with('account')->findOrFail($id);

        try {
            $this->workingHoursService->approveBreakRequest(
                $break,
                auth()->guard('admin')->id(),
                $request->input('duration_minutes')
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', "Break approved for {$break->account->name}.");
    }

    public function rejectRequest(Request $request, $id)
    {
        $request->validate([
            'rejected_reason' => 'nullable|string|max:500',
        ]);

        $break = AccountBreak::with('account')->findOrFail($id);

        try {
            $this->workingHoursService->rejectBreakRequest(
                $break,
                auth()->guard('admin')->id(),
                $request->input('rejected_reason')
            );
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', "Break request rejected for {$break->account->name}.");
    }
}
