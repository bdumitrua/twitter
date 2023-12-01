<?php

namespace App\Modules\User\Listeners;

use App\Kafka\KafkaProducer;
use App\Modules\Tweet\Models\Tweet;
use App\Modules\User\Models\User;
use App\Modules\User\Models\UserSubscribtion;
use App\Modules\User\Repositories\UserRepository;

class NewTweetsListener
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }
    public function handle($event)
    {
        /** @var Tweet */
        $tweet = $event->tweet;
        new KafkaProducer('new_tweets', $tweet->toArray());
    }
}
