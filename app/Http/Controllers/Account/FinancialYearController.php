<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    public function change(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|exists:academic_years,id',
        ]);

        $year = \App\Models\AcademicYear::findOrFail($request->academic_year);

        session([
            'academic_year_id' => $year->id,
            'academic_year_name' => $year->name,
        ]);

        return redirect()->back()->with('success', 'Financial year updated successfully.');
    }
}
