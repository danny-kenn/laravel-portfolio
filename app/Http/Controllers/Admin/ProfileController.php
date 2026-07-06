<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        
        if (!$profile) {
            $profile = new Profile(['user_id' => $user->id]);
            $profile->save();
        }
        
        AuditLogger::view('profile', 'Viewed profile settings');
        
        return view('admin.profile', compact('user', 'profile'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();
        
        $oldData = $profile ? $profile->toArray() : null;
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'tagline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'github_url' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'availability_status' => 'nullable|boolean',
            'availability_note' => 'nullable|string|max:255',
            'email_notifications' => 'nullable|array',
        ]);

        // Update user
        $user->update(['full_name' => $validated['full_name']]);
        
        unset($validated['full_name']);
        
        // Save notification preferences
        if (isset($validated['email_notifications'])) {
            // Ensure we only save checkbox values (1 = true)
            $prefs = [];
            foreach ($validated['email_notifications'] as $key => $value) {
                $prefs[$key] = $value == 1;
            }
            $user->email_notifications = json_encode($prefs);
            $user->save();
            unset($validated['email_notifications']);
        }
        
        // Update profile
        $profile->update($validated);

        // 🔐 Audit Log
        AuditLogger::update('profile', "Updated profile for: {$user->full_name}", $oldData, $profile->toArray());

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 🔐 Audit Log
        AuditLogger::update('profile', "Changed password for: {$user->full_name}");

        // 🔔 Notify user about password change
        NotificationHelper::send(
            $user->id,
            "Password Changed",
            "Your password was changed successfully. If you didn't do this, please contact support immediately.",
            'warning',
            'security',
            'update'
        );

        return back()->with('success', 'Password changed successfully!');
    }
}