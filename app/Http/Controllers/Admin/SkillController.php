<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $skills = Skill::orderBy('category')->orderBy('sort_order')->get();
            return response()->json(['success' => true, 'data' => $skills]);
        }
        
        $skills = Skill::orderBy('category')->orderBy('sort_order')->get();
        
        // 🔐 Audit Log
        AuditLogger::view('skills', 'Viewed skills list');
        
        return view('admin.skills', compact('skills'));
    }

    public function create()
    {
        return view('admin.skill-form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'skill_name' => 'required|string|max:100',
            'proficiency_level' => 'nullable|integer|min:1|max:100',
            'sort_order' => 'nullable|integer',
        ]);

        $skill = Skill::create([
            'category' => $validated['category'],
            'skill_name' => $validated['skill_name'],
            'proficiency_level' => $validated['proficiency_level'] ?? 80,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        // 🔐 Audit Log
        AuditLogger::create('skills', "Added skill: {$skill->skill_name} ({$skill->category})", $skill->toArray());

        return response()->json(['success' => true, 'message' => 'Skill added!']);
    }

    public function edit($id)
    {
        $skill = Skill::findOrFail($id);
        return view('admin.skill-form', compact('skill'));
    }

    public function update(Request $request, $id)
    {
        $skill = Skill::findOrFail($id);
        $oldData = $skill->toArray();

        $validated = $request->validate([
            'category' => 'required|string|max:50',
            'skill_name' => 'required|string|max:100',
            'proficiency_level' => 'nullable|integer|min:1|max:100',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $skill->update($validated);

        // 🔐 Audit Log
        AuditLogger::update('skills', "Updated skill: {$skill->skill_name}", $oldData, $skill->toArray());

        return response()->json(['success' => true, 'message' => 'Skill updated!']);
    }

    public function destroy($id)
    {
        $skill = Skill::findOrFail($id);
        $data = $skill->toArray();
        $name = $skill->skill_name;
        
        $skill->delete();

        // 🔐 Audit Log
        AuditLogger::delete('skills', "Deleted skill: {$name}", $data);

        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }
}