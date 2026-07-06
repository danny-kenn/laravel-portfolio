<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    public static function log($action, $module, $description = null, $oldData = null, $newData = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            // If no user logged in, log as system
            $user = (object) ['id' => 0, 'full_name' => 'System', 'role' => 'system'];
        }

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->full_name ?? 'System',
            'user_role' => $user->role ?? 'system',
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }

    // Convenience methods
    public static function login($user)
    {
        self::log('login', 'auth', "User {$user->full_name} logged in");
    }

    public static function logout($user)
    {
        self::log('logout', 'auth', "User {$user->full_name} logged out");
    }

    public static function create($module, $description, $data = null)
    {
        self::log('create', $module, $description, null, $data);
    }

    public static function update($module, $description, $oldData = null, $newData = null)
    {
        self::log('update', $module, $description, $oldData, $newData);
    }

    public static function delete($module, $description, $data = null)
    {
        self::log('delete', $module, $description, $data, null);
    }

    public static function view($module, $description = null)
    {
        self::log('view', $module, $description);
    }
}