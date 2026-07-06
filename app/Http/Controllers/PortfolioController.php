<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Certificate;
use App\Models\BlogPost;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    public function index($username = null)
    {
        // Get the portfolio owner (SuperAdmin or first user)
        if ($username) {
            $user = User::where('username', $username)->where('is_active', true)->firstOrFail();
        } else {
            $user = User::where('role', 'super_admin')->first();
            if (!$user) {
                $user = User::first();
            }
        }

        // Get all content - NO user_id filters (single portfolio)
        $profile = Profile::where('user_id', $user->id)->first();
        $education = \App\Models\Education::where('is_active', true)->orderBy('sort_order')->get();
        $skills = \App\Models\Skill::where('is_active', true)->orderBy('category')->orderBy('sort_order')->get();
        $experiences = \App\Models\Experience::where('is_active', true)->orderBy('sort_order')->get();
        $projects = \App\Models\Project::where('is_active', true)->orderBy('is_featured', 'desc')->orderBy('sort_order')->get();
        $certificates = \App\Models\Certificate::where('is_active', true)->orderBy('sort_order')->get();
        $affiliations = \App\Models\Affiliation::where('is_active', true)->orderBy('sort_order')->get();
        $blogPosts = BlogPost::where('status', 'published')->latest()->limit(6)->get();

        return view('portfolio.index', compact(
            'user', 'profile', 'education', 'skills', 'experiences',
            'projects', 'certificates', 'affiliations', 'blogPosts'
        ));
    }

    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'message' => 'required|string|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        ContactMessage::create([
            'sender_name' => $request->name,
            'sender_email' => $request->email,
            'message' => $request->message,
            'subject' => $request->subject ?? 'Portfolio Contact',
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
    }
}