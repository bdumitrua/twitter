<?php

namespace App\Console\Commands\KafkaConsumers;

use App\Modules\Notification\Consumers\NewSubscribtionsNotifyConsumer;
use Illuminate\Console\Command;

class NewSubscribtionsNotifyConsumerCommand extends Command
{
    protected $topic = 'new_subscribtions';
    protected $signature = 'kafka:consume:new_subscribtion_notify';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        try {
            $this->info("Starting " . NewSubscribtionsNotifyConsumer::class . "...");

            $consumer = new NewSubscribtionsNotifyConsumer($this->topic, NewSubscribtionsNotifyConsumer::class);
            $consumer->consume();
        } catch (\LogicException $e) {
            $this->error($e->getMessage());
        }
    }
}
