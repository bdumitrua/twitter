<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewLikesNotifyConsumer;
use Illuminate\Console\Command;

class NewLikesNotifyConsumerCommand extends Command
{
    protected $topic = 'new_likes';
    protected $consumerGroup = NewLikesNotifyConsumer::class;
    protected $signature = 'kafka:consume:new_likes_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewLikesNotifyConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
