<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewTweetNotifyConsumer;
use Illuminate\Console\Command;

class NewTweetNotifyConsumerCommand extends Command
{
    protected $topic = 'new_tweets';
    protected $signature = 'kafka:consume:new_tweet_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . NewTweetNotifyConsumer::class . "...");

            $consumer = new NewTweetNotifyConsumer($this->topic, NewTweetNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
