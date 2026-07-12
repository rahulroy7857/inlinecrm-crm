<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Concerns\ListsStudentFeePayments;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentFeePaymentController extends Controller
{
    use ListsStudentFeePayments;

    public function index(Request $request)
    {
        return $this->studentFeePaymentsIndex($request, 'counselor.student-fee-payments.index', [
            'counselor_scope_id' => auth()->guard('counselor')->id(),
        ]);
    }
}
