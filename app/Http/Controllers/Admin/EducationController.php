<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Education;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $education = Education::orderBy('sort_order')->get();
            return response()->json(['success' => true, 'data' => $education]);
        }
        
        $education = Education::orderBy('sort_order')->get();
        
        // 🔐 Audit Log
        AuditLogger::view('education', 'Viewed education list');
        
        return view('admin.education', compact('education'));
    }

    public function create()
    {
        return view('admin.education-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'start_year' => 'required|digits:4',
            'end_year' => 'nullable|digits:4',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $education = Education::create([
            'institution' => $validated['institution'],
            'degree' => $validated['degree'],
            'start_year' => $validated['start_year'],
            'end_year' => $validated['end_year'] ?? null,
            'description' => $validated['description'] ?? '',
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        // 🔐 Audit Log
        AuditLogger::create('education', "Added education: {$education->institution} ({$education->degree})", $education->toArray());

        // 🔔 Notify Admins
        NotificationHelper::sendToAdmins(
            "New Education Added",
            "{$education->institution} ({$education->degree}) was added by " . auth()->user()->full_name,
            'info',
            'education',
            'create'
        );

        return response()->json(['success' => true, 'message' => 'Education added!']);
    }

    public function edit($id)
    {
        $education = Education::findOrFail($id);
        return view('admin.education-form', compact('education'));
    }

    public function update(Request $request, $id)
    {
        $education = Education::findOrFail($id);
        $oldData = $education->toArray();

        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree' => 'required|string|max:255',
            'start_year' => 'required|digits:4',
            'end_year' => 'nullable|digits:4',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $education->update($validated);

        // 🔐 Audit Log
        AuditLogger::update('education', "Updated education: {$education->institution}", $oldData, $education->toArray());

        return response()->json(['success' => true, 'message' => 'Education updated!']);
    }

    public function destroy($id)
    {
        $education = Education::findOrFail($id);
        $data = $education->toArray();
        $name = $education->institution;
        
        $education->delete();

        // 🔐 Audit Log
        AuditLogger::delete('education', "Deleted education: {$name}", $data);

        // 🔔 Notify Admins
        NotificationHelper::sendToAdmins(
            "Education Deleted",
            "{$name} was deleted by " . auth()->user()->full_name,
            'danger',
            'education',
            'delete'
        );

        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }
}