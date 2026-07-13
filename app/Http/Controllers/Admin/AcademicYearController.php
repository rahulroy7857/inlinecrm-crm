<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;
class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::orderByDesc('name')->orderBy('id', 'desc')->get();
        return view('admin.settings.academic-years', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:academic_years',
            'is_active' => 'boolean'
        ]);

        $academicYear = AcademicYear::create($request->all());

        // Log the activity
        ActivityLogger::log(
            "Created new academic year: {$academicYear->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['academic_year' => $academicYear->toArray()]
        );

        return redirect()->back()->with('success', 'Academic Year added successfully!');
    }

    public function update(Request $request, $id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:academic_years,name,'.$id,
            'is_active' => 'boolean'
        ]);

        $oldData = $academicYear->toArray();

        $academicYear->update($request->all());

        // Log the activity
        ActivityLogger::log(
            "Updated academic year: {$academicYear->name}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $academicYear->toArray()
            ]
        );

        return redirect()->back()->with('success', 'Academic Year updated successfully!');
    }

    public function destroy($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        
        if ($academicYear->is_active) {
            return redirect()->back()->with('error', 'Cannot delete active academic year!');
        }

        $academicYearData = $academicYear->toArray();
        
        $academicYear->delete();

        // Log the activity
        ActivityLogger::log(
            "Deleted academic year: {$academicYear->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['academic_year' => $academicYearData]
        );
        
        return redirect()->back()->with('success', 'Academic Year deleted successfully!');
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
            auth()->guard('admin')->user(),
            ['academic_year' => $academicYear->toArray()]
        );

        return redirect()->back()->with('success', 'Academic year updated successfully.');
    }
}