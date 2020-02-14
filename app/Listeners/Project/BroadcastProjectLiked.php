<?php

namespace App\Listeners\Project;

use App\Http\GraphQL\Subscriptions\ProjectLikedSubscription;
use Nuwave\Lighthouse\Subscriptions\Contracts\BroadcastsSubscriptions;

class BroadcastProjectLiked
{
    /**
     * @var BroadcastsSubscriptions
     */
    protected $broadcaster;

    /**
     * @var BroadcastsSubscriptions
     */
    protected $subscription;

    /**
     * Create the event listener.
     *
     * @param BroadcastsSubscriptions  $broadcaster
     * @param PostUpdatedSubscription $subscription
     */
    public function __construct(
        BroadcastsSubscriptions $broadcaster,
        ProjectLikedSubscription $subscription
    ) {
        $this->broadcaster = $broadcaster;
        $this->subscription = $subscription;
    }

    /**
     * Handle the event.
     *
     * @param ProjectLikedEvent $event
     */
    public function handle(ProjectLikedEvent $event)
    {
        $this->broadcaster->broadcast(
            $this->subscription, // <-- The subscription class you created
            'projectLiked', // <-- Name of the subscription field to broadcast
            $event->like // <-- The root object that will be passed into the subscription resolver
        );
    }
}
