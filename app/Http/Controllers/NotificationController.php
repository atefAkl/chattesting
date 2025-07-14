<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class NotificationController extends Controller
{
    /**
     * الحصول على إشعارات المستخدم الحالي
     */
    public function getNotifications()
    {
        $user = Auth::user();
        // استخدام unreadNotifications كمجموعة وليس كدالة
        $notifications = $user->unreadNotifications
            ->where('type', 'App\\Notifications\\NewMessageNotification')
            ->values();
        
        // تحويل الإشعارات إلى تنسيق أبسط
        $formattedNotifications = $notifications->map(function($notification) {
            return [
                'id' => $notification->id,
                'data' => $notification->data,
                'created_at' => $notification->created_at->diffForHumans(),
                'read_at' => $notification->read_at
            ];
        });
        
        return Response::json([
            'status' => 'success',
            'notifications' => $formattedNotifications
        ]);
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        // استخدام المجموعة مباشرة
        $notification = $user->notifications
            ->where('id', $id)
            ->first();
        
        if ($notification) {
            $notification->markAsRead();
            
            // إذا كان الإشعار يتعلق برسالة، قم بتحديث حالة الرسالة أيضًا
            if ($notification->type === 'App\\Notifications\\NewMessageNotification') {
                $data = $notification->data;
                $messageId = $data['message_id'] ?? null;
                
                if ($messageId) {
                    $message = \App\Models\Message::find($messageId);
                    if ($message && $message->receiver == $user->id) {
                        $message->markAsRead();
                    }
                }
            }
            
            return Response::json(['status' => 'success']);
        }
        
        return Response::json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }
    
    /**
     * تحديد كافة الإشعارات كمقروءة
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return Response::json(['status' => 'success']);
    }
}
