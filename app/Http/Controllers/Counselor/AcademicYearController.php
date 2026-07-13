<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
class AcademicYearController extends Controller
{
    public function index()
    {

    }

    public function changeAcademicYear(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|exists:academic_years,id'
        ]);

        $academicYear = AcademicYear::findOrFail($request->academic_year);
        
        session([
            'academic_year_id' => $academicYear->id,
            'academic_year_name' => $academicYear->name
        ]);

        // Update all academic years to inactive first
        // AcademicYear::query()->update(['is_active' => false]);
        
        // Set the selected academic year as active
        // $academicYear->update(['is_active' => true]);
        
        // Log the activity
        ActivityLogger::log(
            "Changed academic year to: {$academicYear->name}",
            'Update',
            auth()->guard('counselor')->user(),
            ['academic_year' => $academicYear->toArray()]
        );

        return redirect()->back()->with('success', 'Academic year updated successfully.');
    }
}