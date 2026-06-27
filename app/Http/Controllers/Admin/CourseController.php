<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::orderBy('name', 'asc')->get();
        return view('admin.settings.courses', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:courses',
            'status' => 'required|in:Active,Inactive'
        ]);

        $course = Course::create($request->all());

        // Log the activity
        ActivityLogger::log(
            "Created new course: {$course->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['course' => $course->toArray()]
        );

        return redirect()->back()->with('success', 'Course added successfully!');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:courses,name,'.$id,
            'status' => 'required|in:Active,Inactive'
        ]);

        $oldData = $course->toArray();

        $course->update($request->all());

        // Log the activity
        ActivityLogger::log(
            "Updated course: {$course->name}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $course->toArray()
            ]
        );

        return redirect()->back()->with('success', 'Course updated successfully!');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $courseData = $course->toArray();
        $course->delete();

        // Log the activity
        ActivityLogger::log(
            "Deleted course: {$course->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['course' => $courseData]
        );

        return redirect()->back()->with('success', 'Course deleted successfully!');
    }
}