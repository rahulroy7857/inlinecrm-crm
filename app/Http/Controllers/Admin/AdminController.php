<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Services\ActivityLogger;
class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
    {
        $admins = Admin::all();
        return view('admin.users.admin', compact('admins'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'status' => 'boolean'
        ]);

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? true
        ]);

        ActivityLogger::log(
            "Created new admin: {$admin->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['admin' => $admin->makeHidden(['password'])->toArray()]
        );
        return redirect()->route('admin.users.admin.index')->with('success', 'Admin created!');
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.users.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,'.$id,
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean'  // Add this validation
        ]);

        $oldData = $admin->makeHidden(['password'])->toArray();
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => (bool)$request->status  // Cast to boolean
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        ActivityLogger::log(
            "Updated admin: {$admin->name}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $admin->makeHidden(['password'])->toArray()
            ]
        );
        
        return redirect()->route('admin.users.admin.index')->with('success', 'Admin updated!');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $adminData = $admin->makeHidden(['password'])->toArray();
        $admin->delete();

        ActivityLogger::log(
            "Deleted admin: {$admin->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['admin' => $adminData]
        );

        return redirect()->route('admin.users.admin.index')->with('success', 'Admin deleted!');
    }
}
