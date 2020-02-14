<?php

namespace App\Http\GraphQL\Subscriptions;

use App\Models\Audio;
use App\Models\Image;
use App\Models\Video;
use Illuminate\Http\Request;
use Nuwave\Lighthouse\Subscriptions\Subscriber;
use Nuwave\Lighthouse\Schema\Types\GraphQLSubscription;

class ProjectFilesUploaded extends GraphQLSubscription
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
        //TODO Make authentication (Jobs auth issue)
        return true;
    }

    /**
     * Filter subscribers who should receive subscription.
     *
     * @param  \Nuwave\Lighthouse\Subscriptions\Subscriber  $subscriber
     * @param  mixed  $root
     * @return bool
     */
    public function filter(Subscriber $subscriber, $projectFiles)
    {
        $args = $subscriber->args;

        $file = $projectFiles->first();
        if ($file instanceof Video) {
            $projectId = $file->videoable_id;
        }
        if ($file instanceof Audio) {
            $projectId = $file->audioable_id;
        }
        if ($file instanceof Image) {
            $projectId = $file->imageable_id;
        }

        logger($projectId == $args['projectId']);
        return $projectId == $args['projectId'];
    }
}
