<?php

namespace App\Http\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class NotificationMutator
{
    public function markNotificationsRead($rootValue, array $args)
    {
        $user = Auth::user();
        $notifications = collect();

        foreach ($args['ids'] as $id) {
            $notification = DatabaseNotification::findOrFail($id);

            if ($notification->notifiable_id != $user->id) {
                throw new \Exception(trans('notifications.wrong_access_rights'));
            }

            $notification->markAsRead();
            $notifications->push($notification);
        }

        return $notifications;
    }

    public function deleteNotifications()
    {
        $user = Auth::user();

        $user->notifications()->delete();

        return [
            'message' => 'Success',
            'statusCode' => 204,
        ];
    }
}
