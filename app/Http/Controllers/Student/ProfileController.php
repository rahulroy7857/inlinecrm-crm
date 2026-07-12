<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $student->load(['course', 'counselor', 'lead']);

        return view('student.profile.index', compact('student'));
    }

    public function complete()
    {
        $student = Auth::guard('student')->user();
        $student->load('course');

        return view('student.profile.complete', [
            'student' => $student,
            'states' => indian_states(),
        ]);
    }

    public function updateComplete(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'father_name' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'relation' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'aadhar' => ['nullable', 'string', 'max:20'],
            'present_address' => ['nullable', 'string', 'max:500'],
            'present_city' => ['nullable', 'string', 'max:100'],
            'present_pin' => ['nullable', 'string', 'max:10'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
        ]);

        $student->update(array_merge($validated, [
            'profile_completed' => true,
            'profile_completed_at' => now(),
            'application_status' => 'profile_completed',
        ]));

        return redirect()->route('student.dashboard')
            ->with('success', 'Profile updated successfully.');
    }

    public function update(Request $request)
    {
        $student = Auth::guard('student')->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'father_occupation' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'mother_occupation' => ['nullable', 'string', 'max:255'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'relation' => ['nullable', 'string', 'max:100'],
            'gender' => ['nullable', 'in:Male,Female,Other'],
            'dob' => ['nullable', 'date'],
            'aadhar' => ['nullable', 'string', 'max:20'],
            'present_address' => ['nullable', 'string', 'max:500'],
            'present_city' => ['nullable', 'string', 'max:100'],
            'present_pin' => ['nullable', 'string', 'max:10'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
        ]);

        $student->update($validated);

        return back()->with('success', 'Profile saved successfully.');
    }
}
