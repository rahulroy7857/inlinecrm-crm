<?php

namespace App\Services;

use App\Mail\StudentPaymentDueMail;
use App\Mail\StudentPaymentReceiptMail;
use App\Models\AccountTransaction;
use App\Models\LeadPayment;
use App\Models\LedgerAccount;
use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StudentFeeService
{
    public const PURPOSE_REGISTRATION = 'registration_fee';
    public const PURPOSE_APPLICATION = 'application_fee';
    public const PURPOSE_COUNSELOR = 'counselor_fee';
    public const PURPOSE_COLLEGE = 'college_fee';

    public static function purposeLabels(): array
    {
        return [
            self::PURPOSE_REGISTRATION => 'Registration Fee',
            self::PURPOSE_APPLICATION => 'Application Fee',
            self::PURPOSE_COUNSELOR => 'Processing Fee',
            self::PURPOSE_COLLEGE => 'College Fee',
        ];
    }

    public static function registrationPlans(): array
    {
        $gst = (float) config('student.gst_percent', 18);
        $plans = [];

        foreach (config('student.registration_fee_plans', []) as $key => $plan) {
            $base = (float) ($plan['base'] ?? 0);
            $gstAmount = round($base * $gst / 100, 2);
            $total = round($base + $gstAmount, 2);

            $plans[$key] = [
                'key' => $key,
                'label' => $plan['label'] ?? strtoupper($key),
                'base' => $base,
                'gst_percent' => $gst,
                'gst_amount' => $gstAmount,
                'total' => $total,
                'refundable' => (bool) ($plan['refundable'] ?? false),
            ];
        }

        return $plans;
    }

    public static function registrationPlanTotal(string $planKey): ?float
    {
        $plans = self::registrationPlans();

        return isset($plans[$planKey]) ? (float) $plans[$planKey]['total'] : null;
    }

    public function feeSummary(Student $student): array
    {
        $registrationPaid = $this->paidTotal($student, self::PURPOSE_REGISTRATION);
        $counselorPaid = $this->paidTotal($student, self::PURPOSE_COUNSELOR);
        $collegePaid = $this->paidTotal($student, self::PURPOSE_COLLEGE);
        $applicationPaid = $this->paidTotal($student, self::PURPOSE_APPLICATION);

        $registrationFee = (float) ($student->registration_fee ?? 0);
        $counselorFee = (float) ($student->counselor_fee ?? 0);
        $collegeFee = (float) ($student->college_fee ?? 0);
        $applicationFee = (float) config('student.application_fee', 0);

        $registrationRemaining = max(0, round($registrationFee - $registrationPaid, 2));
        $counselorRemaining = max(0, round($counselorFee - $counselorPaid, 2));
        $collegeRemaining = max(0, round($collegeFee - $collegePaid, 2));
        $applicationRemaining = max(0, round($applicationFee - $applicationPaid, 2));

        $registrationComplete = $registrationFee > 0 && $registrationRemaining <= 0;
        $plans = self::registrationPlans();
        $planMeta = $student->registration_fee_plan
            ? ($plans[$student->registration_fee_plan] ?? null)
            : null;

        return [
            'registration_fee' => $registrationFee,
            'registration_fee_plan' => $student->registration_fee_plan,
            'registration_plan' => $planMeta,
            'counselor_fee' => $counselorFee,
            'college_fee' => $collegeFee,
            'application_fee' => $applicationFee,
            'registration_paid' => $registrationPaid,
            'counselor_paid' => $counselorPaid,
            'college_paid' => $collegePaid,
            'application_paid' => $applicationPaid,
            'registration_remaining' => $registrationRemaining,
            'counselor_remaining' => $counselorRemaining,
            'college_remaining' => $collegeRemaining,
            'application_remaining' => $applicationRemaining,
            'total_fee' => round($registrationFee + $counselorFee + $collegeFee, 2),
            'total_paid' => round($registrationPaid + $counselorPaid + $collegePaid, 2),
            'total_remaining' => round($registrationRemaining + $counselorRemaining + $collegeRemaining, 2),
            'registration_complete' => $registrationComplete,
            'counselor_complete' => $counselorFee > 0 && $counselorRemaining <= 0,
            'college_complete' => $collegeFee > 0 && $collegeRemaining <= 0,
            'registration_required_first' => $registrationFee > 0 && !$registrationComplete,
            'fees_set' => $registrationFee > 0 || $counselorFee > 0 || $collegeFee > 0,
            'registration_fee_due_date' => $student->registration_fee_due_date,
            'counselor_fee_due_date' => $student->counselor_fee_due_date,
            'college_fee_due_date' => $student->college_fee_due_date,
            'dues' => $this->dueItems(
                $student,
                $registrationRemaining,
                $counselorRemaining,
                $collegeRemaining
            ),
        ];
    }

    public function paidTotal(Student $student, string $purpose): float
    {
        return (float) $student->payments()
            ->where('purpose', $purpose)
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function remainingFor(Student $student, string $purpose): float
    {
        $summary = $this->feeSummary($student);

        return match ($purpose) {
            self::PURPOSE_REGISTRATION => $summary['registration_remaining'],
            self::PURPOSE_COUNSELOR => $summary['counselor_remaining'],
            self::PURPOSE_COLLEGE => $summary['college_remaining'],
            self::PURPOSE_APPLICATION => $summary['application_remaining'],
            default => 0,
        };
    }

    public function dueItems(
        Student $student,
        ?float $registrationRemaining = null,
        ?float $counselorRemaining = null,
        ?float $collegeRemaining = null
    ): array
    {
        $summaryRegistration = $registrationRemaining;
        $summaryCounselor = $counselorRemaining;
        $summaryCollege = $collegeRemaining;

        if ($summaryRegistration === null || $summaryCounselor === null || $summaryCollege === null) {
            $summary = $this->feeSummary($student);
            $summaryRegistration = $summary['registration_remaining'];
            $summaryCounselor = $summary['counselor_remaining'];
            $summaryCollege = $summary['college_remaining'];
        }

        $dues = [];
        $today = now()->startOfDay();

        if ($summaryRegistration > 0 && $student->registration_fee_due_date) {
            $due = $student->registration_fee_due_date->copy()->startOfDay();
            if ($due->lessThanOrEqualTo($today->copy()->addDays(7))) {
                $dues[] = [
                    'purpose' => self::PURPOSE_REGISTRATION,
                    'label' => 'Registration Fee',
                    'remaining' => $summaryRegistration,
                    'due_date' => $student->registration_fee_due_date,
                    'is_overdue' => $due->lessThan($today),
                ];
            }
        }

        if ($summaryCounselor > 0 && $student->counselor_fee_due_date) {
            $due = $student->counselor_fee_due_date->copy()->startOfDay();
            if ($due->lessThanOrEqualTo($today->copy()->addDays(7))) {
                $dues[] = [
                    'purpose' => self::PURPOSE_COUNSELOR,
                    'label' => 'Processing Fee',
                    'remaining' => $summaryCounselor,
                    'due_date' => $student->counselor_fee_due_date,
                    'is_overdue' => $due->lessThan($today),
                ];
            }
        }

        if ($summaryCollege > 0 && $student->college_fee_due_date) {
            $due = $student->college_fee_due_date->copy()->startOfDay();
            if ($due->lessThanOrEqualTo($today->copy()->addDays(7))) {
                $dues[] = [
                    'purpose' => self::PURPOSE_COLLEGE,
                    'label' => 'College Fee',
                    'remaining' => $summaryCollege,
                    'due_date' => $student->college_fee_due_date,
                    'is_overdue' => $due->lessThan($today),
                ];
            }
        }

        return $dues;
    }

    public function setFees(Student $student, array $data, ?int $accountUserId = null, ?int $counselorId = null): Student
    {
        $attributes = [
            'fees_set_at' => now(),
            'fees_set_by' => $counselorId,
            'fees_set_by_account_id' => $accountUserId,
        ];

        foreach ([
            'counselor_fee',
            'college_fee',
            'registration_fee_due_date',
            'counselor_fee_due_date',
            'college_fee_due_date',
            'fee_ledger_account_id',
        ] as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        // The registration plan is owned by the counselor. Only touch it when
        // it is explicitly provided; otherwise keep whatever the counselor set.
        if (array_key_exists('registration_fee_plan', $data)) {
            $planKey = $data['registration_fee_plan'] ?: null;
            $attributes['registration_fee_plan'] = $planKey;
            $attributes['registration_fee'] = $this->resolvePlanFee($planKey);
        }

        $student->update($attributes);

        return $student->fresh();
    }

    /**
     * Apply (or clear) the registration plan chosen by the counselor.
     */
    public function applyRegistrationPlan(Student $student, ?string $planKey): Student
    {
        $planKey = $planKey ?: null;

        $student->update([
            'registration_fee_plan' => $planKey,
            'registration_fee' => $this->resolvePlanFee($planKey),
        ]);

        return $student->fresh();
    }

    private function resolvePlanFee(?string $planKey): float
    {
        if (!$planKey) {
            return 0.0;
        }

        $total = self::registrationPlanTotal($planKey);
        if ($total === null) {
            throw new \InvalidArgumentException('Invalid registration fee plan.');
        }

        return (float) $total;
    }

    public function recordInstallment(
        Student $student,
        string $purpose,
        float $amount,
        array $extra = []
    ): StudentPayment {
        $summary = $this->feeSummary($student);

        if (
            $purpose !== self::PURPOSE_REGISTRATION
            && $summary['registration_required_first']
        ) {
            throw new \InvalidArgumentException('Please pay the Registration Fee first before other fees.');
        }

        $remaining = $this->remainingFor($student, $purpose);

        if ($amount <= 0) {
            throw new \InvalidArgumentException('Payment amount must be greater than zero.');
        }

        if ($amount > $remaining + 0.009) {
            throw new \InvalidArgumentException('Payment amount exceeds remaining balance of ₹' . number_format($remaining, 2));
        }

        // Registration fee is fixed — only full remaining amount allowed.
        if ($purpose === self::PURPOSE_REGISTRATION && abs($amount - $remaining) > 0.009) {
            throw new \InvalidArgumentException('Registration fee must be paid in full (₹' . number_format($remaining, 2) . ').');
        }

        $ledgerAccountId = $extra['ledger_account_id']
            ?? $student->fee_ledger_account_id
            ?? $this->resolveDefaultLedgerAccountId();

        $remark = $extra['remark'] ?? null;
        if ($purpose === self::PURPOSE_REGISTRATION && $summary['registration_plan']) {
            $plan = $summary['registration_plan'];
            $remark = trim(($remark ? $remark . ' | ' : '') . $plan['label'] . ' (base ₹' . number_format($plan['base'], 2) . ' + ' . $plan['gst_percent'] . '% GST)');
        }

        return StudentPayment::create([
            'student_id' => $student->id,
            'purpose' => $purpose,
            'counselor_id' => $extra['counselor_id'] ?? $student->counselor_id,
            'ledger_account_id' => $ledgerAccountId,
            'amount' => $amount,
            'gateway' => $extra['gateway'] ?? config('student.payment.gateway', 'razorpay'),
            'transaction_id' => $extra['transaction_id'] ?? ('TXN' . strtoupper(Str::random(12))),
            'status' => 'pending',
            'remark' => $remark,
            'metadata' => array_merge($extra['metadata'] ?? [], [
                'registration_fee_plan' => $student->registration_fee_plan,
            ]),
            'recorded_by_admin_id' => $extra['recorded_by_admin_id'] ?? null,
        ]);
    }

    public function markPaid(StudentPayment $payment, array $metadata = []): StudentPayment
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'metadata' => array_merge($payment->metadata ?? [], $metadata),
        ]);

        $student = $payment->student()->first();
        $this->syncPortalPaymentStatus($student);
        $this->syncToLedgerAccount($payment->fresh(['student', 'counselor']));

        return $payment->fresh(['student', 'counselor', 'ledgerAccount']);
    }

    /**
     * Map CRM lead payment types to student fee purposes.
     * Application / Admission / Reservation reduce Processing Fee (counselor_fee).
     */
    public static function mapLeadPaymentTypeToPurpose(?string $paymentType): ?string
    {
        return match ($paymentType) {
            'Processing Fee', 'Application Fee', 'Reservation Fee' => self::PURPOSE_COUNSELOR,
            'Tuition Fee' => self::PURPOSE_COLLEGE,
            default => null,
        };
    }

    /**
     * Apply a lead payment to the linked student's fee balance and payment history.
     * Does not create a second ledger entry when the lead payment is already booked.
     */
    public function recordFromLeadPayment(LeadPayment $leadPayment, ?int $ledgerAccountId = null): ?StudentPayment
    {
        if (StudentPayment::where('lead_payment_id', $leadPayment->id)->exists()) {
            return StudentPayment::where('lead_payment_id', $leadPayment->id)->first();
        }

        if (! in_array((string) $leadPayment->transaction_type, ['1', '2', '3'], true)) {
            return null;
        }

        $purpose = self::mapLeadPaymentTypeToPurpose($leadPayment->payment_type);
        if (! $purpose) {
            return null;
        }

        $student = Student::where('lead_id', $leadPayment->lead_id)->first();
        if (! $student) {
            return null;
        }

        $feeAmount = match ($purpose) {
            self::PURPOSE_REGISTRATION => (float) ($student->registration_fee ?? 0),
            self::PURPOSE_COUNSELOR => (float) ($student->counselor_fee ?? 0),
            self::PURPOSE_COLLEGE => (float) ($student->college_fee ?? 0),
            default => 0.0,
        };

        if ($feeAmount <= 0) {
            return null;
        }

        $remaining = $this->remainingFor($student, $purpose);
        $amount = min((float) $leadPayment->amount, $remaining);

        if ($amount <= 0) {
            return null;
        }

        $resolvedLedgerId = $ledgerAccountId
            ?? AccountTransaction::where('lead_payment_id', $leadPayment->id)->value('ledger_account_id')
            ?? $student->fee_ledger_account_id
            ?? $this->resolveDefaultLedgerAccountId();

        $gateway = match (strtolower((string) $leadPayment->payment_mode)) {
            'cash' => 'cash',
            'upi' => 'upi',
            'razorpay' => 'razorpay',
            default => strtolower((string) $leadPayment->payment_mode) ?: 'other',
        };

        $paidAt = $leadPayment->payment_date
            ? $leadPayment->payment_date->copy()->setTimeFromTimeString(now()->format('H:i:s'))
            : now();

        $payment = StudentPayment::create([
            'student_id' => $student->id,
            'purpose' => $purpose,
            'counselor_id' => $student->counselor_id,
            'ledger_account_id' => $resolvedLedgerId,
            'lead_payment_id' => $leadPayment->id,
            'amount' => $amount,
            'gateway' => $gateway,
            'transaction_id' => 'LEADPAY' . $leadPayment->id,
            'status' => 'paid',
            'remark' => $leadPayment->remark,
            'paid_at' => $paidAt,
            'metadata' => [
                'source' => 'lead_payment',
                'lead_payment_id' => $leadPayment->id,
                'lead_payment_type' => $leadPayment->payment_type,
                'payment_mode' => $leadPayment->payment_mode,
            ],
        ]);

        $this->syncPortalPaymentStatus($student->fresh());

        $existingTxn = AccountTransaction::where('lead_payment_id', $leadPayment->id)->first();
        if ($existingTxn && ! $existingTxn->student_payment_id) {
            $existingTxn->update(['student_payment_id' => $payment->id]);
        }

        return $payment->fresh(['student', 'counselor', 'ledgerAccount']);
    }

    public function syncToLedgerAccount(StudentPayment $payment, ?int $ledgerAccountId = null): ?AccountTransaction
    {
        if ($payment->status !== 'paid') {
            return null;
        }

        if (AccountTransaction::where('student_payment_id', $payment->id)->exists()) {
            return AccountTransaction::where('student_payment_id', $payment->id)->first();
        }

        $payment->loadMissing(['student', 'counselor']);

        $ledgerAccountId = $ledgerAccountId
            ?? $payment->ledger_account_id
            ?? $payment->student?->fee_ledger_account_id
            ?? $this->resolveDefaultLedgerAccountId();

        if (!$ledgerAccountId) {
            return null;
        }

        $purposeLabel = self::purposeLabels()[$payment->purpose] ?? $payment->purpose;
        $mode = match (strtolower((string) $payment->gateway)) {
            'razorpay' => 'RazorPay',
            'cash' => 'Cash',
            'upi' => 'UPI',
            default => 'Other',
        };

        $transaction = AccountTransaction::create([
            'ledger_account_id' => $ledgerAccountId,
            'student_payment_id' => $payment->id,
            'academic_year_id' => session('academic_year_id')
                ?? \App\Models\AcademicYear::where('is_active', true)->value('id'),
            'created_by' => null,
            'transaction_date' => optional($payment->paid_at)->toDateString() ?? now()->toDateString(),
            'entry_type' => 'credit',
            'category' => 'income',
            'reference_no' => $payment->transaction_id,
            'party_name' => $payment->student?->name,
            'amount' => $payment->amount,
            'payment_mode' => $mode,
            'description' => trim(sprintf(
                'Student %s — %s (Lead: %s)%s',
                $purposeLabel,
                $payment->student?->name,
                $payment->student?->lead_ref,
                $payment->counselor ? ' | Counselor: ' . $payment->counselor->name : ''
            )),
            'is_crm_synced' => true,
        ]);

        if (!$payment->ledger_account_id) {
            $payment->update(['ledger_account_id' => $ledgerAccountId]);
        }

        return $transaction;
    }

    public function resolveDefaultLedgerAccountId(): ?int
    {
        $configured = config('student.default_ledger_account_id');
        if ($configured) {
            return (int) $configured;
        }

        return LedgerAccount::where('status', 'Active')
            ->orderByRaw("CASE WHEN type = 'bank' THEN 0 ELSE 1 END")
            ->orderBy('id')
            ->value('id');
    }

    public function syncPortalPaymentStatus(Student $student): void
    {
        $summary = $this->feeSummary($student);

        if ($summary['registration_fee'] > 0 && $summary['registration_complete']) {
            $latest = $student->payments()
                ->where('purpose', self::PURPOSE_REGISTRATION)
                ->where('status', 'paid')
                ->latest('paid_at')
                ->first();

            $student->update([
                'payment_status' => 'paid',
                'payment_amount' => $summary['registration_paid'],
                'payment_reference' => $latest?->transaction_id,
                'paid_at' => $latest?->paid_at ?? now(),
            ]);

            return;
        }

        $paid = $this->paidTotal($student, self::PURPOSE_APPLICATION);
        $fee = (float) config('student.application_fee', 0);

        if ($fee > 0 && $paid >= $fee) {
            $latest = $student->payments()
                ->where('purpose', self::PURPOSE_APPLICATION)
                ->where('status', 'paid')
                ->latest('paid_at')
                ->first();

            $student->update([
                'payment_status' => 'paid',
                'payment_amount' => $paid,
                'payment_reference' => $latest?->transaction_id,
                'paid_at' => $latest?->paid_at ?? now(),
            ]);
        }
    }

    public function sendReceipt(StudentPayment $payment): void
    {
        $payment->loadMissing(['student', 'counselor']);

        try {
            Mail::to($payment->student->email)->send(new StudentPaymentReceiptMail($payment));
            $payment->update(['receipt_sent_at' => now()]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function sendDueReminder(Student $student, string $purpose, ?string $message = null): void
    {
        $summary = $this->feeSummary($student);

        $remaining = match ($purpose) {
            self::PURPOSE_REGISTRATION => $summary['registration_remaining'],
            self::PURPOSE_COUNSELOR => $summary['counselor_remaining'],
            self::PURPOSE_COLLEGE => $summary['college_remaining'],
            default => 0,
        };

        $dueDate = match ($purpose) {
            self::PURPOSE_REGISTRATION => $student->registration_fee_due_date,
            self::PURPOSE_COUNSELOR => $student->counselor_fee_due_date,
            self::PURPOSE_COLLEGE => $student->college_fee_due_date,
            default => null,
        };

        $label = self::purposeLabels()[$purpose] ?? $purpose;

        try {
            Mail::to($student->email)->send(new StudentPaymentDueMail(
                $student,
                $label,
                $remaining,
                $dueDate,
                $message
            ));
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }

        if ($student->counselor?->email) {
            try {
                Mail::to($student->counselor->email)->send(new StudentPaymentDueMail(
                    $student,
                    $label,
                    $remaining,
                    $dueDate,
                    $message,
                    'counselor'
                ));
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }
}
