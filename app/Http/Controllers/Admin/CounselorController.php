<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Counselor;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CounselorController extends Controller
{
    public function index()
    {
        $counselors = Counselor::all();
        return view('admin.users.counselor', compact('counselors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:counselors',
            'mobile' => 'required|string|max:20|unique:counselors',
            'password' => 'required|string|min:8',
            'languages' => 'nullable|array',
            'languages.*' => 'string|in:' . implode(',', config('languages.indian')),
            'status' => 'required|boolean'
        ]);

        $counselor = Counselor::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'languages' => $request->languages,
            'status' => $request->status
        ]);

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
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:counselors,email,'.$id,
            'mobile' => 'required|string|max:20|unique:counselors,mobile,'.$id,
            'password' => 'nullable|string|min:8',
            'languages' => 'nullable|array',
            'languages.*' => 'string|in:' . implode(',', config('languages.indian')),
            'status' => 'required|boolean'
        ]);

        $oldData = $counselor->makeHidden(['password'])->toArray();
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'languages' => $request->languages,
            'status' => $request->status
        ];

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