<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ListsStudentFeePayments;
use App\Http\Controllers\Controller;
use App\Models\Counselor;
use Illuminate\Http\Request;

class StudentFeePaymentController extends Controller
{
    use ListsStudentFeePayments;

    public function index(Request $request)
    {
        return $this->studentFeePaymentsIndex($request, 'admin.student-fee-payments.index', [
            'counselors' => Counselor::orderBy('name')->get(['id', 'name']),
        ]);
    }
}
