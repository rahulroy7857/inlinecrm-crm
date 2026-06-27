<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\CounselorAuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\SourceController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CounselorController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LeadEducationController;
use App\Http\Controllers\Admin\LeadExamController;
use App\Http\Controllers\Admin\LeadPaymentController;
use App\Http\Controllers\Admin\LeadContactLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AgentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/application', function () {
    return view('application');
});
Route::get('/payment', function () {
    return view('payment');
});
Route::get('/invoice', function () {
    return view('invoice');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::prefix('admin')->name('admin.')->group(function() {
    // Public routes
    Route::get('login', function() {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return app(AdminAuthController::class)->login();
    })->name('login');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::post('login', [AdminAuthController::class, 'authenticate'])->name('authenticate');
    
    // Protected routes
    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('leads/{status}', [LeadController::class, 'statusWiseLeads'])->name('leads.status');

        Route::get('new-leads', [LeadController::class, 'newLeads'])->name('new-leads');
        Route::post('/leads/bulk-transfer', [LeadController::class, 'bulkTransfer'])->name('bulk-transfer');
        Route::get('delete-lead/{id}', [LeadController::class, 'destroy'])->name('lead.destroy');
        Route::post('store-lead', [LeadController::class, 'store'])->name('leads.store');
        Route::post('leads/verify', [LeadController::class, 'verifyLead'])->name('leads.verify');
        Route::get('get-counts', [LeadController::class, 'getCounts'])->name('get-counts');

        Route::get('upload-leads', [LeadController::class, 'uploadLeads'])->name('upload-leads');
        Route::post('leads/upload', [LeadController::class, 'upload'])->name('leads.upload');
        
        Route::get('followups/pending', [LeadController::class, 'pendingFollowups'])->name('followups.pending');
        Route::get('followups/today', [LeadController::class, 'todayFollowups'])->name('followups.today');
        Route::get('followups/tomorrow', [LeadController::class, 'tomorrowFollowups'])->name('followups.tomorrow');

        Route::get('search', [LeadController::class, 'search'])->name('search');

        Route::get('change-password', [ChangePasswordController::class, 'index'])
            ->name('change-password');
        Route::put('change-password', [ChangePasswordController::class, 'update'])
            ->name('change-password.update');

        Route::get('settings/countries', function() {
            return view('admin.settings.countries');
        })->name('settings.countries');

        Route::controller(CollegeController::class)->group(function () {
            Route::get('settings/colleges', 'index')->name('settings.colleges');
            Route::post('settings/colleges', 'store')->name('settings.colleges.store');
            Route::put('settings/colleges/{id}', 'update')->name('settings.colleges.update');
            Route::delete('settings/colleges/{id}', 'destroy')->name('settings.colleges.destroy');
        });

        Route::controller(CourseController::class)->group(function () {
            Route::get('settings/courses', 'index')->name('settings.courses');
            Route::post('settings/courses', 'store')->name('settings.courses.store');
            Route::put('settings/courses/{id}', 'update')->name('settings.courses.update');
            Route::delete('settings/courses/{id}', 'destroy')->name('settings.courses.destroy');
        });

        Route::controller(AcademicYearController::class)->group(function () {
            Route::get('settings/academic-years', 'index')->name('settings.academic-years');
            Route::post('settings/academic-years', 'store')->name('settings.academic-years.store');
            Route::put('settings/academic-years/{id}', 'update')->name('settings.academic-years.update');
            Route::delete('settings/academic-years/{id}', 'destroy')->name('settings.academic-years.destroy');
        });

        Route::controller(SourceController::class)->group(function () {
            Route::get('settings/sources', 'index')->name('settings.sources');
            Route::post('settings/sources', 'store')->name('settings.sources.store');
            Route::put('settings/sources/{id}', 'update')->name('settings.sources.update');
            Route::delete('settings/sources/{id}', 'destroy')->name('settings.sources.destroy');
        });

        Route::controller(HolidayController::class)->group(function () {
            Route::get('settings/holidays', 'index')->name('settings.holidays');
            Route::post('settings/holidays', 'store')->name('settings.holidays.store');
            Route::put('settings/holidays/{id}', 'update')->name('settings.holidays.update');
            Route::delete('settings/holidays/{id}', 'destroy')->name('settings.holidays.destroy');
        });

        Route::controller(AgentController::class)->group(function () {
            Route::get('settings/agents', 'index')->name('settings.agents');
            Route::post('settings/agents', 'store')->name('settings.agents.store');
            Route::put('settings/agents/{id}', 'update')->name('settings.agents.update');
            Route::delete('settings/agents/{id}', 'destroy')->name('settings.agents.destroy');
        });

        Route::get('users/admin', function() {
            return view('admin.users.admin');
        })->name('users.admin');

        Route::resource('users/admin', AdminController::class, [
            'names' => [
                'index' => 'users.admin.index',
                'create' => 'users.admin.create',
                'store' => 'users.admin.store',
                'edit' => 'users.admin.edit',
                'update' => 'users.admin.update',
                'destroy' => 'users.admin.destroy',
                'show' => 'users.admin.show',
            ]
        ])->parameters(['admin' => 'id']);


        Route::prefix('users')->name('users.')->group(function () {
            Route::controller(CounselorController::class)->prefix('counselor')->name('counselor.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });
        });

        Route::get('users/role', function() {
            return view('admin.users.role');
        })->name('users.role');

        Route::get('users/permission', function() {
            return view('admin.users.permission');
        })->name('users.permission');

        Route::get('users/role-permission', function() {
            return view('admin.users.role-permission');
        })->name('users.role-permission');
        

        Route::get('lead-profile/{id}', [LeadController::class, 'show'])->name('leads.show');
        Route::post('leads/{id}/update', [LeadController::class, 'update'])->name('leads.update');
        Route::post('leads/{id}/photo', [LeadController::class, 'updatePhoto'])->name('leads.update.photo');
        Route::post('leads/transfer', [LeadController::class, 'transfer'])->name('lead.transfer');
        
        Route::post('leads/education', [LeadEducationController::class, 'store'])->name('lead.education.store');
        Route::get('leads/education/{id}/delete', [LeadEducationController::class, 'destroy'])->name('lead.education.destroy');
        Route::post('leads/education/{id}/update', [LeadEducationController::class, 'update'])->name('lead.education.update');
        
        Route::get('leads/exams/{id}/destroy', [LeadExamController::class, 'destroy'])->name('lead.exams.destroy');
        Route::post('leads/exams', [LeadExamController::class, 'store'])->name('lead.exams.store');
        Route::post('leads/exams/{id}/update', [LeadExamController::class, 'update'])->name('lead.exams.update');

        Route::post('leads/payments', [LeadPaymentController::class, 'store'])->name('lead.payments.store');

        Route::get('leads/contact-logs/{id}/delete', [LeadContactLogController::class, 'destroy'])->name('lead.contact_logs.destroy');
        Route::post('leads/contact-logs', [LeadContactLogController::class, 'store'])->name('lead.contact_logs.store');
        Route::post('leads/contact-logs/{id}/update', [LeadContactLogController::class, 'update'])->name('lead.contact_logs.update');

        Route::post('lead/admission/store', [LeadController::class, 'storeAdmission'])->name('lead.admission.store');
        Route::post('lead/application/store', [LeadController::class, 'storeApplication'])->name('lead.application.store');
        Route::post('lead/reservation/store', [LeadController::class, 'storeReservation'])->name('lead.reservation.store');
        Route::post('lead/cancel', [LeadController::class, 'cancel'])->name('lead.cancel');

        Route::get('reports/leads', [ReportController::class, 'leads'])->name('reports.leads');

        Route::get('reports/picked-leads', [ReportController::class, 'pickedLeads'])->name('reports.picked-leads');

        Route::get('reports/call-logs', [ReportController::class, 'callLogs'])->name('reports.call-logs');

        Route::get('reports/payments', [ReportController::class, 'payments'])->name('reports.payments');

        Route::get('reports/pending-followups', [ReportController::class, 'pendingFollowups'])->name('reports.pending-followups');
        Route::get('leads/pending/{counselor_id}', [ReportController::class, 'showPendingFollowups'])->name('leads.pending.show');

        Route::get('reports/analytics', [ReportController::class, 'analytics'])->name('reports.analytics');

        Route::get('reports/counselor-performance', [ReportController::class, 'counselorPerformance'])->name('reports.counselor-performance');

        Route::get('reports/transfer', [ReportController::class, 'transfer'])->name('reports.transfer');

        Route::get('/reports/agent-commission', [ReportController::class, 'agentCommission'])->name('reports.agent-commission');
        
        Route::get('reports/consolidated', [ReportController::class, 'consolidated'])->name('reports.consolidated');

        Route::get('notifications', function() {
            return view('admin.notifications');
        })->name('notifications');

        Route::get('messages', function() {
            return view('admin.messages');
        })->name('messages');

        Route::get('bulk-sms', function() {
            return view('admin.bulk-sms');
        })->name('bulk-sms');

        //logs
        Route::get('logs/activity', [ActivityLogController::class, 'index'])->name('logs.activity');
        
        //change academic year
        Route::post('change-academic-year', [AcademicYearController::class, 'changeAcademicYear'])
         ->name('change-academic-year');
        
         Route::prefix('config')->name('config.')->group(function () {
            Route::get('general', function () {
                return view('admin.config.general');
            })->name('general');

            Route::get('sms', function () {
                return view('admin.config.sms');
            })->name('sms');

            Route::get('payment-gateway', function () {
                return view('admin.config.payment-gateway');
            })->name('payment-gateway');

            Route::get('mail', function () {
                return view('admin.config.mail');
            })->name('mail');
        });
        
    });
});



// Route::prefix('counselor')->name('counselor.')->group(function() {
//     // Public routes
//     Route::get('login', function() {
//         if (auth()->guard('counselor')->check()) {
//             return redirect()->route('counselor.dashboard');
//         }
//         return app(CounselorAuthController::class)->login();
//     })->name('login');
//     Route::post('logout', [CounselorAuthController::class, 'logout'])->name('logout');
//     Route::post('login', [CounselorAuthController::class, 'authenticate'])->name('authenticate');
    
//     // Protected routes
//     Route::middleware(['auth:counselor'])->group(function () {
//         Route::get('dashboard', function() {
//             return view('counselor.dashboard');
//         })->name('dashboard');
        
//         // Add other counselor routes here
//     });
// });


require __DIR__.'/auth.php';
