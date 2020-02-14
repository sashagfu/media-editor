<?php

namespace App\Notifications;

use App\Channels\GraphQLChannel;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Nuwave\Lighthouse\Execution\Utils\Subscription;

class ProjectClippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $asset;

    protected $clipper;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Asset $asset, User $clipper)
    {
        $this->asset = $asset;
        $this->clipper = $clipper;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', GraphQLChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type_name' => 'asset',
            'message' => trans(
                'notifications.project_clipped',
                [
                    'user' => $this->clipper->display_name,
                    'project' => $this->asset->project->title,
                    'type' => $this->asset->type,
                ]
            ),
            'asset_id' => $this->asset->id,
        ];
    }

    public function toGraphql($notifiable)
    {
        $notification = DatabaseNotification::findOrFail($this->id);

        Subscription::broadcast('projectClipped', $this->asset);
        Subscription::broadcast('notificationCreated', $notification);
    }
}
