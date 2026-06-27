<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('holiday_date')->get();
        return view('admin.settings.holidays', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        $holiday = Holiday::create($request->all());
        // Log the activity
        ActivityLogger::log(
            "Created new holiday: {$holiday->title}",
            'Create',
            auth()->guard('admin')->user(),
            ['holiday' => $holiday->toArray()]
        );
        return redirect()->back()->with('success', 'Holiday added successfully!');
    }

    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'holiday_date' => 'required|date',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);
        
        $oldData = $holiday->toArray();
        $holiday->update($request->all());

        // Log the activity
        ActivityLogger::log(
            "Updated holiday: {$holiday->title}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $holiday->toArray()
            ]
        );
        return redirect()->back()->with('success', 'Holiday updated successfully!');
    }

    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holidayData = $holiday->toArray();
        $holiday->delete();

        // Log the activity
        ActivityLogger::log(
            "Deleted holiday: {$holiday->title}",
            'Delete',
            auth()->guard('admin')->user(),
            ['holiday' => $holidayData]
        );

        return redirect()->back()->with('success', 'Holiday deleted successfully!');
    }
}
