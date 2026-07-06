<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the blog posts.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role; // Get exact role
        
        // Build query based on EXACT role
        $query = BlogPost::with(['categories', 'tags', 'author']);
        
        // 🔥 Use EXACT role matching, NOT hierarchical
        if ($role === 'author') {
            // Authors: ONLY see their own posts
            $query->where('author_id', $user->id);
        } 
        elseif ($role === 'editor') {
            // Editors: See all posts EXCEPT other people's archived posts
            $query->where(function($q) use ($user) {
                $q->where('status', '!=', 'archived')
                  ->orWhere('author_id', $user->id);
            });
        }
        // Admins and SuperAdmins: See EVERYTHING (no filter needed)

        if ($request->has('json') && $request->json == 1) {
            $posts = $query->orderBy('created_at', 'desc')->get();
            
            $formattedPosts = $posts->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => $post->excerpt,
                    'body' => $post->body,
                    'featured_image' => $post->featured_image,
                    'status' => $post->status,
                    'view_count' => $post->view_count,
                    'created_at' => $post->created_at,
                    'created_at_human' => $post->created_at->diffForHumans(),
                    'categories' => $post->categories->map(function($cat) {
                        return ['id' => $cat->id, 'name' => $cat->name];
                    }),
                    'categories_string' => $post->categories->pluck('name')->implode(', '),
                    'tags' => $post->tags->map(function($tag) {
                        return ['id' => $tag->id, 'name' => $tag->name];
                    }),
                    'tags_string' => $post->tags->pluck('name')->implode(', '),
                    'author_name' => $post->author ? $post->author->full_name : 'Unknown',
                    'author_id' => $post->author_id,
                ];
            });
            
            return response()->json(['success' => true, 'data' => $formattedPosts]);
        }
        
        $posts = $query->orderBy('created_at', 'desc')->get();
        return view('admin.blog', compact('posts'));
    }

    /**
     * Show the form for creating a new blog post.
     */
    public function create()
    {
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        return view('admin.blog-form', compact('categories', 'tags'));
    }

    /**
     * Store a newly created blog post in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'featured_image' => 'nullable|url|max:255',
                'status' => 'required|in:draft,published,archived',
                'categories' => 'nullable|string',
                'tags' => 'nullable|string',
            ]);

            $user = auth()->user();
            $role = $user->role;

            // 🔥 EXACT role matching for Authors
            if ($role === 'author') {
                // Authors can ONLY create drafts
                if ($validated['status'] !== 'draft') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Authors can only create drafts. Please ask an Editor or Admin to publish.'
                    ], 403);
                }
            }
            
            // 🔥 EXACT role matching for Editors
            if ($role === 'editor') {
                // Editors cannot create archived posts
                if ($validated['status'] === 'archived') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Editors cannot archive posts. Only Admins and SuperAdmins can archive.'
                    ], 403);
                }
            }

            // Parse categories
            $categoryNames = array_map('trim', explode(',', $validated['categories'] ?? ''));
            $categoryNames = array_filter($categoryNames);

            // Parse tags (remove # and trim)
            $tagNames = array_map('trim', explode(',', $validated['tags'] ?? ''));
            $tagNames = array_filter($tagNames);
            $tagNames = array_map(function($tag) {
                return ltrim(trim($tag), '#');
            }, $tagNames);

            // Generate unique slug
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            
            while (BlogPost::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $post = BlogPost::create([
                'title' => $validated['title'],
                'slug' => $slug,
                'body' => $validated['body'],
                'excerpt' => $validated['excerpt'] ?? Str::limit(strip_tags($validated['body']), 150),
                'featured_image' => $validated['featured_image'] ?? null,
                'status' => $validated['status'],
                'published_at' => $validated['status'] === 'published' ? now() : null,
                'view_count' => 0,
                'author_id' => $user->id,
            ]);

            // Attach categories
            if (!empty($categoryNames)) {
                $categoryIds = [];
                foreach ($categoryNames as $name) {
                    $category = BlogCategory::firstOrCreate([
                        'name' => trim($name),
                        'slug' => Str::slug(trim($name))
                    ]);
                    $categoryIds[] = $category->id;
                }
                $post->categories()->sync($categoryIds);
            }

            // Attach tags
            if (!empty($tagNames)) {
                $tagIds = [];
                foreach ($tagNames as $name) {
                    if (!empty($name)) {
                        $tag = BlogTag::firstOrCreate([
                            'name' => trim($name),
                            'slug' => Str::slug(trim($name))
                        ]);
                        $tagIds[] = $tag->id;
                    }
                }
                $post->tags()->sync($tagIds);
            }

            AuditLogger::create('blog', "Created blog post: {$post->title} (Status: {$post->status})", $post->toArray());

            // 🔔 Notify Editors about new draft
            if ($post->status === 'draft') {
                NotificationHelper::sendToRole(
                    'editor',
                    "📝 New Blog Draft",
                    "Author '{$user->full_name}' has created a new draft: '{$post->title}'. Please review and publish.",
                    'info',
                    'blog',
                    'draft'
                );
                NotificationHelper::sendToAdmins(
                    "New Blog Draft",
                    "Author '{$user->full_name}' has created a new draft: '{$post->title}'.",
                    'info',
                    'blog',
                    'draft'
                );
            }

            return response()->json(['success' => true, 'message' => 'Blog post created!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified blog post.
     */
    public function edit($id)
    {
        $post = BlogPost::with(['categories', 'tags', 'author'])->findOrFail($id);
        $user = auth()->user();
        $role = $user->role;
        
        // 🔥 EXACT role matching for Authors
        if ($role === 'author') {
            if ($post->author_id !== $user->id) {
                abort(403, 'You can only edit your own posts.');
            }
            if ($post->status !== 'draft') {
                abort(403, 'You can only edit draft posts.');
            }
        }
        
        // 🔥 EXACT role matching for Editors
        if ($role === 'editor') {
            if ($post->status === 'archived' && $post->author_id !== $user->id) {
                abort(403, 'You can only edit your own archived posts.');
            }
        }
        
        $categories = BlogCategory::all();
        $tags = BlogTag::all();
        $selectedCategories = $post->categories->pluck('id')->toArray();
        $selectedTags = $post->tags->pluck('id')->toArray();
        return view('admin.blog-form', compact('post', 'categories', 'tags', 'selectedCategories', 'selectedTags'));
    }

    /**
     * Update the specified blog post in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $post = BlogPost::findOrFail($id);
            $user = auth()->user();
            $role = $user->role;
            $oldData = $post->toArray();
            $oldStatus = $post->status;
            
            // 🔥 EXACT role matching for Authors
            if ($role === 'author') {
                if ($post->author_id !== $user->id) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can only edit your own posts.'
                    ], 403);
                }
                if ($post->status !== 'draft') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can only edit draft posts.'
                    ], 403);
                }
                if (!in_array($request->status, ['draft', 'archived'])) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Authors can only change status to Draft or Archive.'
                    ], 403);
                }
            }
            
            // 🔥 EXACT role matching for Editors
            if ($role === 'editor') {
                // Can't edit others' archived posts
                if ($post->status === 'archived' && $post->author_id !== $user->id) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can only edit your own archived posts.'
                    ], 403);
                }
                // Can't archive any post
                if ($request->status === 'archived') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Editors cannot archive posts. Only Admins can archive.'
                    ], 403);
                }
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'excerpt' => 'nullable|string|max:500',
                'featured_image' => 'nullable|url|max:255',
                'status' => 'required|in:draft,published,archived',
                'categories' => 'nullable|string',
                'tags' => 'nullable|string',
            ]);

            // Parse categories
            $categoryNames = array_map('trim', explode(',', $validated['categories'] ?? ''));
            $categoryNames = array_filter($categoryNames);

            // Parse tags
            $tagNames = array_map('trim', explode(',', $validated['tags'] ?? ''));
            $tagNames = array_filter($tagNames);
            $tagNames = array_map(function($tag) {
                return ltrim(trim($tag), '#');
            }, $tagNames);

            if ($validated['status'] === 'published' && $post->status !== 'published') {
                $validated['published_at'] = now();
            }

            // Update slug if title changed
            if ($post->title !== $validated['title']) {
                $baseSlug = Str::slug($validated['title']);
                $slug = $baseSlug;
                $counter = 1;
                
                while (BlogPost::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                $validated['slug'] = $slug;
            }

            $post->update($validated);

            // Sync categories
            if (!empty($categoryNames)) {
                $categoryIds = [];
                foreach ($categoryNames as $name) {
                    $category = BlogCategory::firstOrCreate([
                        'name' => trim($name),
                        'slug' => Str::slug(trim($name))
                    ]);
                    $categoryIds[] = $category->id;
                }
                $post->categories()->sync($categoryIds);
            } else {
                $post->categories()->sync([]);
            }

            // Sync tags
            if (!empty($tagNames)) {
                $tagIds = [];
                foreach ($tagNames as $name) {
                    if (!empty($name)) {
                        $tag = BlogTag::firstOrCreate([
                            'name' => trim($name),
                            'slug' => Str::slug(trim($name))
                        ]);
                        $tagIds[] = $tag->id;
                    }
                }
                $post->tags()->sync($tagIds);
            } else {
                $post->tags()->sync([]);
            }

            AuditLogger::update('blog', "Updated blog post: {$post->title}", $oldData, $post->toArray());

            return response()->json(['success' => true, 'message' => 'Blog post updated!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified blog post from storage.
     */
    public function destroy($id)
    {
        try {
            $post = BlogPost::findOrFail($id);
            $user = auth()->user();
            $role = $user->role;
            $data = $post->toArray();
            $title = $post->title;
            
            // 🔥 EXACT role matching for Authors
            if ($role === 'author') {
                if ($post->author_id !== $user->id) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can only delete your own posts.'
                    ], 403);
                }
                if ($post->status !== 'draft') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can only delete draft posts.'
                    ], 403);
                }
            }
            
            // 🔥 EXACT role matching for Editors
            if ($role === 'editor') {
                if ($post->status === 'archived' && $post->author_id !== $user->id) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'You can only delete your own archived posts.'
                    ], 403);
                }
                if ($post->status === 'published') {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Editors cannot delete published posts. Ask an Admin.'
                    ], 403);
                }
            }
            
            $post->categories()->detach();
            $post->tags()->detach();
            $post->delete();

            AuditLogger::delete('blog', "Deleted blog post: {$title}", $data);

            return response()->json(['success' => true, 'message' => 'Deleted!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}