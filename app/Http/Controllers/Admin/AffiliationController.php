<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliation;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class AffiliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $affiliations = Affiliation::orderBy('sort_order')->get();
            return response()->json(['success' => true, 'data' => $affiliations]);
        }
        
        $affiliations = Affiliation::orderBy('sort_order')->get();
        
        // 🔐 Audit Log
        AuditLogger::view('affiliations', 'Viewed affiliations list');
        
        return view('admin.affiliations', compact('affiliations'));
    }

    public function create()
    {
        return view('admin.affiliation-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'member_since' => 'nullable|digits:4',
            'icon_class' => 'nullable|string|max:50',
            'badge_text' => 'nullable|string|max:50',
            'benefits' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);

        $benefits = [];
        if (!empty($validated['benefits'])) {
            $benefits = array_map('trim', explode("\n", $validated['benefits']));
        }

        $affiliation = Affiliation::create([
            'organization' => $validated['organization'],
            'description' => $validated['description'] ?? '',
            'status' => $validated['status'] ?? 'Active',
            'member_since' => $validated['member_since'] ?? null,
            'icon_class' => $validated['icon_class'] ?? 'fas fa-users',
            'badge_text' => $validated['badge_text'] ?? null,
            'benefits' => json_encode($benefits),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        // 🔐 Audit Log
        AuditLogger::create('affiliations', "Added affiliation: {$affiliation->organization}", $affiliation->toArray());

        return response()->json(['success' => true, 'message' => 'Affiliation added!']);
    }

    public function edit($id)
    {
        $affiliation = Affiliation::findOrFail($id);
        $benefits = implode("\n", json_decode($affiliation->benefits ?? '[]', true) ?? []);
        return view('admin.affiliation-form', compact('affiliation', 'benefits'));
    }

    public function update(Request $request, $id)
    {
        $affiliation = Affiliation::findOrFail($id);
        $oldData = $affiliation->toArray();

        $validated = $request->validate([
            'organization' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
            'member_since' => 'nullable|digits:4',
            'icon_class' => 'nullable|string|max:50',
            'badge_text' => 'nullable|string|max:50',
            'benefits' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $benefits = [];
        if (!empty($validated['benefits'])) {
            $benefits = array_map('trim', explode("\n", $validated['benefits']));
        }

        $validated['benefits'] = json_encode($benefits);
        $affiliation->update($validated);

        // 🔐 Audit Log
        AuditLogger::update('affiliations', "Updated affiliation: {$affiliation->organization}", $oldData, $affiliation->toArray());

        return response()->json(['success' => true, 'message' => 'Affiliation updated!']);
    }

    public function destroy($id)
    {
        $affiliation = Affiliation::findOrFail($id);
        $data = $affiliation->toArray();
        $name = $affiliation->organization;
        
        $affiliation->delete();

        // 🔐 Audit Log
        AuditLogger::delete('affiliations', "Deleted affiliation: {$name}", $data);

        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }
}