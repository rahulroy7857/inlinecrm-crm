<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Account\Concerns\ManagesAccountPortal;
use App\Models\Lead;
use App\Models\LeadPayment;
use App\Models\LedgerAccount;
use App\Services\ActivityLogger;
use App\Services\LeadPaymentService;
use Illuminate\Http\Request;

class LeadPaymentController extends Controller
{
    use ManagesAccountPortal;

    public function index(Request $request)
    {
        $payments = LeadPayment::with(['lead', 'accountTransaction.ledgerAccount'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->paginate(25);

        $ledgerAccounts = LedgerAccount::where('status', 'Active')->orderBy('name')->get();

        return view('account.lead-payments.index', compact('payments', 'ledgerAccounts'));
    }

    public function searchLeads(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        $leads = Lead::select('id', 'name', 'lead_id', 'mobile')
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('lead_id', 'like', "%{$term}%")
                        ->orWhere('mobile', 'like', "%{$term}%");
                });
            })
            ->orderByDesc('updated_at')
            ->limit(25)
            ->get();

        return response()->json([
            'results' => $leads->map(fn ($lead) => [
                'id' => $lead->id,
                'text' => trim("{$lead->lead_id} — {$lead->name}" . ($lead->mobile ? " ({$lead->mobile})" : '')),
            ]),
        ]);
    }

    public function store(Request $request, LeadPaymentService $paymentService)
    {
        $this->authorizeAccountManage();

        $isOtherTxn = (string) $request->input('transaction_type') === '7';
        $isOtherType = $request->input('payment_type') === 'Other';
        $isOther = $isOtherTxn || $isOtherType;

        $validated = $request->validate([
            'lead_id' => ($isOther ? 'nullable' : 'required') . '|exists:leads,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_type' => 'required|string',
            'transaction_type' => 'required|integer|between:1,7',
            'payment_mode' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:1000',
            'transaction_other_message' => ($isOtherTxn ? 'required' : 'nullable') . '|string|max:2000',
            'payment_type_other_message' => ($isOtherType ? 'required' : 'nullable') . '|string|max:2000',
            'ledger_account_id' => 'required|exists:ledger_accounts,id',
        ]);

        $validated['remarks'] = $isOther ? null : $request->input('remarks');
        $validated['transaction_other_message'] = $isOtherTxn ? $request->input('transaction_other_message') : null;
        $validated['payment_type_other_message'] = $isOtherType ? $request->input('payment_type_other_message') : null;
        $validated['lead_id'] = $isOther ? ($validated['lead_id'] ?? null) : $validated['lead_id'];

        $payment = $paymentService->create(
            $validated,
            (int) $validated['ledger_account_id'],
            $this->accountCreatedById()
        );

        ActivityLogger::log(
            $validated['lead_id']
                ? "Added lead payment for lead ID: {$validated['lead_id']}"
                : 'Added other lead payment (no lead)',
            'Create',
            $this->accountActor(),
            ['lead_payment_id' => $payment->id, 'payment' => $validated]
        );

        return redirect()
            ->route(account_route_prefix() . '.lead-payments.index')
            ->with('success', 'Payment recorded successfully.');
    }
}
