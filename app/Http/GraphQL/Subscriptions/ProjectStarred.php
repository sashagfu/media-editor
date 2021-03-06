<?php

namespace App\Http\GraphQL\Subscriptions;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Star;
use Illuminate\Http\Request;
use Nuwave\Lighthouse\Subscriptions\Subscriber;
use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;

class ProjectStarred extends GraphQLSubscription
{
    /**
     * Check if subscriber is allowed to listen to the subscription.
     *
     * @param  \Nuwave\Lighthouse\Subscriptions\Subscriber  $subscriber
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Subscriber $subscriber, Request $request)
    {
        return !!$subscriber->context->user;
    }

    /**
     * Filter subscribers who should receive subscription.
     *
     * @param  \Nuwave\Lighthouse\Subscriptions\Subscriber  $subscriber
     * @param  mixed  $root
     * @return bool
     */
    public function filter(Subscriber $subscriber, $star)
    {
        $args = $subscriber->args;

        return $args['projectId'] == $star->starable->id;
    }
}
