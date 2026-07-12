<?php

namespace App\Http\Controllers\Concerns;

use App\Models\StudentPayment;
use App\Services\StudentFeeService;
use Illuminate\Http\Request;
use Illuminate\View\View;

trait ListsStudentFeePayments
{
    protected function studentFeePaymentsIndex(Request $request, string $view, array $extra = []): View
    {
        $query = StudentPayment::with(['student.course', 'counselor'])
            ->where('status', 'paid')
            ->latest('paid_at');

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->purpose);
        }

        if ($request->filled('counselor_id')) {
            $query->where('counselor_id', $request->counselor_id);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('student', function ($studentQuery) use ($q) {
                $studentQuery->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('lead_ref', 'like', "%{$q}%");
            });
        }

        if (!empty($extra['counselor_scope_id'])) {
            $query->where('counselor_id', $extra['counselor_scope_id']);
        }

        $payments = $query->paginate(25)->withQueryString();
        $purposeLabels = StudentFeeService::purposeLabels();

        return view($view, array_merge($extra, compact('payments', 'purposeLabels')));
    }
}
