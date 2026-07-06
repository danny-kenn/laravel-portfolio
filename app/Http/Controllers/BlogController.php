<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);
        
        $categories = BlogCategory::all();
        $recentPosts = BlogPost::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('blog.index', compact('posts', 'categories', 'recentPosts'));
    }

    public function show($slug)
    {
        $post = BlogPost::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();
        
        // Increment view count
        $post->increment('view_count');
        
        $relatedPosts = BlogPost::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function($query) use ($post) {
                $query->whereIn('blog_categories.id', $post->categories->pluck('id')->toArray());
            })
            ->limit(3)
            ->get();
        
        $recentPosts = BlogPost::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('blog.show', compact('post', 'relatedPosts', 'recentPosts'));
    }
}