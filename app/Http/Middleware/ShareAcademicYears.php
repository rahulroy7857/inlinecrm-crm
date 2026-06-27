<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShareAcademicYears
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->guard('admin')->check()) {
            $academicYears = AcademicYear::orderByDesc('name')->get();
            
            // Share academic years with all views
            View::share('academicYears', $academicYears);
            
            // Set default academic year if not set
            if (!session()->has('academic_year_id') && $academicYears->count() > 0) {
                session(['academic_year_id' => $academicYears->first()->id]);
            }
        }

        return $next($request);
    }
}