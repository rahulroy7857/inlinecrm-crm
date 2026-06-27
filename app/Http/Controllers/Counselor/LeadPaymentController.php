<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadPayment; 
use App\Services\ActivityLogger;
use App\Models\Timeline; // Assuming you have a Timeline model
use App\Models\Lead; // Assuming you have a Lead model

class LeadPaymentController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'payment_date' => 'required|date',
            'payment_type' => 'required|string',
            'transaction_type' => 'required|integer|between:1,7',
            'payment_mode' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'remark' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            $leadId = $request->input('lead_id');
            $redirectUrl = '/counselor/lead-profile/' . $leadId . '#payments';
            return redirect($redirectUrl)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $validated['remarks'] = $request->remarks ?? null; // Optional field
        // Assuming you have a LeadPayment model and a relationship with Lead
        LeadPayment::create([
            'lead_id'      => $validated['lead_id'],
            'payment_date' => $validated['payment_date'],
            'payment_type' => $validated['payment_type'],
            'transaction_type' => $validated['transaction_type'],
            'payment_mode' => $validated['payment_mode'],
            'amount'      => $validated['amount'],
            'remark'      => $validated['remarks'],
        ]);

        Timeline::create([
            'lead_id'     => $validated['lead_id'],
            'title'       => "Added payment: {$validated['payment_type']}",
            'description' => "Amount: {$validated['amount']}, Mode: {$validated['payment_mode']}" . ($validated['remarks'] ? ", Remarks: {$validated['remarks']}" : ""),
            'event_type'  => 'payment',
            'performed_by'=> auth()->guard('counselor')->id(),
            'event_date'  => now(),
        ]);

        // Log the activity
        ActivityLogger::log(
            "Added payment for lead ID: {$request->input('lead_id')}",
            'Create',
            auth()->guard('counselor')->user(),
            ['lead_id' => $request->input('lead_id'), 'payment' => $request->all()]
        );

        return redirect('/counselor/lead-profile/' . $validated['lead_id'] . '#payments')->with('success', 'Payment added successfully.');
    }
}
