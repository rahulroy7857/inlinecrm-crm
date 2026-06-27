<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use App\Models\College;
use Illuminate\Http\Request;

class CollegeController extends Controller
{
    public function index()
    {
        $colleges = College::all();
        return view('admin.settings.colleges', compact('colleges'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:colleges',
            'place' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive'
        ]);

        $college = College::create($request->all());

        // Log the activity
        ActivityLogger::log(
            "Created new college: {$college->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['college' => $college->toArray()]
        );

        return redirect()->back()->with('success', 'College added successfully!');
    }

    public function update(Request $request, $id)
    {
        $college = College::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:colleges,name,'.$id,
            'place' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive'
        ]);

        $oldData = $college->toArray();

        $college->update($request->all());

        // Log the activity
        ActivityLogger::log(
            "Updated college: {$college->name}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $college->toArray()
            ]
        );

        return redirect()->back()->with('success', 'College updated successfully!');
    }

    public function destroy($id)
    {
        $college = College::findOrFail($id);
        $collegeData = $college->toArray();
        $college->delete();

        // Log the activity
        ActivityLogger::log(
            "Deleted college: {$college->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['college' => $collegeData]
        );
        
        return redirect()->back()->with('success', 'College deleted successfully!');
    }
}