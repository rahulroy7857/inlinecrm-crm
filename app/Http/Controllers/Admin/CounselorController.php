<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CounselorController extends Controller
{
    private function employmentRules(): array
    {
        $weekdayKeys = implode(',', array_keys(config('weekdays')));

        return [
            'joining_date' => 'required|date|before_or_equal:today',
            'office_start_time' => 'required|date_format:H:i',
            'office_end_time' => 'required|date_format:H:i|after:office_start_time',
            'working_days' => 'required|array|min:1',
            'working_days.*' => 'string|in:' . $weekdayKeys,
            'salary' => 'required|numeric|min:0',
        ];
    }

    private function employmentData(Request $request): array
    {
        return [
            'joining_date' => $request->joining_date,
            'office_start_time' => $request->office_start_time,
            'office_end_time' => $request->office_end_time,
            'working_days' => $request->working_days ?? [],
            'salary' => $request->salary ?? 0,
        ];
    }

    public function index()
    {
        $counselors = Counselor::all();
        return view('admin.users.counselor', compact('counselors'));
    }

    public function store(Request $request)
    {
        $request->validate(array_merge([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:counselors',
            'mobile' => 'required|string|max:20|unique:counselors',
            'password' => 'required|string|min:8',
            'languages' => 'nullable|array',
            'languages.*' => 'string|in:' . implode(',', config('languages.indian')),
            'status' => 'required|boolean'
        ], $this->employmentRules()));

        $counselor = Counselor::create(array_merge([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'languages' => $request->languages,
            'status' => $request->status
        ], $this->employmentData($request)));

        ActivityLogger::log(
            "Created new counselor: {$counselor->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['counselor' => $counselor->makeHidden(['password'])->toArray()]
        );

        return redirect()->back()->with('success', 'Counselor added successfully!');
    }

    public function update(Request $request, $id)
    {
        $counselor = Counselor::findOrFail($id);
        
        $request->validate(array_merge([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:counselors,email,'.$id,
            'mobile' => 'required|string|max:20|unique:counselors,mobile,'.$id,
            'password' => 'nullable|string|min:8',
            'languages' => 'nullable|array',
            'languages.*' => 'string|in:' . implode(',', config('languages.indian')),
            'status' => 'required|boolean'
        ], $this->employmentRules()));

        $oldData = $counselor->makeHidden(['password'])->toArray();
        
        $updateData = array_merge([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'languages' => $request->languages,
            'status' => $request->status
        ], $this->employmentData($request));

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $counselor->update($updateData);

        ActivityLogger::log(
            "Updated counselor: {$counselor->name}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $counselor->makeHidden(['password'])->toArray()
            ]
        );

        return redirect()->back()->with('success', 'Counselor updated successfully!');
    }

    public function destroy($id)
    {
        $counselor = Counselor::findOrFail($id);
        $counselorData = $counselor->makeHidden(['password'])->toArray();
        
        $counselor->delete();

        ActivityLogger::log(
            "Deleted counselor: {$counselor->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['counselor' => $counselorData]
        );

        return redirect()->back()->with('success', 'Counselor deleted successfully!');
    }
}