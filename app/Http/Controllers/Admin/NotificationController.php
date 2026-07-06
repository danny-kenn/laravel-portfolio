<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Notification::where('user_id', auth()->id());

        if ($request->has('json') && $request->json == 1) {
            $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'meta' => [
                    'total' => $notifications->total(),
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                ]
            ]);
        }

        return view('admin.notifications');
    }

    public function markRead($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', auth()->id())->first();
        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true, 'message' => 'Marked as read!']);
        }
        return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true, 'message' => 'All notifications marked as read!']);
    }
}