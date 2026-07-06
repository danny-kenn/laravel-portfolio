<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $experiences = Experience::orderBy('sort_order')->get();
            return response()->json(['success' => true, 'data' => $experiences]);
        }
        
        $experiences = Experience::orderBy('sort_order')->get();
        
        // 🔐 Audit Log
        AuditLogger::view('experience', 'Viewed experience list');
        
        return view('admin.experience', compact('experiences'));
    }

    public function create()
    {
        return view('admin.experience-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'start_date' => 'required|string|max:50',
            'end_date' => 'nullable|string|max:50',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $experience = Experience::create([
            'job_title' => $validated['job_title'],
            'company' => $validated['company'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'is_current' => $validated['is_current'] ?? false,
            'description' => $validated['description'] ?? '',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        // 🔐 Audit Log
        AuditLogger::create('experience', "Added experience: {$experience->job_title} at {$experience->company}", $experience->toArray());

        return response()->json(['success' => true, 'message' => 'Experience added!']);
    }

    public function edit($id)
    {
        $experience = Experience::findOrFail($id);
        return view('admin.experience-form', compact('experience'));
    }

    public function update(Request $request, $id)
    {
        $experience = Experience::findOrFail($id);
        $oldData = $experience->toArray();

        $validated = $request->validate([
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'start_date' => 'required|string|max:50',
            'end_date' => 'nullable|string|max:50',
            'is_current' => 'nullable|boolean',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $experience->update($validated);

        // 🔐 Audit Log
        AuditLogger::update('experience', "Updated experience: {$experience->job_title}", $oldData, $experience->toArray());

        return response()->json(['success' => true, 'message' => 'Experience updated!']);
    }

    public function destroy($id)
    {
        $experience = Experience::findOrFail($id);
        $data = $experience->toArray();
        $name = $experience->job_title;
        
        $experience->delete();

        // 🔐 Audit Log
        AuditLogger::delete('experience', "Deleted experience: {$name}", $data);

        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }
}