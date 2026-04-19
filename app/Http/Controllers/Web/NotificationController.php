<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        return view('notifications.index');
    }

    public function getNotifications(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $filter = $request->input('filter', 'all'); // all, unread, read
        $limit = $request->input('limit', 20);

        $query = Notification::where('user_id', $userId)
            ->orderByDesc('created_at');

        if ($filter === 'unread') {
            $query->unread();
        } elseif ($filter === 'read') {
            $query->read();
        }

        $notifications = $query->limit($limit)->get();
        $unreadCount = Notification::where('user_id', $userId)->unread()->count();
        $totalCount = Notification::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'total_count' => $totalCount,
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        $unreadCount = Notification::where('user_id', $userId)->unread()->count();

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Notification::where('user_id', $userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'unread_count' => 0,
        ]);
    }

    public function delete(Request $request, $id)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = Notification::where('user_id', $userId)
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->delete();

        $unreadCount = Notification::where('user_id', $userId)->unread()->count();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted',
            'unread_count' => $unreadCount,
        ]);
    }

    public function deleteAll(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $deletedCount = Notification::where('user_id', $userId)->count();
        Notification::where('user_id', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => "All {$deletedCount} notifications deleted",
            'unread_count' => 0,
            'deleted_count' => $deletedCount,
        ]);
    }

    public function getPreferences(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $preferences = NotificationPreference::firstOrCreate(
            ['user_id' => $userId],
            [
                'budget_alerts' => true,
                'goal_updates' => true,
                'group_activities' => true,
                'transaction_alerts' => true,
                'bill_reminders' => true,
                'feature_updates' => true,
                'budget_threshold_percentage' => 80,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'budget_alerts' => 'boolean',
            'goal_updates' => 'boolean',
            'group_activities' => 'boolean',
            'transaction_alerts' => 'boolean',
            'bill_reminders' => 'boolean',
            'feature_updates' => 'boolean',
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'budget_threshold_percentage' => 'integer|min:1|max:100',
        ]);

        $preferences = NotificationPreference::updateOrCreate(
            ['user_id' => $userId],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully',
            'data' => $preferences,
        ]);
    }

    public function getUnreadCount(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $unreadCount = Notification::where('user_id', $userId)->unread()->count();

        return response()->json([
            'success' => true,
            'count' => $unreadCount,
            'unread_count' => $unreadCount,
        ]);
    }
}
