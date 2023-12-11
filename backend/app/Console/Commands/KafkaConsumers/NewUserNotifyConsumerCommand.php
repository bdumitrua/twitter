<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewUserNotifyConsumer;
use Illuminate\Console\Command;

class NewUserNotifyConsumerCommand extends Command
{
    protected $topic = 'newUsers';
    protected $consumerGroup = NewUserNotifyConsumer::class;
    protected $signature = 'kafka:consume:new_user_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting {$this->consumerGroup} consumer...");

            $consumer = app()->make(NewUserNotifyConsumer::class, [
                'topicName' => $this->topic,
                'consumerGroup' => $this->consumerGroup,
            ]);

            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
