<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timeline;
use App\Services\ActivityLogger;
use App\Services\LeadPaymentService;

class LeadPaymentController extends Controller
{
    public function store(Request $request, LeadPaymentService $paymentService)
    {
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_type' => 'required|string',
            'transaction_type' => 'required|integer|between:1,7',
            'payment_mode' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:255',
            'ledger_account_id' => 'nullable|exists:ledger_accounts,id',
        ]);

        $validated['remarks'] = $request->input('remarks');
        $ledgerAccountId = $request->filled('ledger_account_id') ? (int) $request->ledger_account_id : null;

        $paymentService->create($validated, $ledgerAccountId);

        Timeline::create([
            'lead_id' => $validated['lead_id'],
            'title' => "Added payment: {$validated['payment_type']}",
            'description' => "Amount: {$validated['amount']}, Mode: {$validated['payment_mode']}" . ($validated['remarks'] ? ", Remarks: {$validated['remarks']}" : ''),
            'event_type' => 'payment',
            'performed_by' => auth()->guard('admin')->id(),
            'event_date' => now(),
        ]);

        ActivityLogger::log(
            "Added payment for lead ID: {$request->input('lead_id')}",
            'Create',
            auth()->guard('admin')->user(),
            ['lead_id' => $request->input('lead_id'), 'payment' => $request->all()]
        );

        return redirect('/admin/lead-profile/' . $validated['lead_id'] . '#payments')->with('success', 'Payment added successfully.');
    }
}
