<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tag;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $projects = Project::with('tags')->orderBy('is_featured', 'desc')->orderBy('sort_order')->get();
            return response()->json(['success' => true, 'data' => $projects]);
        }
        
        $projects = Project::with('tags')->orderBy('is_featured', 'desc')->orderBy('sort_order')->get();
        return view('admin.projects', compact('projects'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('admin.project-form', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'github_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'image_url' => 'nullable|url|max:255',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'tags' => 'nullable|string',
        ]);

        $project = Project::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title'] . '-' . Str::random(6)),
            'description' => $validated['description'] ?? '',
            'github_url' => $validated['github_url'] ?? null,
            'live_url' => $validated['live_url'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'is_featured' => $validated['is_featured'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => true,
        ]);

        // Handle tags
        if (!empty($validated['tags'])) {
            $tagNames = array_map('trim', explode(',', $validated['tags']));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tagIds[] = $tag->id;
            }
            $project->tags()->sync($tagIds);
        }

        // 🔐 Audit Log
        AuditLogger::create('projects', "Created project: {$project->title}", $project->toArray());

        // 🔔 Notify Admins & Editors
        NotificationHelper::sendToAdmins(
            "New Project Created",
            "Project '{$project->title}' was created by " . auth()->user()->full_name,
            'success',
            'projects',
            'create'
        );

        NotificationHelper::sendToRole(
            'editor',
            "New Project Created",
            "Project '{$project->title}' was created by " . auth()->user()->full_name,
            'info',
            'projects',
            'create'
        );

        return response()->json(['success' => true, 'message' => 'Project added!']);
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $tags = Tag::all();
        return view('admin.project-form', compact('project', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail($id);
        $oldData = $project->toArray();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'github_url' => 'nullable|url|max:255',
            'live_url' => 'nullable|url|max:255',
            'image_url' => 'nullable|url|max:255',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'tags' => 'nullable|string',
        ]);

        $project->update($validated);

        if (!empty($validated['tags'])) {
            $tagNames = array_map('trim', explode(',', $validated['tags']));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $tag = Tag::firstOrCreate(['name' => $name]);
                $tagIds[] = $tag->id;
            }
            $project->tags()->sync($tagIds);
        } else {
            $project->tags()->sync([]);
        }

        // 🔐 Audit Log
        AuditLogger::update('projects', "Updated project: {$project->title}", $oldData, $project->toArray());

        // 🔔 Notify Admins & Editors
        NotificationHelper::sendToAdmins(
            "Project Updated",
            "Project '{$project->title}' was updated by " . auth()->user()->full_name,
            'info',
            'projects',
            'update'
        );

        return response()->json(['success' => true, 'message' => 'Project updated!']);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $data = $project->toArray();
        $title = $project->title;
        
        $project->tags()->detach();
        $project->delete();

        // 🔐 Audit Log
        AuditLogger::delete('projects', "Deleted project: {$title}", $data);

        // 🔔 Notify Admins
        NotificationHelper::sendToAdmins(
            "Project Deleted",
            "Project '{$title}' was deleted by " . auth()->user()->full_name,
            'danger',
            'projects',
            'delete'
        );

        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }
}