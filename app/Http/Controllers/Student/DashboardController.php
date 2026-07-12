<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentFeeService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private StudentFeeService $feeService
    ) {}

    public function index()
    {
        $student = Auth::guard('student')->user();
        $student->load(['course', 'counselor', 'lead', 'documents', 'payments']);
        $feeSummary = $this->feeService->feeSummary($student);

        return view('student.dashboard', compact('student', 'feeSummary'));
    }
}
