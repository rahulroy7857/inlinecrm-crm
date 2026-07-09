<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeadExam; 
use App\Services\ActivityLogger;
use App\Models\Timeline; // Assuming you have a Timeline model

class LeadExamController extends Controller
{
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'lead_id'      => 'required|exists:leads,id', // Assuming you have a leads table
            'exam_name'    => 'required|string|max:255',
            'score'        => 'required|string|max:100',
            'year'         => 'required|integer|min:2010|max:' . date('Y'),
        ]);

        if ($validator->fails()) {
            $leadId = $request->input('lead_id');
            $redirectUrl = '/admin/lead-profile/' . $leadId . '#exams';
            return redirect($redirectUrl)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();
        $validated['remarks'] = $request->remarks ?? null; // Optional field
        // Assuming you have a LeadExam model and a relationship with Lead
        LeadExam::create([
            'lead_id'      => $validated['lead_id'],
            'exam_name'    => $validated['exam_name'],
            'score'        => $validated['score'],
            'year'         => $validated['year'],
            'remarks'      => $validated['remarks'],
        ]);

        // Add a timeline entry for the contact log
        Timeline::create([
            'lead_id'     => $validated['lead_id'],
            'title'       => "Added exam: {$validated['exam_name']}",
            'description' => "Score: {$validated['score']}, Year: {$validated['year']}" . ($validated['remarks'] ? ", Remarks: {$validated['remarks']}" : ""),
            'event_type'  => 'exam',
            ...Timeline::performerAttributes(auth()->guard('admin')->user()),
            'event_date'  => now(),
        ]);

        // Log the activity
        ActivityLogger::log(
            "Added exam for lead ID: {$validated['lead_id']}",
            'Create',
            auth()->guard('admin')->user(),
            ['lead_id' => $validated['lead_id'], 'exam' => $validated]
        );

        return redirect('/admin/lead-profile/' . $validated['lead_id'] . '#exams')->with('success', 'Exam added successfully.');
    }

    public function update(Request $request, $id)
    {
        $exam = LeadExam::findOrFail($id);
        $field = $request->name;
        $value = $request->value;

        $oldValue = $exam->$field;
        $exam->$field = $value;
        $exam->save();

        ActivityLogger::log(
            "Updated exam ID: {$exam->id}'s {$field}",
            'Update',
            auth()->guard('admin')->user(),
            [
            'exam_id' => $exam->id,
            'lead_id' => $exam->lead_id,
            'field' => $field,
            'old_value' => $oldValue,
            'new_value' => $value
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $exam = LeadExam::findOrFail($id);
        $leadId = $exam->lead_id;

        // Delete the exam record
        $exam->delete();

        Timeline::create([
            'lead_id'     => $leadId,
            'title'       => "Deleted exam: {$exam->exam_name}",
            'description' => "Score: {$exam->score}, Year: {$exam->year}" . ($exam->remarks ? ", Remarks: {$exam->remarks}" : ""),
            'event_type'  => 'exam',
            ...Timeline::performerAttributes(auth()->guard('admin')->user()),
            'event_date'  => now(),
        ]);

        // Log the activity
        ActivityLogger::log(
            "Deleted exam ID: {$id} for lead ID: {$leadId}",
            'Delete',
            auth()->guard('admin')->user(),
            ['lead_id' => $leadId, 'exam_id' => $id]
        );

        return redirect('/admin/lead-profile/' . $leadId . '#exams')->with('success', 'Exam deleted successfully.');
    }
}
