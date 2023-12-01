<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewTweetNotifyConsumer;
use Illuminate\Console\Command;

class NewTweetNotifyConsumerCommand extends Command
{
    protected $topic = 'new_tweets';
    protected $consumerGroup = NewTweetNotifyConsumer::class;
    protected $signature = 'kafka:consume:new_tweet_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewTweetNotifyConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
