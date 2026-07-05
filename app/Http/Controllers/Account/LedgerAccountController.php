<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\LedgerAccount;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class LedgerAccountController extends Controller
{
    use ManagesAccountPortal;

    public function index()
    {
        $accounts = LedgerAccount::orderBy('type')->orderBy('name')->get();

        return view('account.ledger-accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $this->authorizeAccountManage();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',            
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        $account = LedgerAccount::create($data);

        ActivityLogger::log(
            "Created ledger account: {$account->name}",
            'Create',
            $this->accountActor(),
            ['ledger_account' => $account->toArray()]
        );

        return redirect()->back()->with('success', 'Account created successfully.');
    }

    public function update(Request $request, $id)
    {
        $this->authorizeAccountManage();

        $account = LedgerAccount::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,cash',
            'account_number' => 'nullable|string|max:50',
            'bank_name' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:20',
            'opening_balance' => 'required|numeric',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string',
        ]);

        $old = $account->toArray();
        $account->update($data);

        ActivityLogger::log(
            "Updated ledger account: {$account->name}",
            'Update',
            $this->accountActor(),
            ['old' => $old, 'new' => $account->toArray()]
        );

        return redirect()->back()->with('success', 'Account updated successfully.');
    }

    public function destroy($id)
    {
        $this->authorizeAccountManage();

        $account = LedgerAccount::findOrFail($id);

        if ($account->transactions()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete account with existing transactions.');
        }

        $data = $account->toArray();
        $account->delete();

        ActivityLogger::log(
            "Deleted ledger account: {$account->name}",
            'Delete',
            $this->accountActor(),
            ['ledger_account' => $data]
        );

        return redirect()->back()->with('success', 'Account deleted successfully.');
    }
}
