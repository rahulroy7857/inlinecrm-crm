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
        $isOtherTxn = (string) $request->input('transaction_type') === '7';
        $isOtherType = $request->input('payment_type') === 'Other';
        $isOther = $isOtherTxn || $isOtherType;

        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_type' => 'required|string',
            'transaction_type' => 'required|integer|between:1,7',
            'payment_mode' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:1000',
            'transaction_other_message' => ($isOtherTxn ? 'required' : 'nullable') . '|string|max:2000',
            'payment_type_other_message' => ($isOtherType ? 'required' : 'nullable') . '|string|max:2000',
            'ledger_account_id' => 'nullable|exists:ledger_accounts,id',
        ]);

        $validated['remarks'] = $isOther ? null : $request->input('remarks');
        $validated['transaction_other_message'] = $isOtherTxn ? $request->input('transaction_other_message') : null;
        $validated['payment_type_other_message'] = $isOtherType ? $request->input('payment_type_other_message') : null;
        $ledgerAccountId = $request->filled('ledger_account_id') ? (int) $request->ledger_account_id : null;

        $paymentService->create($validated, $ledgerAccountId);

        $extraNotes = collect([
            $validated['transaction_other_message'] ?? null,
            $validated['payment_type_other_message'] ?? null,
            $validated['remarks'] ?? null,
        ])->filter()->implode(' | ');

        Timeline::create([
            'lead_id' => $validated['lead_id'],
            'title' => "Added payment: {$validated['payment_type']}",
            'description' => "Amount: {$validated['amount']}, Mode: {$validated['payment_mode']}" . ($extraNotes !== '' ? ", Notes: {$extraNotes}" : ''),
            'event_type' => 'payment',
            ...Timeline::performerAttributes(auth()->guard('admin')->user()),
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
