<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agent;

class AgentController extends Controller
{
    public function index()
    {
        $agents = Agent::latest()->get();
        return view('admin.settings.agents', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        Agent::create($validated);

        return redirect()
            ->route('admin.settings.agents')
            ->with('success', 'Agent added successfully');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $agent = Agent::findOrFail($id);
        $agent->update($validated);

        return redirect()
            ->route('admin.settings.agents')
            ->with('success', 'Agent updated successfully');
    }

    public function destroy($id)
    {
        $agent = Agent::findOrFail($id);
        $agent->delete();

        return redirect()
            ->route('admin.settings.agents')
            ->with('success', 'Agent deleted successfully');
    }
}