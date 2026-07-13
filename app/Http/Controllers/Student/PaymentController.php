<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentFeeService;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(
        private StudentFeeService $feeService
    ) {}

    public function index()
    {
        $student = Auth::guard('student')->user();
        $student->load(['payments' => fn ($q) => $q->latest(), 'counselor']);
        $summary = $this->feeService->feeSummary($student);
        $purposeLabels = StudentFeeService::purposeLabels();

        return view('student.payment.index', compact(
            'student',
            'summary',
            'purposeLabels'
        ));
    }
}
