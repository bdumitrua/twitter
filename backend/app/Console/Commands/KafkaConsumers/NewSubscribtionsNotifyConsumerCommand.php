<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewSubscribtionsNotifyConsumer;
use Illuminate\Console\Command;

class NewSubscribtionsNotifyConsumerCommand extends Command
{
    protected $topic = 'newSubscribtions';
    protected $consumerGroup = NewSubscribtionsNotifyConsumer::class;
    protected $signature = 'kafka:consume:newSubscribtionNotify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewSubscribtionsNotifyConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
