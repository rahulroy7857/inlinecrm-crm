<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Student\RegistrationController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ProfileController;
use App\Http\Controllers\Student\ApplicationController;
use App\Http\Controllers\Student\PaymentController;
use App\Http\Controllers\Student\DocumentController;

Route::get('login', function () {
    if (auth()->guard('student')->check()) {
        return redirect()->route('student.dashboard');
    }
    return app(StudentAuthController::class)->login();
})->name('login');

Route::post('login', [StudentAuthController::class, 'authenticate'])->name('authenticate');
Route::match(['get', 'post'], 'logout', [StudentAuthController::class, 'logout'])->name('logout');

Route::get('registration', [RegistrationController::class, 'show'])->name('registration');
Route::get('registration/{leadRef}', [RegistrationController::class, 'show'])->name('registration.lead');
Route::post('registration', [RegistrationController::class, 'store'])->name('registration.store');
Route::post('registration/{leadRef}', [RegistrationController::class, 'store'])->name('registration.lead.store');
Route::post('registration/{leadRef}/verify-otp', [RegistrationController::class, 'verifyOtp'])->name('registration.verify-otp');
Route::post('registration/{leadRef}/resend-otp', [RegistrationController::class, 'resendOtp'])->name('registration.resend-otp');

Route::middleware(['auth:student'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/complete', [ProfileController::class, 'complete'])->name('profile.complete');
    Route::put('profile/complete', [ProfileController::class, 'updateComplete'])->name('profile.complete.update');

    Route::redirect('application/status', '/student/dashboard')->name('application.status');
    Route::post('application/submit', [ApplicationController::class, 'submit'])->name('application.submit');

    Route::post('documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::get('documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::delete('documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('payment/callback/{payment}', [PaymentController::class, 'callback'])->name('payment.callback');
});
