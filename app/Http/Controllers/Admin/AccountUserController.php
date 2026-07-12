<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\AccountWorkingHoursService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountUserController extends Controller
{
    public function __construct(
        private AccountWorkingHoursService $workingHoursService
    ) {}

    public function index()
    {
        $accounts = Account::orderBy('name')->get();
        $pendingBreakRequests = $this->workingHoursService->pendingBreakRequests();

        return view('admin.users.account', compact('accounts', 'pendingBreakRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accounts',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,accountant,viewer',
            'status' => 'required|boolean',
        ]);

        $account = Account::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        ActivityLogger::log(
            "Created account user: {$account->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['account' => $account->makeHidden(['password'])->toArray()]
        );

        return redirect()->back()->with('success', 'Account user added successfully.');
    }

    public function update(Request $request, $id)
    {
        $account = Account::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:accounts,email,' . $id,
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,accountant,viewer',
            'status' => 'required|boolean',
        ]);

        $oldData = $account->makeHidden(['password'])->toArray();

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'role' => $request->role,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $account->update($updateData);

        ActivityLogger::log(
            "Updated account user: {$account->name}",
            'Update',
            auth()->guard('admin')->user(),
            ['old' => $oldData, 'new' => $account->makeHidden(['password'])->toArray()]
        );

        return redirect()->back()->with('success', 'Account user updated successfully.');
    }

    public function destroy($id)
    {
        $account = Account::findOrFail($id);
        $accountData = $account->makeHidden(['password'])->toArray();
        $account->delete();

        ActivityLogger::log(
            "Deleted account user: {$account->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['account' => $accountData]
        );

        return redirect()->back()->with('success', 'Account user deleted successfully.');
    }

    public function unlockBreakLogin($id)
    {
        $account = Account::findOrFail($id);

        if (!$account->isBreakLoginLocked()) {
            return redirect()->back()->with('error', 'This account user is not locked for break overtime.');
        }

        $this->workingHoursService->unlockBreakLogin(
            $account,
            auth()->guard('admin')->id()
        );

        ActivityLogger::log(
            "Granted break login permission: {$account->name}",
            'Break Unlock',
            auth()->guard('admin')->user(),
            ['account_id' => $account->id]
        );

        return redirect()->back()->with('success', "Login permission granted for {$account->name}.");
    }
}
