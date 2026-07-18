<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Counselor;
use App\Models\CounselorTarget;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CounselorTargetController extends Controller
{
    public function index()
    {
        $targets = CounselorTarget::with(['counselor', 'course', 'academicYear'])
            ->latest()
            ->get();

        $counselors = Counselor::query()
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $courses = Course::query()
            ->where('status', 'Active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $academicYears = AcademicYear::query()
            ->orderByDesc('name')
            ->get(['id', 'name', 'is_active']);

        return view('admin.settings.set-target', compact(
            'targets',
            'counselors',
            'courses',
            'academicYears'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTarget($request);

        CounselorTarget::create($validated);

        return redirect()
            ->route('admin.settings.set-target')
            ->with('success', 'Target set successfully.');
    }

    public function update(Request $request, $id)
    {
        $target = CounselorTarget::findOrFail($id);
        $validated = $this->validateTarget($request, $target->id);

        $target->update($validated);

        return redirect()
            ->route('admin.settings.set-target')
            ->with('success', 'Target updated successfully.');
    }

    public function destroy($id)
    {
        CounselorTarget::findOrFail($id)->delete();

        return redirect()
            ->route('admin.settings.set-target')
            ->with('success', 'Target deleted successfully.');
    }

    private function validateTarget(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'counselor_id' => [
                'required',
                'exists:counselors,id',
                Rule::unique('counselor_targets')
                    ->where(fn ($query) => $query
                        ->where('course_id', $request->course_id)
                        ->where('academic_year_id', $request->academic_year_id))
                    ->ignore($ignoreId),
            ],
            'course_id' => ['required', 'exists:courses,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'amount' => ['required', 'numeric', 'min:0'],
        ], [
            'counselor_id.unique' => 'A target already exists for this counselor, course, and academic year.',
        ]);
    }
}
