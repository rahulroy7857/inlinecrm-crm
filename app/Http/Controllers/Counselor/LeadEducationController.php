<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadEducation; // Assuming you have a LeadEducation model
use App\Services\ActivityLogger;
use App\Models\Timeline; // Assuming you have a Timeline model

class LeadEducationController extends Controller
{
    
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lead_id'      => 'required|exists:leads,id', // Assuming you have a leads table
            'qualification' => 'required|string|max:255',
            'marks'        => 'required|string|max:100',
            'institute'    => 'required|string|max:255',
            'year'         => 'required|integer|min:2010|max:' . date('Y'),
        ]);

        // Removed the after callback to prevent infinite recursion

        if ($validator->fails()) {
            $leadId = $request->input('lead_id');
            $redirectUrl = '/counselor/lead-profile/' . $leadId . '#education';
            return redirect($redirectUrl)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        // Assuming you have a LeadEducation model and a relationship with Lead
        LeadEducation::create([
            'lead_id'      => $validated['lead_id'],
            'qualification'=> $validated['qualification'],
            'marks'        => $validated['marks'],
            'institute'    => $validated['institute'],
            'year'         => $validated['year'],
        ]);

        // Add a timeline entry for the contact log
        Timeline::create([
            'lead_id'     => $validated['lead_id'],
            'title'       => "Added education: {$validated['qualification']}",
            'description' => "Institute: {$validated['institute']}, Marks: {$validated['marks']}, Year: {$validated['year']}",
            'event_type'  => 'education',
            ...Timeline::performerAttributes(auth()->guard('counselor')->user()),
            'event_date'  => now(),
        ]);

        // Log the activity
        ActivityLogger::log(
            "Added education for lead ID: {$validated['lead_id']}",
            'Create',
            auth()->guard('counselor')->user(),
            ['lead_id' => $validated['lead_id'], 'education' => $validated]
        );

        return redirect('/counselor/lead-profile/' . $validated['lead_id'] . '#education')->with('success', 'Education added successfully.');
    }

    public function update(Request $request, $id)
    {
        $education = LeadEducation::findOrFail($id);
        $field = $request->name;
        $value = $request->value;

        $oldValue = $education->$field;
        $education->$field = $value;
        $education->save();

        ActivityLogger::log(
            "Updated education ID: {$education->id}'s {$field}",
            'Update',
            auth()->guard('counselor')->user(),
            [
            'education_id' => $education->id,
            'lead_id' => $education->lead_id,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $value
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $education = LeadEducation::findOrFail($id);
        $leadId = $education->lead_id;

        // Delete the education record
        $education->delete();

        // Add a timeline entry for the contact log
        Timeline::create([
            'lead_id'     => $leadId,
            'title'       => "Deleted education: {$education->qualification}",
            'description' => "Institute: {$education->institute}, Marks: {$education->marks}, Year: {$education->year}",
            'event_type'  => 'education',
            ...Timeline::performerAttributes(auth()->guard('counselor')->user()),
            'event_date'  => now(),
        ]);

        // Log the activity
        ActivityLogger::log(
            "Deleted education ID: {$id} for lead ID: {$leadId}",
            'Delete',
            auth()->guard('counselor')->user(),
            ['lead_id' => $leadId, 'education_id' => $id]
        );

        return redirect('/counselor/lead-profile/' . $leadId . '#education')->with('success', 'Education deleted successfully.');
    }
}
