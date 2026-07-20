<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadContactLog;
use App\Models\Timeline;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadContactLogController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'contact_date' => 'required|date',
            'remark' => 'nullable|string|max:1000',
            'duration' => 'nullable|numeric|min:0.01',
            'type' => 'required|string|in:Call,Email,SMS,WhatsApp,In-Person,Other',
            'contacted_by' => 'nullable|string|max:255',
            'status' => 'required|string|max:30',
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
        $nextFollowUp = Carbon::parse($request->contact_date);
        $admin = auth()->guard('admin')->user();
        $leadId = $validated['lead_id'];

        try {
            DB::transaction(function () use ($validated, $nextFollowUp, $admin, $request, $leadId) {
                $validated['contact_date'] = now();
                LeadContactLog::create($validated);

                $lead = Lead::findOrFail($leadId);
                $oldStatus = $lead->status;

                if ($oldStatus !== $request->status) {
                    $lead->update([
                        'status' => $request->status,
                        'next_follow_up' => $nextFollowUp,
                    ]);

                    Timeline::create([
                        'lead_id' => $leadId,
                        'title' => 'Status Updated',
                        'description' => "Status changed from {$oldStatus} to {$request->status}",
                        'event_type' => 'status_change',
                        ...Timeline::performerAttributes($admin),
                        'event_date' => now(),
                    ]);

                    ActivityLogger::log(
                        "Updated lead status from {$oldStatus} to {$request->status}",
                        'Update',
                        $admin,
                        [
                            'lead_id' => $leadId,
                            'old_status' => $oldStatus,
                            'new_status' => $request->status,
                        ]
                    );
                } else {
                    $lead->update([
                        'next_follow_up' => $nextFollowUp,
                    ]);
                }

                Timeline::create([
                    'lead_id' => $leadId,
                    'title' => "Contacted via {$validated['type']}",
                    'description' => 'Contacted by: ' . ($validated['contacted_by'] ?? 'N/A') . '. Remark: ' . ($validated['remark'] ?? ''),
                    'event_type' => 'followup',
                    ...Timeline::performerAttributes($admin),
                    'event_date' => now(),
                ]);

                ActivityLogger::log(
                    "Added contact log for lead ID: {$leadId}",
                    'Create',
                    $admin,
                    [
                        'lead_id' => $leadId,
                        'contact_log' => $this->serializeForActivityLog($validated),
                    ]
                );
            });
        } catch (\Throwable $exception) {
            report($exception);

            return redirect('/admin/lead-profile/' . $leadId . '#call-log')
                ->withInput()
                ->with('error', 'Unable to save contact log. Please try again or contact support.');
        }

        return redirect('/admin/lead-profile/' . $leadId . '#call-log')
            ->with('success', 'Contact log added successfully.');
    }

    public function update(Request $request, $id)
    {
        $contactLog = LeadContactLog::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|in:remark,duration,type,response_type,contacted_by',
            'value' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        $field = $request->name;
        $value = $request->value;

        $fieldValidator = Validator::make(
            ['value' => $value],
            ['value' => $this->updateFieldRules()[$field]]
        );

        if ($fieldValidator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $fieldValidator->errors()->first('value'),
            ], 422);
        }

        try {
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
                    'new_value' => $value,
                ]
            );
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'error' => 'Unable to update contact log.',
            ], 500);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $contactLog = LeadContactLog::findOrFail($id);
        $leadId = $contactLog->lead_id;

        $contactLog->delete();

        ActivityLogger::log(
            "Deleted contact log ID: {$id} for lead ID: {$leadId}",
            'Delete',
            auth()->guard('admin')->user(),
            [
                'lead_id' => $leadId,
                'contact_log_id' => $id,
                'contact_log' => $contactLog->toArray(),
            ]
        );

        return redirect('/admin/lead-profile/' . $leadId . '#call-log')
            ->with('success', 'Contact log deleted successfully.');
    }

    private function updateFieldRules(): array
    {
        return [
            'remark' => 'nullable|string|max:1000',
            'duration' => 'nullable|numeric|min:0.01',
            'type' => 'required|string|in:Call,Email,SMS,WhatsApp,In-Person,Other',
            'response_type' => 'nullable|string|in:Positive,Negative,Neutral,RNR,Invalid Number',
            'contacted_by' => 'nullable|string|max:255',
        ];
    }

    private function serializeForActivityLog(array $data): array
    {
        return collect($data)
            ->map(function ($value) {
                if ($value instanceof \DateTimeInterface) {
                    return $value->format('Y-m-d H:i:s');
                }

                return $value;
            })
            ->all();
    }
}
