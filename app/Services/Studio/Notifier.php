<?php

namespace App\Services\Studio;

use Aws\Laravel\AwsFacade;

class Notifier
{

    /** @var \Aws\Sns\SnsClient $sns_client */
    protected $sns_client;

    public function __construct()
    {

        $this->sns_client = AwsFacade::createClient('Sns');

        $this->sns_client->subscribe([
            'TopicArn' => config('aws.sns.topics.completed'),
            'Protocol' => 'https',
            'Endpoint' => route('sns.completed')
        ]);
    }
}
