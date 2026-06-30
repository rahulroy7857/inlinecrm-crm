<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\StudentWelcomeMail;
use App\Models\Course;
use App\Models\Lead;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function show(Request $request, ?string $leadRef = null)
    {
        $leadRef = $leadRef ?: $this->resolveLeadRefFromQuery($request);

        if (!$leadRef) {
            return view('student.auth.register', [
                'lead' => null,
                'leadRef' => null,
                'courses' => Course::where('status', 'Active')->orderBy('name')->get(),
                'countries' => countries(),
                'states' => indian_states(),
                'error' => 'Invalid registration link. Please use the link provided by your counselor.',
            ]);
        }

        $lead = Lead::where('lead_id', $leadRef)->first();

        if (!$lead) {
            return view('student.auth.register', [
                'lead' => null,
                'leadRef' => $leadRef,
                'courses' => Course::where('status', 'Active')->orderBy('name')->get(),
                'countries' => countries(),
                'states' => indian_states(),
                'error' => "Lead ID {$leadRef} was not found.",
            ]);
        }

        if (Student::where('lead_id', $lead->id)->exists()) {
            return redirect()->route('student.login')
                ->with('info', 'An account already exists for this lead. Please sign in.');
        }

        return view('student.auth.register', [
            'lead' => $lead,
            'leadRef' => $leadRef,
            'courses' => Course::where('status', 'Active')->orderBy('name')->get(),
            'countries' => countries(),
            'states' => indian_states(),
            'error' => null,
        ]);
    }

    public function store(Request $request, ?string $leadRef = null)
    {
        $leadRef = $leadRef ?: $this->resolveLeadRefFromQuery($request);

        $lead = Lead::where('lead_id', $leadRef)->firstOrFail();

        if (Student::where('lead_id', $lead->id)->exists()) {
            return redirect()->route('student.login')
                ->with('info', 'An account already exists for this lead. Please sign in.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'mobile' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'course_id' => ['required', 'exists:courses,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $plainPassword = $validated['password'];

        $student = Student::create([
            'lead_id' => $lead->id,
            'lead_ref' => $lead->lead_id,
            'counselor_id' => $lead->counselor_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'],
            'country' => $validated['country'],
            'state' => $validated['state'],
            'course_id' => $validated['course_id'],
            'password' => Hash::make($plainPassword),
            'application_status' => 'registered',
            'status' => true,
        ]);

        try {
            Mail::to($student->email)->send(new StudentWelcomeMail($student, $plainPassword));
        } catch (\Throwable $e) {
            report($e);
        }

        auth()->guard('student')->login($student);

        return redirect()->route('student.profile.complete')
            ->with('success', 'Registration successful! Please complete your profile.');
    }

    private function resolveLeadRefFromQuery(Request $request): ?string
    {
        if ($request->filled('lead')) {
            return $request->query('lead');
        }

        foreach ($request->query() as $key => $value) {
            if ($value === null || $value === '') {
                return $key;
            }
        }

        return null;
    }
}
