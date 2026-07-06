<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Helpers\AuditLogger;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->has('json') && $request->json == 1) {
            $messages = ContactMessage::orderBy('created_at', 'desc')->get();
            return response()->json(['success' => true, 'data' => $messages]);
        }
        
        $messages = ContactMessage::orderBy('created_at', 'desc')->get();
        
        // 🔐 Audit Log
        AuditLogger::view('messages', 'Viewed messages list');
        
        return view('admin.messages', compact('messages'));
    }

    public function markRead($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['is_read' => true]);

        // 🔐 Audit Log
        AuditLogger::update('messages', "Marked message as read from: {$message->sender_name}");

        return response()->json(['success' => true, 'message' => 'Marked as read!']);
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $data = $message->toArray();
        $name = $message->sender_name;
        
        $message->delete();

        // 🔐 Audit Log
        AuditLogger::delete('messages', "Deleted message from: {$name}", $data);

        return response()->json(['success' => true, 'message' => 'Deleted!']);
    }
}