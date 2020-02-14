<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Helpers\AuthHelper;

class NotificationsController extends Controller
{
    public function getUserNotifications()
    {
        $user = AuthHelper::me();
        $notifications =  $user->notifications()->paginate(10);

        return $notifications;
    }

    public function readNotifications(Request $request)
    {
        $notification_id = $request->notification_id;
        $user = AuthHelper::me();

        $notification = $user->notifications()->where('id', $notification_id)->first();

        if ($notification) {
            $notification->markAsRead();
        }
        return response()->json($user->unreadNotifications()->count());
    }

    public function readAllNotifications()
    {
        $user = AuthHelper::me();

        foreach ($user->unreadNotifications as $notification) {
            $notification->markAsRead();
        }
        return response()->json($user->unreadNotifications()->count());
    }
}
