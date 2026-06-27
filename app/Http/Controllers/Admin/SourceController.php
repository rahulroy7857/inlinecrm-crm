<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use App\Services\ActivityLogger;

class SourceController extends Controller
{
    public function index()
    {
        $sources = Source::all();
        return view('admin.settings.sources', compact('sources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sources',
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        $source = Source::create($request->all());

        // Log the activity
        ActivityLogger::log(
            "Created new source: {$source->name}",
            'Create',
            auth()->guard('admin')->user(),
            ['source' => $source->toArray()]
        );

        return redirect()->back()->with('success', 'Source added successfully!');
    }

    public function update(Request $request, $id)
    {
        $source = Source::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:sources,name,'.$id,
            'status' => 'required|in:Active,Inactive',
            'description' => 'nullable|string'
        ]);

        $oldData = $source->toArray();

        $source->update($request->all());

        // Log the activity
        ActivityLogger::log(
            "Updated source: {$source->name}",
            'Update',
            auth()->guard('admin')->user(),
            [
                'old' => $oldData,
                'new' => $source->toArray()
            ]
        );

        return redirect()->back()->with('success', 'Source updated successfully!');
    }

    public function destroy($id)
    {
        $source = Source::findOrFail($id);
        $sourceData = $source->toArray();
        $source->delete();

        // Log the activity
        ActivityLogger::log(
            "Deleted source: {$source->name}",
            'Delete',
            auth()->guard('admin')->user(),
            ['source' => $sourceData]
        );
        
        return redirect()->back()->with('success', 'Source deleted successfully!');
    }
}
