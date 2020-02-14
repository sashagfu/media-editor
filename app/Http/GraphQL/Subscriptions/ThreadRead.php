<?php

namespace App\Http\GraphQL\Subscriptions;

use Illuminate\Http\Request;
use Nuwave\Lighthouse\Subscriptions\Subscriber;
use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;

class ThreadRead extends GraphQLSubscription
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
        $user = $subscriber->context->user;

        return !!$user->threads->where('id', $subscriber->args['threadId'])->first();
    }

    /**
     * Filter subscribers who should receive subscription.
     *
     * @param  \Nuwave\Lighthouse\Subscriptions\Subscriber  $subscriber
     * @param  mixed  $root
     * @return bool
     */
    public function filter(Subscriber $subscriber, $root)
    {
        $thread = $root;

        $args = $subscriber->args;

        return $thread->users->where('id', $args['userId'])->first() &&
            $thread->reader->id != $args['userId'];
    }
}
