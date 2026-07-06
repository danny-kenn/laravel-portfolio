<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationEmail;

class NotificationHelper
{
    /**
     * Send notification to a specific user
     */
    public static function send($userId, $title, $message, $type = 'info', $module = null, $action = null, $relatedId = null, $relatedType = null)
    {
        $user = User::find($userId);
        if (!$user) return;

        // Create notification in database (always saved)
        $notification = Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'module' => $module,
            'action' => $action,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'is_read' => false,
            'is_emailed' => false,
            'created_at' => now(),
        ]);

        // Send email if user has email notifications enabled for this module
        if (self::shouldSendEmail($user, $module, $action)) {
            try {
                Mail::to($user->email)->send(new NotificationEmail($user, $notification));
                $notification->update(['is_emailed' => true]);
            } catch (\Exception $e) {
                \Log::error('Notification email failed: ' . $e->getMessage());
            }
        }

        return $notification;
    }

    /**
     * Send notification to all users with a specific role
     */
    public static function sendToRole($role, $title, $message, $type = 'info', $module = null, $action = null)
    {
        $users = User::where('role', $role)->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $title, $message, $type, $module, $action);
        }
    }

    /**
     * Send notification to all admins and superadmins
     */
    public static function sendToAdmins($title, $message, $type = 'info', $module = null, $action = null)
    {
        $users = User::whereIn('role', ['admin', 'super_admin'])->where('is_active', true)->get();
        foreach ($users as $user) {
            self::send($user->id, $title, $message, $type, $module, $action);
        }
    }

    /**
     * Check if user should receive email notification
     */
    private static function shouldSendEmail($user, $module, $action)
    {
        // SuperAdmin and Admins always get email notifications for important actions
        if (in_array($user->role, ['super_admin', 'admin'])) {
            // Even admins should be able to opt out of non-critical notifications
            if ($user->email_notifications) {
                $settings = json_decode($user->email_notifications, true);
                if ($settings && isset($settings['all_emails']) && $settings['all_emails'] === false) {
                    return false;
                }
            }
            return true;
        }
        
        // Check user's preferences
        if (!$user->email_notifications) {
            return false;
        }

        $settings = json_decode($user->email_notifications, true);
        
        // If no settings, default to false for non-admin users
        if (!$settings) {
            return false;
        }

        // Check if user has enabled notifications for this module
        $moduleKey = $module ? str_replace(' ', '_', $module) : 'general';
        
        // If they have a specific setting for this module
        if (isset($settings[$moduleKey])) {
            return $settings[$moduleKey] === true || $settings[$moduleKey] == 1;
        }
        
        return false;
    }

    /**
     * Get unread count for a user
     */
    public static function unreadCount($userId)
    {
        return Notification::where('user_id', $userId)->where('is_read', false)->count();
    }

    /**
     * Get all unread notifications for a user
     */
    public static function getUnread($userId, $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark a notification as read
     */
    public static function markRead($notificationId, $userId)
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();
        if ($notification) {
            $notification->update(['is_read' => true]);
            return true;
        }
        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllRead($userId)
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}