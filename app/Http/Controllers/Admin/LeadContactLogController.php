<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadContactLog; 
use App\Services\ActivityLogger;
use App\Models\Timeline; // Assuming you have a Timeline model
use App\Models\Lead; // Assuming you have a Lead model

class LeadContactLogController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lead_id'      => 'required|exists:leads,id', // Assuming you have a leads table
            'contact_date' => 'required|date',
            'remark'       => 'nullable|string|max:1000',
            'duration'     => 'nullable|integer|min:1',
            'type'        => 'required|string|in:Call,Email,SMS,WhatsApp,In-Person,Other',
            'contacted_by' => 'nullable|string|max:255',
            'status'      => 'required|string|max:30',
            'response_type' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            $leadId = $request->input('lead_id');
            $redirectUrl = '/admin/lead-profile/' . $leadId . '#call-log';
            return redirect($redirectUrl)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $validated['contact_date'] = now();
        LeadContactLog::create($validated);

        // Get the lead
        $lead = Lead::findOrFail($validated['lead_id']);
        
        // Store old status for timeline
        $oldStatus = $lead->status;

        // Create timeline entry for status change if status was changed
        if ($oldStatus !== $request->status) {
            $lead->update([
                'status' => $request->status,
                'next_follow_up' => $request->contact_date,
            ]);
            Timeline::create([
                'lead_id'     => $validated['lead_id'],
                'title'       => 'Status Updated',
                'description' => "Status changed from {$oldStatus} to {$request->status}",
                'event_type'  => 'status_change',
                ...Timeline::performerAttributes(auth()->guard('admin')->user()),
                'event_date'  => now(),
            ]);

            // Log status change activity
            ActivityLogger::log(
                "Updated lead status from {$oldStatus} to {$request->status}",
                'Update',
                auth()->guard('admin')->user(),
                [
                    'lead_id' => $validated['lead_id'],
                    'old_status' => $oldStatus,
                    'new_status' => $request->status
                ]
            );
        } else {
            // Update lead status and next followup
            $lead->update([
                'next_follow_up' => $request->contact_date,
            ]);
        }

        // Add a timeline entry for the contact log
        Timeline::create([
            'lead_id'     => $validated['lead_id'],
            'title'       => "Contacted via {$validated['type']}",
            'description' => "Contacted by: " . ($validated['contacted_by'] ?? 'N/A') . ". Remark: " . ($validated['remark'] ?? ''),
            'event_type'  => 'followup',
            ...Timeline::performerAttributes(auth()->guard('admin')->user()),
            'event_date'  => now(),
        ]);

        // Log the activity
        ActivityLogger::log(
            "Added contact log for lead ID: {$validated['lead_id']}",
            'Create',
            auth()->guard('admin')->user(),
            ['lead_id' => $validated['lead_id'], 'contact_log' => $validated]
        );

        return redirect('/admin/lead-profile/' . $validated['lead_id'] . '#call-log')->with('success', 'Contact log added successfully.');
    }

    public function update(Request $request, $id)
    {
        $contactLog = LeadContactLog::findOrFail($id);
        $field = $request->name;
        $value = $request->value;

        $oldValue = $contactLog->$field;
        $contactLog->$field = $value;
        $contactLog->save();

        ActivityLogger::log(
            "Updated contact log ID: {$contactLog->id}'s {$field}",
            'Update',
            auth()->guard('admin')->user(),
            [
            'contact_log_id' => $contactLog->id,
            'lead_id' => $contactLog->lead_id,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $value
            ]
        );

        return response()->json(['success' => true]);
    }


    public function destroy($id)
    {
        $contactLog = LeadContactLog::findOrFail($id);
        $leadId = $contactLog->lead_id;

        // Delete the contact log record
        $contactLog->delete();

        // Log the activity
        ActivityLogger::log(
            "Deleted contact log ID: {$id} for lead ID: {$leadId}",
            'Delete',
            auth()->guard('admin')->user(),
            [
            'lead_id' => $leadId,
            'contact_log_id' => $id,
            'contact_log' => $contactLog->toArray()
            ]
        );

        return redirect('/admin/lead-profile/' . $leadId . '#call-log')->with('success', 'Contact log deleted successfully.');
    }
}
