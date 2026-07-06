<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use App\Models\Certificate;
use App\Models\Skill;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Helpers\AuditLogger;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // 🔐 Audit Log
        AuditLogger::view('dashboard', 'Viewed dashboard');
        
        // Basic stats for everyone
        $stats = [
            'total_projects' => Project::where('is_active', true)->count(),
            'total_certificates' => Certificate::where('is_active', true)->count(),
            'total_blog_posts' => BlogPost::where('status', 'published')->count(),
            'total_skills' => Skill::where('is_active', true)->count(),
            'is_viewer' => $user->isViewer(),
            'is_author' => $user->isAuthor(),
            'is_editor' => $user->isEditor(),
            'is_admin' => $user->isAdmin() || $user->isSuperAdmin(),
            // 🔥 Viewers see the same as public - show recent content
            'recent_projects' => Project::where('is_active', true)->latest()->limit(5)->get(),
            'recent_blog_posts' => BlogPost::where('status', 'published')->latest()->limit(5)->get(),
        ];

        // If not a viewer, add admin stats
        if (!$user->isViewer()) {
            $stats['total_users'] = User::count();
            $stats['active_users'] = User::where('is_active', true)->count();
            $stats['unread_messages'] = ContactMessage::where('is_read', false)->count();
            $stats['recent_users'] = User::latest()->limit(5)->get();
            $stats['recent_messages'] = ContactMessage::latest()->limit(5)->get();
        } else {
            $stats['total_users'] = 0;
            $stats['active_users'] = 0;
            $stats['unread_messages'] = 0;
            $stats['recent_users'] = collect([]);
            $stats['recent_messages'] = collect([]);
        }

        return view('admin.dashboard', compact('stats'));
    }
}