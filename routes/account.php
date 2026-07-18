<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AccountAuthController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\LedgerAccountController;
use App\Http\Controllers\Account\TransactionController;
use App\Http\Controllers\Account\DaybookController;
use App\Http\Controllers\Account\ProfitLossController;
use App\Http\Controllers\Account\ReportController;
use App\Http\Controllers\Account\ChangePasswordController;
use App\Http\Controllers\Account\FinancialYearController;
use App\Http\Controllers\Account\LeadPaymentController as AccountLeadPaymentController;
use App\Http\Controllers\Account\CounselorSalaryController;
use App\Http\Controllers\Account\WorkingHoursController;
use App\Http\Controllers\Account\StudentFeePaymentController as AccountStudentFeePaymentController;
use App\Http\Controllers\Account\StudentFeeManageController as AccountStudentFeeManageController;
use App\Http\Middleware\EnsureAccountBreakCompliance;

Route::get('login', function () {
    if (auth()->guard('account')->check()) {
        return redirect()->route('account.dashboard');
    }
    return app(AccountAuthController::class)->login();
})->name('login');

Route::post('login', [AccountAuthController::class, 'authenticate'])->name('authenticate');
Route::match(['get', 'post'], 'logout', [AccountAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:account', EnsureAccountBreakCompliance::class])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('working-hours/status', [WorkingHoursController::class, 'status'])->name('working-hours.status');
    Route::post('working-hours/break/start', [WorkingHoursController::class, 'startBreak'])->name('working-hours.break.start');
    Route::post('working-hours/break/end', [WorkingHoursController::class, 'endBreak'])->name('working-hours.break.end');

    Route::controller(LedgerAccountController::class)->group(function () {
        Route::get('ledger-accounts', 'index')->name('ledger-accounts.index');
        Route::post('ledger-accounts', 'store')->name('ledger-accounts.store');
        Route::put('ledger-accounts/{id}', 'update')->name('ledger-accounts.update');
        Route::delete('ledger-accounts/{id}', 'destroy')->name('ledger-accounts.destroy');
    });

    Route::controller(TransactionController::class)->group(function () {
        Route::get('transactions', 'index')->name('transactions.index');
        Route::get('transactions/create', 'create')->name('transactions.create');
        Route::post('transactions', 'store')->name('transactions.store');
        Route::delete('transactions/{id}', 'destroy')->name('transactions.destroy');
    });

    Route::get('daybook', [DaybookController::class, 'index'])->name('daybook.index');
    Route::get('profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');

    Route::controller(AccountLeadPaymentController::class)->group(function () {
        Route::get('lead-payments', 'index')->name('lead-payments.index');
        Route::get('lead-payments/search-leads', 'searchLeads')->name('lead-payments.search-leads');
        Route::post('lead-payments', 'store')->name('lead-payments.store');
    });

    Route::get('student-fee-payments', [AccountStudentFeePaymentController::class, 'index'])->name('student-fee-payments.index');

    Route::get('student-fees', [AccountStudentFeeManageController::class, 'index'])->name('student-fees.index');
    Route::put('student-fees/{id}', [AccountStudentFeeManageController::class, 'update'])->name('student-fees.update');
    Route::post('student-fees/{id}/pay', [AccountStudentFeeManageController::class, 'recordPayment'])->name('student-fees.pay');

    Route::controller(CounselorSalaryController::class)->group(function () {
        Route::get('counselor-salaries', 'index')->name('counselor-salaries.index');
        Route::get('counselor-salaries/{id}', 'show')->name('counselor-salaries.show');
        Route::post('counselor-salaries/{id}/pay', 'pay')->name('counselor-salaries.pay');
    });

    Route::controller(ReportController::class)->group(function () {
        Route::get('reports', 'index')->name('reports.index');
        Route::get('reports/account-statement', 'accountStatement')->name('reports.account-statement');
        Route::get('reports/cash-flow', 'cashFlow')->name('reports.cash-flow');
        Route::get('reports/ledger-summary', 'ledgerSummary')->name('reports.ledger-summary');
    });

    Route::get('change-password', [ChangePasswordController::class, 'index'])->name('change-password');
    Route::put('change-password', [ChangePasswordController::class, 'update'])->name('change-password.update');

    Route::post('change-financial-year', [FinancialYearController::class, 'change'])->name('change-financial-year');
});
