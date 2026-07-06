<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\WelcomeMail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,super_admin');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $users = User::orderBy('created_at', 'desc')->get();
            return response()->json(['success' => true, 'data' => $users]);
        }
        
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        
        // 🔐 Audit Log
        AuditLogger::view('users', 'Viewed users list');
        
        return view('admin.users', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only SuperAdmin can create new users!');
        }
        
        return view('admin.user-form');
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();
        
        if (!$currentUser->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only SuperAdmin can create new users!');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'role' => 'required|in:admin,editor,author,viewer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['role'] === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot create another SuperAdmin! Only one SuperAdmin is allowed.');
        }

        $plainPassword = Str::random(12);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'full_name' => $validated['full_name'],
            'password' => Hash::make($plainPassword),
            'role' => $validated['role'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        Profile::create(['user_id' => $user->id]);

        // 🔐 Audit Log
        AuditLogger::create('users', "Created user: {$user->full_name} ({$user->role})", $user->toArray());

        // 🔔 Notify all admins
        NotificationHelper::sendToAdmins(
            "New User Created",
            "{$user->full_name} has been created as a {$user->role} by " . auth()->user()->full_name,
            'success',
            'users',
            'create'
        );

        // 🔔 Notify the new user
        NotificationHelper::send(
            $user->id,
            "Welcome to Portfolio Builder",
            "Your account has been created! You can login using your email and the password sent to you.",
            'success',
            'auth',
            'create'
        );

        // Send welcome email
        try {
            Mail::to($user->email)->send(new WelcomeMail($user, $plainPassword));
            $message = 'User created successfully! A welcome email with login credentials has been sent.';
        } catch (\Exception $e) {
            $message = 'User created successfully! But welcome email failed to send.';
            \Log::error('Welcome email failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        if ($user->id === $currentUser->id && $currentUser->isSuperAdmin()) {
            return redirect()->route('admin.profile')->with('info', 'Edit your profile from the Profile section!');
        }
        
        if (!$currentUser->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only SuperAdmin can manage users!');
        }
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot edit the SuperAdmin account!');
        }
        
        return view('admin.user-form', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        $oldData = $user->toArray();
        
        if (!$currentUser->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only SuperAdmin can manage users!');
        }
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot update the SuperAdmin account!');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'role' => 'required|in:admin,editor,author,viewer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validated['role'] === 'super_admin') {
            return redirect()->back()->with('error', 'Cannot assign SuperAdmin role! Only one SuperAdmin is allowed.');
        }

        if (!empty($request->password)) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        // 🔐 Audit Log
        AuditLogger::update('users', "Updated user: {$user->full_name} (Role: {$oldData['role']} → {$user->role})", $oldData, $user->toArray());

        // 🔔 Notify the user
        NotificationHelper::send(
            $user->id,
            "Account Updated",
            "Your account details have been updated by " . auth()->user()->full_name,
            'info',
            'users',
            'update'
        );

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        $data = $user->toArray();
        $name = $user->full_name;
        
        if (!$currentUser->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only SuperAdmin can delete users!');
        }
        
        if ($id == $currentUser->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account!');
        }
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot delete the SuperAdmin account!');
        }
        
        if ($user->profile) {
            $user->profile->delete();
        }
        
        $user->delete();

        // 🔐 Audit Log
        AuditLogger::delete('users', "Deleted user: {$name}", $data);

        // 🔔 Notify Admins
        NotificationHelper::sendToAdmins(
            "User Deleted",
            "{$name} was deleted by " . auth()->user()->full_name,
            'danger',
            'users',
            'delete'
        );

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        if (!$currentUser->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Only SuperAdmin can reset passwords!');
        }
        
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Cannot reset SuperAdmin password!');
        }
        
        $newPassword = Str::random(12);
        
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        // 🔐 Audit Log
        AuditLogger::update('users', "Reset password for user: {$user->full_name}");

        // 🔔 Notify the user
        NotificationHelper::send(
            $user->id,
            "Password Reset",
            "Your password was reset by " . auth()->user()->full_name . ". A new password has been sent to your email.",
            'warning',
            'security',
            'reset'
        );

        try {
            Mail::to($user->email)->send(new WelcomeMail($user, $newPassword));
            $message = 'Password reset successfully! New password sent via email.';
        } catch (\Exception $e) {
            $message = 'Password reset but email failed to send.';
        }

        return redirect()->route('admin.users.index')->with('success', $message);
    }
}