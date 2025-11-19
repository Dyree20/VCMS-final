<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Clamping;
use App\Models\Payee;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $this->authorize('view', $notification);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->notifications()->unread()->count();
        return response()->json(['count' => $count]);
    }

    public function getRecent()
    {
        $notifications = auth()->user()->notifications()
            ->latest()
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }

    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $notifications = [];

        if (!$user) {
            return response()->json([
                'notifications' => [],
                'count' => 0
            ]);
        }

        $roleName = strtolower($user->role->name ?? '');
        $userId = $user->id;

        // Get notifications from database
        $dbNotifications = Notification::where('user_id', $userId)
            ->latest()
            ->limit(10)
            ->get();

        // If no database notifications, generate legacy notifications
        if ($dbNotifications->isEmpty()) {
            switch ($roleName) {
                case 'admin':
                    $notifications = $this->getAdminNotifications();
                    break;

                case 'enforcer':
                    $notifications = $this->getEnforcerNotifications($userId);
                    break;

                case 'front desk':
                    $notifications = $this->getFrontDeskNotifications();
                    break;

                default:
                    $notifications = [];
            }
        } else {
            $notifications = $dbNotifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'time' => $n->created_at->diffForHumans(),
                    'timestamp' => $n->created_at->timestamp,
                    'is_read' => $n->is_read,
                ];
            })->toArray();
        }

        return response()->json([
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    /**
     * Admin notifications: All activities and system-wide events
     */
    private function getAdminNotifications()
    {
        // Recent activity logs (last 30 minutes)
        $recentLogs = ActivityLog::with('user')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // New pending clampings
        $newClampings = Clamping::where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $notifications = [];

        // Add activity log notifications
        foreach ($recentLogs as $log) {
            $userInfo = $log->user ? ($log->user->f_name . ' ' . $log->user->l_name) : ($log->username ?? 'Unknown');
            $clamping = $log->clamping_id ? Clamping::find($log->clamping_id) : null;
            
            $notifications[] = [
                'id' => 'log_' . $log->id,
                'type' => 'activity',
                'title' => ucfirst($log->action) . ' Action',
                'message' => $userInfo . ' ' . strtolower($log->action) . ($clamping ? ' ticket ' . $clamping->ticket_no : ''),
                'time' => $log->created_at->diffForHumans(),
                'timestamp' => $log->created_at->timestamp,
                'action' => $log->action,
                'user' => $userInfo,
                'clamping_id' => $log->clamping_id,
            ];
        }

        // Add new clamping notifications
        foreach ($newClampings as $clamping) {
            $enforcerName = $clamping->user ? ($clamping->user->f_name . ' ' . $clamping->user->l_name) : 'Unknown';
            
            $notifications[] = [
                'id' => 'clamping_' . $clamping->id,
                'type' => 'new_clamping',
                'title' => 'New Clamping',
                'message' => $enforcerName . ' created new clamping ' . $clamping->ticket_no,
                'time' => $clamping->created_at->diffForHumans(),
                'timestamp' => $clamping->created_at->timestamp,
                'action' => 'created',
                'user' => $enforcerName,
                'clamping_id' => $clamping->id,
            ];
        }

        // Sort by timestamp (most recent first)
        usort($notifications, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return array_slice($notifications, 0, 10);
    }

    /**
     * Enforcer notifications: Only their own clampings and status changes
     */
    private function getEnforcerNotifications($userId)
    {
        $notifications = [];

        // Get enforcer's clampings with recent status changes
        $myClampings = Clamping::where('user_id', $userId)
            ->where('status', '!=', 'released')
            ->where('updated_at', '>=', now()->subHours(24))
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get activity logs for enforcer's clampings
        $clampingIds = $myClampings->pluck('id');
        $recentActivities = ActivityLog::whereIn('clamping_id', $clampingIds)
            ->where('created_at', '>=', now()->subHours(24))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        // Add status change notifications
        foreach ($myClampings as $clamping) {
            $statusMessages = [
                'paid' => 'Your clamping ' . $clamping->ticket_no . ' has been paid',
                'approved' => 'Your clamping ' . $clamping->ticket_no . ' has been approved',
                'accepted' => 'Your clamping ' . $clamping->ticket_no . ' has been accepted',
                'rejected' => 'Your clamping ' . $clamping->ticket_no . ' has been rejected',
            ];

            if (isset($statusMessages[$clamping->status])) {
                $notifications[] = [
                    'id' => 'clamping_' . $clamping->id,
                    'type' => 'status_change',
                    'title' => 'Status Update',
                    'message' => $statusMessages[$clamping->status],
                    'time' => $clamping->updated_at->diffForHumans(),
                    'timestamp' => $clamping->updated_at->timestamp,
                    'action' => $clamping->status,
                    'clamping_id' => $clamping->id,
                ];
            }
        }

        // Add activity notifications
        foreach ($recentActivities as $log) {
            if ($log->user_id != $userId) { // Only show actions by others
                $actorName = $log->user ? ($log->user->f_name . ' ' . $log->user->l_name) : ($log->username ?? 'Unknown');
                $clamping = Clamping::find($log->clamping_id);
                
                $notifications[] = [
                    'id' => 'log_' . $log->id,
                    'type' => 'activity',
                    'title' => ucfirst($log->action) . ' by ' . $actorName,
                    'message' => $actorName . ' ' . strtolower($log->action) . ' your clamping ' . ($clamping ? $clamping->ticket_no : ''),
                    'time' => $log->created_at->diffForHumans(),
                    'timestamp' => $log->created_at->timestamp,
                    'action' => $log->action,
                    'user' => $actorName,
                    'clamping_id' => $log->clamping_id,
                ];
            }
        }

        // Sort by timestamp (most recent first)
        usort($notifications, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return array_slice($notifications, 0, 10);
    }

    /**
     * Front Desk notifications: Payments and new clampings
     */
    private function getFrontDeskNotifications()
    {
        $notifications = [];

        // New payments (last 30 minutes)
        $recentPayments = Payee::where('created_at', '>=', now()->subMinutes(30))
            ->with('clamping')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // New pending clampings (last 30 minutes)
        $newClampings = Clamping::where('status', 'pending')
            ->where('created_at', '>=', now()->subMinutes(30))
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Payment notifications
        foreach ($recentPayments as $payment) {
            $clamping = $payment->clamping;
            if ($clamping) {
                $notifications[] = [
                    'id' => 'payment_' . $payment->id,
                    'type' => 'payment',
                    'title' => 'Payment Received',
                    'message' => 'Payment of ₱' . number_format($payment->amount_paid, 2) . ' for ticket ' . $clamping->ticket_no,
                    'time' => $payment->created_at->diffForHumans(),
                    'timestamp' => $payment->created_at->timestamp,
                    'action' => 'payment',
                    'clamping_id' => $clamping->id,
                ];
            }
        }

        // New clamping notifications
        foreach ($newClampings as $clamping) {
            $enforcerName = $clamping->user ? ($clamping->user->f_name . ' ' . $clamping->user->l_name) : 'Unknown';
            
            $notifications[] = [
                'id' => 'clamping_' . $clamping->id,
                'type' => 'new_clamping',
                'title' => 'New Clamping',
                'message' => 'New clamping ' . $clamping->ticket_no . ' by ' . $enforcerName,
                'time' => $clamping->created_at->diffForHumans(),
                'timestamp' => $clamping->created_at->timestamp,
                'action' => 'created',
                'user' => $enforcerName,
                'clamping_id' => $clamping->id,
            ];
        }

        // Sort by timestamp (most recent first)
        usort($notifications, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return array_slice($notifications, 0, 10);
    }
}
