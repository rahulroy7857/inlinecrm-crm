<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $amount = config('student.application_fee');
        $gateway = config('student.payment.gateway');
        $testMode = config('student.payment.test_mode');

        return view('student.payment.index', compact('student', 'amount', 'gateway', 'testMode'));
    }

    public function initiate(Request $request)
    {
        $student = Auth::guard('student')->user();
        $amount = config('student.application_fee');

        if ($student->hasPaid()) {
            return redirect()->route('student.payment.index')
                ->with('info', 'Payment already completed.');
        }

        $payment = StudentPayment::create([
            'student_id' => $student->id,
            'amount' => $amount,
            'gateway' => config('student.payment.gateway'),
            'transaction_id' => 'TXN' . strtoupper(Str::random(12)),
            'status' => 'pending',
        ]);

        if (config('student.payment.test_mode')) {
            return redirect()->route('student.payment.callback', [
                'payment' => $payment->id,
                'status' => 'success',
            ]);
        }

        return redirect()->route('student.payment.index')
            ->with('error', 'Live payment gateway is not configured. Enable test mode or configure Razorpay credentials.');
    }

    public function callback(Request $request, int $payment)
    {
        $student = Auth::guard('student')->user();
        $record = StudentPayment::where('id', $payment)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($request->query('status') === 'success') {
            $record->update([
                'status' => 'paid',
                'paid_at' => now(),
                'metadata' => ['mode' => config('student.payment.test_mode') ? 'test' : 'live'],
            ]);

            $student->update([
                'payment_status' => 'paid',
                'payment_amount' => $record->amount,
                'payment_reference' => $record->transaction_id,
                'paid_at' => now(),
            ]);

            return redirect()->route('student.payment.index')
                ->with('success', 'Payment successful! You can now submit your application.');
        }

        $record->update(['status' => 'failed']);

        return redirect()->route('student.payment.index')
            ->with('error', 'Payment failed. Please try again.');
    }
}
