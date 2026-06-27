<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use App\Models\AcademicYear;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.layouts.app', function ($view) {
            if (auth()->guard('admin')->check()) {
                $academicYears = AcademicYear::orderByDesc('name')->get();
                $view->with('academicYears', $academicYears);
            }
        });
    }
}
