<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lead;
use App\Models\Student;
use App\Services\StudentRegistrationOtpService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function __construct(
        private StudentRegistrationOtpService $otpService
    ) {}

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
                'otpPending' => false,
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
                'otpPending' => false,
            ]);
        }

        if (Student::where('lead_id', $lead->id)->exists()) {
            return redirect()->route('student.login')
                ->with('info', 'An account already exists for this student. Please sign in.');
        }

        $otpPending = session()->has('student_registration_otp');

        return view('student.auth.register', [
            'lead' => $lead,
            'leadRef' => $leadRef,
            'courses' => Course::where('status', 'Active')->orderBy('name')->get(),
            'countries' => countries(),
            'states' => indian_states(),
            'error' => null,
            'otpPending' => $otpPending,
            'otpEmail' => session('student_registration_otp.email'),
        ]);
    }

    public function store(Request $request, ?string $leadRef = null)
    {
        $leadRef = $leadRef ?: $this->resolveLeadRefFromQuery($request);
        $lead = Lead::where('lead_id', $leadRef)->firstOrFail();

        if (Student::where('lead_id', $lead->id)->exists()) {
            return redirect()->route('student.login')
                ->with('info', 'An account already exists for this student. Please sign in.');
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

        try {
            $issued = $this->otpService->issue($lead, $validated);
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Unable to send OTP email right now. Please try again.');
        }

        session([
            'student_registration_otp' => [
                'otp_id' => $issued['otp_id'],
                'email' => $issued['email'],
                'lead_ref' => $leadRef,
            ],
        ]);

        return redirect()
            ->route('student.registration.lead', $leadRef)
            ->with('success', 'A 6-digit OTP has been sent to ' . $issued['email'] . '. Enter it below to continue.');
    }

    public function verifyOtp(Request $request, string $leadRef)
    {
        $session = session('student_registration_otp');

        if (!$session || ($session['lead_ref'] ?? null) !== $leadRef) {
            return redirect()->route('student.registration.lead', $leadRef)
                ->with('error', 'Please complete registration first to receive an OTP.');
        }

        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        try {
            $student = $this->otpService->verify((int) $session['otp_id'], $validated['otp']);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        session()->forget('student_registration_otp');
        auth()->guard('student')->login($student);

        return redirect()->route('student.dashboard')
            ->with('success', 'Email verified! Welcome to your student panel.');
    }

    public function resendOtp(Request $request, string $leadRef)
    {
        $session = session('student_registration_otp');

        if (!$session || ($session['lead_ref'] ?? null) !== $leadRef) {
            return redirect()->route('student.registration.lead', $leadRef)
                ->with('error', 'Please complete registration first.');
        }

        $lead = Lead::where('lead_id', $leadRef)->firstOrFail();
        $otp = \App\Models\StudentOtp::find($session['otp_id']);

        if (!$otp || empty($otp->payload)) {
            return redirect()->route('student.registration.lead', $leadRef)
                ->with('error', 'OTP session expired. Please register again.');
        }

        try {
            $issued = $this->otpService->issue($lead, $otp->payload);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Unable to resend OTP. Please try again.');
        }

        session([
            'student_registration_otp' => [
                'otp_id' => $issued['otp_id'],
                'email' => $issued['email'],
                'lead_ref' => $leadRef,
            ],
        ]);

        return back()->with('success', 'A new OTP has been sent to ' . $issued['email'] . '.');
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
