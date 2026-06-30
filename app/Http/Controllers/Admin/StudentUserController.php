<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class StudentUserController extends Controller
{
    public function index()
    {
        $students = Student::with(['lead', 'course', 'counselor'])
            ->orderByDesc('created_at')
            ->get();

        return view('admin.users.student', compact('students'));
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $studentData = $student->makeHidden(['password'])->toArray();
        $student->delete();

        ActivityLogger::log(
            "Deleted student account: {$studentData['name']}",
            'Delete',
            auth()->guard('admin')->user(),
            ['student' => $studentData]
        );

        return redirect()->back()->with('success', 'Student account deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'status' => 'required|boolean',
        ]);

        $student->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Student status updated.');
    }
}
