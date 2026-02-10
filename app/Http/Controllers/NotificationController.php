<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Return JSON list of notifications for the logged-in participant.
     */
    public function index(Request $request)
    {
        $participantId = session('user_id');
        $userType = session('user_type');

        if ($userType !== 'participant' || !$participantId) {
            return response()->json([], 403);
        }

        $notifications = DB::table('notification')
            ->where('participant_id', $participantId)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $unreadCount = DB::table('notification')
            ->where('participant_id', $participantId)
            ->where('status', 'unread')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead($notification_id)
    {
        $participantId = session('user_id');

        DB::table('notification')
            ->where('notification_id', $notification_id)
            ->where('participant_id', $participantId)
            ->update(['status' => 'read', 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $participantId = session('user_id');

        DB::table('notification')
            ->where('participant_id', $participantId)
            ->where('status', 'unread')
            ->update(['status' => 'read', 'updated_at' => now()]);

        return response()->json(['success' => true]);
    }
}
