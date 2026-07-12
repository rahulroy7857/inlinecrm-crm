<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CounselorAuthController;
use App\Http\Controllers\Counselor\CounselorController;
use App\Http\Controllers\Counselor\LeadController;
use App\Http\Controllers\Counselor\LeadEducationController;
use App\Http\Controllers\Counselor\LeadExamController;
use App\Http\Controllers\Counselor\LeadPaymentController;
use App\Http\Controllers\Counselor\StudentFeeController;
use App\Http\Controllers\Counselor\StudentFeePaymentController;
use App\Http\Controllers\Counselor\LeadContactLogController;
use App\Http\Controllers\Counselor\DashboardController;
use App\Http\Controllers\Counselor\ChangePasswordController;
use App\Http\Controllers\Counselor\ReportController;
use App\Http\Controllers\Counselor\AcademicYearController;
use App\Http\Controllers\Counselor\WorkingHoursController;
use App\Http\Middleware\EnsureCounselorBreakCompliance;

// Public routes
Route::get('login', function() {
    if (auth()->guard('counselor')->check()) {
        return redirect()->route('counselor.dashboard');
    }
    return app(CounselorAuthController::class)->login();
})->name('login');

Route::match(['get', 'post'], 'logout', [CounselorAuthController::class, 'logout'])->name('logout');
Route::post('login', [CounselorAuthController::class, 'authenticate'])->name('authenticate');

// Protected routes
Route::middleware(['auth:counselor', EnsureCounselorBreakCompliance::class])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('working-hours/status', [WorkingHoursController::class, 'status'])->name('working-hours.status');
    Route::post('working-hours/break/start', [WorkingHoursController::class, 'startBreak'])->name('working-hours.break.start');
    Route::post('working-hours/break/end', [WorkingHoursController::class, 'endBreak'])->name('working-hours.break.end');
    
    // Leads management
    Route::get('leads/{status}', [LeadController::class, 'statusWiseLeads'])->name('leads.status');
    Route::get('new-leads', [LeadController::class, 'newLeads'])->name('new-leads');
    Route::get('leads-basket', [LeadController::class, 'leadsBasket'])->name('leads-basket');
    Route::get('pick-lead/{id}', [LeadController::class, 'pickLead'])->name('pick-lead');
    Route::get('search', [LeadController::class, 'search'])->name('search');
    Route::get('lead-profile/{id}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('leads/{id}/update', [LeadController::class, 'update'])->name('leads.update');
    Route::post('leads/{id}/photo', [LeadController::class, 'updatePhoto'])->name('leads.update.photo');
    Route::get('get-counts', [LeadController::class, 'getCounts'])->name('get-counts');
    Route::post('store-lead', [LeadController::class, 'store'])->name('leads.store');
    Route::post('leads/verify', [LeadController::class, 'verifyLead'])->name('leads.verify');
    Route::get('delete-lead/{id}', [LeadController::class, 'destroy'])->name('lead.destroy');
    Route::post('leads/transfer', [LeadController::class, 'transfer'])->name('lead.transfer');
    // Lead education
    Route::post('leads/education', [LeadEducationController::class, 'store'])->name('lead.education.store');
    Route::get('leads/education/{id}/delete', [LeadEducationController::class, 'destroy'])->name('lead.education.destroy');
    Route::post('leads/education/{id}/update', [LeadEducationController::class, 'update'])->name('lead.education.update');
    
    // Lead exams
    Route::get('leads/exams/{id}/destroy', [LeadExamController::class, 'destroy'])->name('lead.exams.destroy');
    Route::post('leads/exams', [LeadExamController::class, 'store'])->name('lead.exams.store');
    Route::post('leads/exams/{id}/update', [LeadExamController::class, 'update'])->name('lead.exams.update');

    // Lead payments
    Route::post('leads/payments', [LeadPaymentController::class, 'store'])->name('lead.payments.store');

    Route::post('leads/{leadId}/student-fees/remind', [StudentFeeController::class, 'sendDueReminder'])->name('leads.student-fees.remind');
    Route::get('student-fee-payments', [StudentFeePaymentController::class, 'index'])->name('student-fee-payments.index');
    Route::get('leads/contact-logs/{id}/delete', [LeadContactLogController::class, 'destroy'])->name('lead.contact_logs.destroy');
    Route::post('leads/contact-logs', [LeadContactLogController::class, 'store'])->name('lead.contact_logs.store');
    Route::post('leads/contact-logs/{id}/update', [LeadContactLogController::class, 'update'])->name('lead.contact_logs.update');

    // Lead status updates
    Route::post('lead/admission/store', [LeadController::class, 'storeAdmission'])->name('lead.admission.store');
    Route::post('lead/application/store', [LeadController::class, 'storeApplication'])->name('lead.application.store');
    Route::post('lead/reservation/store', [LeadController::class, 'storeReservation'])->name('lead.reservation.store');
    Route::post('lead/cancel', [LeadController::class, 'cancel'])->name('lead.cancel');


    Route::get('followups/pending', [LeadController::class, 'pendingFollowups'])->name('followups.pending');
    Route::get('followups/today', [LeadController::class, 'todayFollowups'])->name('followups.today');
    Route::get('followups/tomorrow', [LeadController::class, 'tomorrowFollowups'])->name('followups.tomorrow');

    Route::get('notifications', function() {
        return view('counselor.notifications');
    })->name('notifications');

    Route::get('messages', function() {
        return view('counselor.messages');
    })->name('messages');

    //change academic year
    Route::post('change-academic-year', [AcademicYearController::class, 'changeAcademicYear'])
    ->name('change-academic-year');

    Route::get('reports/leads', [ReportController::class, 'leads'])->name('reports.leads');
    Route::get('reports/call-logs', [ReportController::class, 'callLogs'])->name('reports.call-logs');
    Route::get('reports/counselor-performance', [ReportController::class, 'counselorPerformance'])->name('reports.counselor-performance');
    Route::get('reports/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');

    // Profile management
    Route::get('change-password', [ChangePasswordController::class, 'index'])->name('change-password');
    Route::put('change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');
}); 