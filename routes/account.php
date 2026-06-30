<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AccountAuthController;
use App\Http\Controllers\Account\DashboardController;
use App\Http\Controllers\Account\LedgerAccountController;
use App\Http\Controllers\Account\TransactionController;
use App\Http\Controllers\Account\DaybookController;
use App\Http\Controllers\Account\ProfitLossController;
use App\Http\Controllers\Account\CrmSyncController;
use App\Http\Controllers\Account\ReportController;
use App\Http\Controllers\Account\ChangePasswordController;
use App\Http\Controllers\Account\FinancialYearController;

Route::get('login', function () {
    if (auth()->guard('account')->check()) {
        return redirect()->route('account.dashboard');
    }
    return app(AccountAuthController::class)->login();
})->name('login');

Route::post('login', [AccountAuthController::class, 'authenticate'])->name('authenticate');
Route::match(['get', 'post'], 'logout', [AccountAuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:account'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

    Route::controller(CrmSyncController::class)->group(function () {
        Route::get('crm-sync', 'index')->name('crm-sync.index');
        Route::post('crm-sync', 'sync')->name('crm-sync.sync');
        Route::post('crm-sync/all', 'syncAll')->name('crm-sync.sync-all');
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
