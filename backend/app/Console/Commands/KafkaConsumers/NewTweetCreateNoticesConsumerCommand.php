<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Tweet\Consumers\NewTweetCreateNoticesConsumer;
use Illuminate\Console\Command;

class NewTweetCreateNoticesConsumerCommand extends Command
{
    protected $topic = 'newTweets';
    protected $consumerGroup = NewTweetCreateNoticesConsumer::class;
    protected $signature = 'kafka:consume:newTweetCreateNotices';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewTweetCreateNoticesConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
